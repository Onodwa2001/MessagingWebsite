<?php 
    session_start();

    include('./connect.php');

    $receiver = '';

    if (isset($_GET['id'])) $receiver = $_GET['id']; // this value will vary depending on who the sender clicks on

    /**
     * $sender -> sender username
     * $receiver -> receiver username
     * $message -> message being sent
     * 
     * adding the records to the database
     */
    function addMessageToDatabase($sender, $receiver, $message) {
        $sqlquery = "INSERT INTO conversation(message, sender_id, receiver_id) VALUES ('$message', '$sender', '$receiver')";

        global $connection;

        if (!mysqli_query($connection, $sqlquery)) {
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
                array_push($messages, array('sender_id' => $row["sender_id"], 'receiver_id' => $row['receiver_id'], 'message' => $row["message"], 'timestamp' => $row['time']));
            }

            return $messages;
        } else {
            echo "No messages yet, start a conversation";
        }
        return null;
    }

    $placeholder = '';
    $messages = getAllMessages();

?>

<div class="messages">
    <h3><?php echo $receiver; ?></h3>

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
                <h4><?php echo $message['message']; ?></h4>
                <h5><?php echo $message['timestamp']; ?></h5>
            </div>
        <?php } ?>
    <?php endforeach; ?>
</div>

