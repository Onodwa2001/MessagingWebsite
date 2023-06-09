<?php 
    session_start();

    include('./connect.php');

    require 'C:/xampp/php/vendor/autoload.php';

    // encryption module
    include('./encryption.php');

    $receiver = '';

    function getAllUnreadMessages() {
        global $connection, $unread_messages;

        $current_logged_in_user = $_SESSION['username'];

        $sql = "SELECT * FROM unread_messages WHERE receiver_id='$current_logged_in_user'";
        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            $unread_messages = array();

            while($row = mysqli_fetch_assoc($result)) {
                array_push($unread_messages, array('id' => $row['id'], 'message' => $row["message"], 'sender_id' => $row['sender_id'], 'receiver_id' => $row['receiver_id']));
            }

            return $unread_messages;
        } 
        return null;
    }

    $receiver_records = array();
    if (isset($_GET['id'])) {
        $receiver = $_GET['id']; // this value will vary depending on who the sender clicks on

        $unread_messages = getAllUnreadMessages();

        if ($unread_messages != null) {
            foreach ($unread_messages as $message) {
                if ($_SESSION['username'] === $message['receiver_id'] && $_GET['id'] === $message['sender_id']) {
                    // delete this message from unread_messages database
                    $id = $message['id'];

                    $sqlquery = "DELETE FROM unread_messages WHERE id = '$id'";

                    global $connection;

                    if (mysqli_query($connection, $sqlquery)) {
                        // successfully deleted
                    } else {
                        echo "Error: " . $sqlquery . "<br>" . mysqli_error($connection);
                    }
                }
            }
        }

        function getRecordsOfReceiver($receiver) {
            global $connection;
    
            $sql = "SELECT * FROM users WHERE username = '$receiver'";
    
            $result = mysqli_query($connection, $sql);
            $data = mysqli_fetch_array($result);
    
            return array('image' => $data['image'], 'username' => $data['username'], 'name' => $data['name'], 'city' => $data['city']);
        }
    
        $receiver_records = getRecordsOfReceiver($receiver);
        // echo $receiver_records['username'];

    }

    /**
     * adding the records to the database
     * 
     * @param string $sender -> sender username
     * @param string $receiver -> receiver username
     * @param string $message -> message being sent
     * 
     * @return void
     * 
     */
    function addMessageToConversationTable($sender, $receiver, $message) {
        // Convert special characters to HTML entities so it can be successfully added to database
        $message = htmlspecialchars($message);
        $sender = htmlspecialchars($sender);
        $receiver = htmlspecialchars($receiver);

        $hashed_message = encrypt($message);

        $sqlquery = "INSERT INTO conversation(message, sender_id, receiver_id) VALUES ('$hashed_message', '$sender', '$receiver')";

        global $connection;

        if (mysqli_query($connection, $sqlquery)) {
            // success - might add something here later
        } else {
            echo "Error: " . $sqlquery . "<br>" . mysqli_error($connection);
        }
    }

    function addMessageToUnreadMessagesTable($sender, $receiver, $message) {
        // Convert special characters to HTML entities so it can be successfully added to database
        $message = htmlspecialchars($message);
        $sender = htmlspecialchars($sender);
        $receiver = htmlspecialchars($receiver);

        $hashed_message = encrypt($message);

        $sqlquery = "INSERT INTO unread_messages(message, sender_id, receiver_id) VALUES ('$hashed_message', '$sender', '$receiver')";

        global $connection;

        if (mysqli_query($connection, $sqlquery)) {
            // success - might add something here later
        } else {
            echo "Error: " . $sqlquery . "<br>" . mysqli_error($connection);
        }
    }

    /**
     * getting the arguments and passing them to the addMessageToConverstaions and addMessageToUnreadMessageTable functions 
     * under the condition that the message has be written and submitted
     * 
     * @return void
     * 
     */
    function sendMessage() {
        $sender = $_SESSION['username'];
        global $receiver;

        if (isset($_POST['message'])) {
            $message = $_POST['message'];
            addMessageToConversationTable($sender, $receiver, $message);
            addMessageToUnreadMessagesTable($sender, $receiver, $message);
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
                array_push($messages, array('sender_id' => $row["sender_id"], 'receiver_id' => $row['receiver_id'], 'message' => decrypt(($row["message"])), 'timestamp' => $row['time']));
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

        body {

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

        .chat-space::-webkit-scrollbar {
            display: none;
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

        .chat_space {
            height: 500px;
            overflow-y: scroll;
            /* transform: scaleY(-1);  */
            display: flex;
            flex-direction: column-reverse;
        }

        .topbar {
            display: flex;
            background-color: #3b71ca;
            padding: 10px;
            color: white;
            border-radius: 8px;
        }

        .topbar h3 {
            margin-top: 15px;
            margin-left: 10px;
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

        .image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .image img {
            object-fit: cover;
            border-radius: 50%;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body onload="refreshChat();">
    

    <p style="text-align: center; background-color: #3b71ca; width: fit-content; margin: 10px auto 10px auto; padding: 10px; border-radius: 8px;"><a href="friends.php" style="color: white; text-decoration: none">Back</a></p>
    <div class="chat-wrap">
        <a href="details.php?id=<?php echo $receiver_records['username']; ?>">
            <div class="topbar">
                <div class="image">
                    <img src="uploads/<?php echo $receiver_records['image'] === '' ? '6522516.png' : $receiver_records['image']; ?>" alt="profile picture" height="50px" width="50px" />
                </div>
                <h3><?php echo $receiver; ?></h3>
            </div>
        </a>
        
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
        }, 1000);


    </script>
</body>
</html>