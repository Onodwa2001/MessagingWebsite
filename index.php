<?php 

    session_start();

    include('./connect.php');

    function check_exist_in_db($username, $password) {
        global $connection;

        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
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
            $password = $_POST['password'];

            // returns credentials of user
            $creds = check_exist_in_db($username, $password);

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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style>
        .form-wrapper {
            width: 500px;
            margin: 0 auto 0 auto;
            background-color: rgb(244, 241, 241);
            padding: 40px;
        }

        h1 {
            text-align: center;
            font-size: 28px;
            margin-top: 80px;
        }
    </style>
</head>
<body>

    <h1>Sign in to your account</h1>
    <div class="form-wrapper">
        <form action="" method="POST">
            <div class="form-group">
                <label for="exampleInputEmail1">Username</label>
                <input type="text" class="form-control" id="exampleInputEmail1" name="username" aria-describedby="emailHelp" placeholder="Enter username">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password">
            </div>
            <a href="register.php">Don't have an account?</a><br/><br/>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    
</body>
</html>