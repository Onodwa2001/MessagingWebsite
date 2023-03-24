<?php 

    session_start();

    include('./connect.php');

    $error = '';

    /**
     * Checks if user exists in the database based on input values
     * 
     * @param string $username
     * @param string $password
     * 
     * @return array associative array with 1 value which is the user that logged in
     * 
     */
    function check_exist_in_db($username, $password) {
        global $connection, $error;

        // encryption module
        include('./encryption.php');
        $encrypted_input = encrypt($password);

        // prevent SQL injection by sanitizing input values
        $safe_username = mysqli_real_escape_string($connection, $username);
        $safe_password = mysqli_real_escape_string($connection, $encrypted_input);

        $sql = "SELECT * FROM users WHERE username='$safe_username' AND password='$safe_password'";
        $result = mysqli_query($connection, $sql);

        try {
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    return array('username' => $row["username"], 'name' => $row['name']);
                }
            }
        } catch (Exception $e) {
            // $error = '<p style="color: red; text-align: center;">Incorrect details entered</p>';  
            echo 'Err: ';  
        }

        return null;

    }


    /**
     * logs a user in
     * 
     * @param string $username
     * 
     * @return void
     * 
     */
    function login_user($username) {
        global $error;

        if (isset($_POST['username'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $error = '<p style="color: red; text-align: center;">Please enter missing details</p>';
            } else {
                // returns credentials of user
                $creds = check_exist_in_db($username, $password);

                if (!$creds) {
                    $error = '<p style="color: red; text-align: center;">This user does not exist</p>';
                } else {
                    $_SESSION['username'] = $creds['username'];
                    $_SESSION['name'] = $creds['name'];

                    header('Location: friends.php');
                }
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
    
    <?php echo $error; ?>

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