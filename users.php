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

    <!-- Font Awesome -->
    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
    />
    <!-- Google Fonts -->
    <link
    href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
    rel="stylesheet"
    />
    <!-- MDB -->
    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css"
    rel="stylesheet"
    />
    
    <style>
        * {
            font-family: sans-serif;
        }

        h1 {
            text-align: center;
        }

        .users-wrap {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            max-width: 700px;
            padding: 40px;
            margin: 0 auto 0 auto;
        }

        .search-bar {
            width: fit-content;
            margin: 0 auto 0 auto;
        }

        /* input {
            background-color: white !important;
        } */
    </style>
</head>
<body>

    <h1>Find someone to talk to</h1>
    
    <div class="users-wrap">
        <div class="input-group rounded search-bar">
            <input type="search" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="search-addon" />
            <span class="input-group-text border-0" id="search-addon">
                <button type="button" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </span>
        </div>

        <br/>
        <h5>Users available</h5>
        <br/>
        <div class="users">
            <?php foreach($users as $user) { ?>
                <a href="chatroom.php?id=<?php echo $user['username'];?>" style="display: flex;">
                    <i class="fas fa-user"></i><p style="margin-left: 10px;"><?php echo $user['name']; ?></p>
                </a>
            <?php } ?>
        </div>
    </div>
    

</body>
</html>