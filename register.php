<?php 

    include('./connect.php');

    $error = '';

    /**
     * @param string $username -> primary key of user
     * @param string $name -> optional name
     * 
     * @return error
     */
    function add_user_to_database($username, $name, $password) {

        // encryption module
        include('./encryption.php');
        $encrypted_password = encrypt($password);

        $sqlquery = "INSERT INTO users(username, name, password) VALUES ('$username', '$name', '$encrypted_password')";
 
        global $connection;

        try {
            if (mysqli_query($connection, $sqlquery)) {
                return '<p style="color: green; text-align: center;">Account created successfully <a href="index.php">Log in</a></p>';
            } else {
                return "This username is taken";
            }
        } catch (Exception $e) {
            echo 'error: ' . mysqli_error($connection);

            return '<p style="color: red; text-align: center;">This username is taken</p>';
        }
        
    }

    /**
     * @param string $username -> primary key of user
     * @param string $name -> optional name
     * 
     * @return void
     */
    function register_user($username, $name) {
        if (isset($_POST['username']) && isset($_POST['name']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $name = $_POST['name'];
            $password = $_POST['password'];

            global $error;

            if (empty($username) || empty($name) || empty($password)) {
                $error = '<p style="color: red; text-align: center;">Please enter missing value</a>';
            } else {
                $error = add_user_to_database($username, $name, $password);
            }
        }
    }

    $username = '';
    $name = '';

    register_user($username, $name);

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

    <h1>Create a new account</h1>
    <?php echo $error; ?>
    <div class="form-wrapper">
        <form action="" method="POST">
            <div class="form-group">
                <label for="exampleInputEmail1">Username</label>
                <input type="text" class="form-control" id="exampleInputEmail1" name="username" aria-describedby="emailHelp" placeholder="Enter username">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Name</label>
                <input type="text" class="form-control" id="exampleInputEmail1" name="name" aria-describedby="emailHelp" placeholder="Enter name">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password">
            </div>
            <a href="index.php">Already have an account?</a><br/><br/>
            <button type="submit" class="btn btn-primary">Sign Up</button>
        </form>
    </div>
    
    
</body>
</html>