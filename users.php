<?php 

    session_start();

    include('connect.php');

    function getAllUsers() {
        global $connection;

        $sql = "SELECT * FROM users";
        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            $users = array();

            while($row = mysqli_fetch_assoc($result)) {
                array_push($users, array('username' => $row["username"], 'name' => $row['name']));
            }

            return $users;
        } else {
            echo "No users yet, start a conversation";
        }
        return null;
    }

    $users = getAllUsers();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <div class="users" >
        <?php foreach($users as $user) { ?>
            <a href="chatroom.php?id=<?php echo $user['username'];?>">
                <p><?php echo $user['name']; ?></p>
            </a>
        <?php } ?>
    </div>

</body>
</html>