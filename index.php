<?php 

    session_start();

    include('./connect.php');

    function check_exist_in_db($username) {
        global $connection;

        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                return array('username' => $row["username"], 'name' => $row['name']);
            }
        } else {
            echo "Error: ";
        }
        return null;
    }

    function login_user($username) {
        if (isset($_POST['username'])) {
            $username = $_POST['username'];

            // returns credentials of user
            $creds = check_exist_in_db($username);

            if (!$creds) {
                echo 'This user does not exist';
            } else {
                $_SESSION['username'] = $creds['username'];
                $_SESSION['name'] = $creds['name'];

                header('Location: users.php');
            }
            
        }
    }

    $username = '';

    login_user($username);

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

    <form action="" method="POST">
        <label for="username">Enter username</label><br>
        <input type="text" name="username" id="username">
        <button>Login</button>
    </form>
    
</body>
</html>