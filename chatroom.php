<?php 
    session_start();

    include('./connect.php');

    require 'C:/xampp/php/vendor/autoload.php';

    $receiver = '';

    if (isset($_GET['id'])) $receiver = $_GET['id']; // this value will vary depending on who the sender clicks on

    class Chat {
        
    }

    function encryptMessage($text) {
        $sSalt = '20adeb83e85f03cfc84d0fb7e5f4d290';
        $sSalt = substr(hash('sha256', $sSalt, true), 0, 32);
        $method = 'aes-256-cbc';
    
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    
        $encrypted = base64_encode(openssl_encrypt($text, $method, $sSalt, OPENSSL_RAW_DATA, $iv));
        return $encrypted;
    }

    function decryptMessage($text) {
        $sSalt = '20adeb83e85f03cfc84d0fb7e5f4d290';
        $sSalt = substr(hash('sha256', $sSalt, true), 0, 32);
        $method = 'aes-256-cbc';
    
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    
        $decrypted = openssl_decrypt(base64_decode($text), $method, $sSalt, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

    /**
     * $sender -> sender username
     * $receiver -> receiver username
     * $message -> message being sent
     * 
     * adding the records to the database
     */
    function addMessageToDatabase($sender, $receiver, $message) {
        // Convert special characters to HTML entities so it can be successfully added to database
        $message = htmlspecialchars($message);
        $sender = htmlspecialchars($sender);
        $receiver = htmlspecialchars($receiver);

        $hashed_message = encryptMessage($message);

        $sqlquery = "INSERT INTO conversation(message, sender_id, receiver_id) VALUES ('$hashed_message', '$sender', '$receiver')";

        global $connection;

        if (mysqli_query($connection, $sqlquery)) {
            // success - might add something here later
        } else {
            echo "Error: " . $sqlquery . "<br>" . mysqli_error($connection);
        }
    }

    /**
     * getting the arguments and passing them to the addMessageToDatabase function 
     * under the condition that the message has be written and submitted
     */
    function sendMessage() {
        $sender = $_SESSION['username'];
        global $receiver;

        if (isset($_POST['message'])) {
            $message = $_POST['message'];
            addMessageToDatabase($sender, $receiver, $message);
        }
    }


    // call the sendMessage function if someone is logged in
    if (isset($_SESSION['username'])) {
        sendMessage();
    }

    function getAllMessages() {
        global $connection;

        $sql = "SELECT * FROM conversation";
        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            $messages = array();

            while($row = mysqli_fetch_assoc($result)) {
                array_push($messages, array('sender_id' => $row["sender_id"], 'receiver_id' => $row['receiver_id'], 'message' => decryptMessage(($row["message"])), 'timestamp' => $row['time']));
            }

            return $messages;
        } 
        return null;
    }

    $placeholder = '';
    $messages = getAllMessages();

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        * {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        .message {
            background-color: rgb(246, 239, 239);
            max-width: 60%;
            width: fit-content;
            padding: 10px 10px 10px 10px;
            margin-top: 10px;
            border-radius: 10px;
            /* color: white; */
        }

        .message p {
            font-size: 14px;
        }

        .messages {
            padding: 20px;
        }

        .Them {
            background-color: rgb(246, 239, 239);
            color: black;
        }

        .Me {
            color: white;
            background-color: #3b71ca;
            margin-left: auto;
            margin-right: right;
        }

        .chat-wrap {
            width: 600px;
            margin: auto auto;
        }

        .chat-wrap h3 {
            background-color: rgb(187, 96, 114);
            color: white;
            padding: 10px;
        }

        .chat_space {
            height: 500px;
            overflow-y: scroll;
            /* transform: scaleY(-1);  */
            display: flex;
            flex-direction: column-reverse;
        }

        #messageFrom {
            width: 100%;
        }

        #messageFrom input {
            width: 80%;
            padding: 10px;
            border-radius: 200px;
            border: solid grey 1px;
        }

        #messageFrom button {
            width: 15%;
            padding: 10px;
            border-radius: 200px;
            border: none;
        }

        #messageFrom button:hover {
            cursor: pointer;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body onload="refreshChat();">
    

    <div class="chat-wrap">
        <h3><?php echo $receiver; ?></h3>
        <div id="chat_space" class="chat_space">
            <div class="messages" id="messages">

                <?php if ($messages > 0) { ?>
                    <?php foreach($messages as $message) : ?>
                        <?php if (isset($_SESSION['username']) && $_SESSION['username'] === $message['sender_id'] && $receiver === $message['receiver_id'] || $receiver === $message['sender_id'] && $_SESSION['username'] === $message['receiver_id']) { ?>
                            <div class="message <?php 
                                    if ($_SESSION['username'] === $message['sender_id']) {
                                        $placeholder = 'Me';
                                    } else {
                                        $placeholder = 'Them'; 
                                    }

                                    echo $placeholder;
                                ?>">
                                <p><?php echo $message['message']; ?></p>
                                <h5 style="font-weight: lighter; font-size: 10px; margin-top: 10px;"><?php echo date('d M Y, H.i.s A', strtotime($message['timestamp'])); ?></h5>
                            </div>
                        <?php } ?>
                    <?php endforeach; ?>
                <?php } else { ?>
                    <h3 style="font-weight: lighter; text-align: center;">No messages yet, start a conversation</h3>
                <?php } ?>

            </div>
        </div>
        <form action="" method="POST" id="messageFrom">
            <input type="text" name="message" id="message">
            <button>Send</button>
        </form>
    </div>


    <script type="text/javascript">

        const element = document.getElementById('chat_space');

        element.addEventListener('scroll', () => {
            console.log('Scroll is triggered');
        });
        
        function refreshChat(){
            $('#messages').load(location.href + " #messages");
        }

        setInterval(function(){
            refreshChat();
        }, 2000);


    </script>
</body>
</html>