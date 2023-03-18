<?php 

    include('./connect.php');

    /**
     * $username -> primary key of user
     * $name -> optional name
     */
    function add_user_to_database($username, $name) {
        $sqlquery = "INSERT INTO users VALUES ('$username', '$name')";
 
        global $connection;

        if (mysqli_query($connection, $sqlquery)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sqlquery . "<br>" . mysqli_error($connection);
        }
    }

    /**
     * $username -> primary key of user
     * $name -> optional name
     */
    function register_user($username, $name) {
        if (isset($_POST['username'])) {
            $username = $_POST['username'];
            $name = $_POST['name'];

            add_user_to_database($username, $name);
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
</head>
<body>

    <form action="" method="POST">
        <label for="username">Enter username</label><br>
        <input type="text" name="username" id="username"><br>
        <label for="name">Enter name</label><br>
        <input type="text" name="name" id="name">
        <br>
        <button>Login</button>
    </form>
    
</body>
</html>