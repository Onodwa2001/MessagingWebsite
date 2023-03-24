<?php 

    session_start();

    include('connect.php');

    function getAllFriends() {
        global $connection;

        $loggedUser = $_SESSION['username'];
        $sql = "SELECT * FROM friends WHERE friend1='$loggedUser' OR friend2='$loggedUser'";

        $result = mysqli_query($connection, $sql);

        $data = array();

        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }

        return $data;
    }

    $myFriends = array();

    foreach (getAllFriends() as $friendship) {
        if ($friendship['friend1'] !== $_SESSION['username']) {
            array_push($myFriends, $friendship['friend1']);
        } else if ($friendship['friend2'] !== $_SESSION['username']) {
            array_push($myFriends, $friendship['friend2']);
        }
    }

    function getFriendsRecords($id) {
        global $connection;

        $sql = "SELECT * FROM users WHERE username = '$id'";
        $result = mysqli_query($connection, $sql);

        $data = mysqli_fetch_array($result);

        return array('username' => $data['username'], 'name' => $data['name'], 'city' => $data['city']);
    }


    $myFriendsAndRecords = array();
    
    foreach ($myFriends as $myFriend) {
        array_push($myFriendsAndRecords, getFriendsRecords($myFriend));
    }

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



    function getAllUnreadMessages() {
        global $connection;

        $current_logged_in_user = $_SESSION['username'];

        $sql = "SELECT * FROM unread_messages WHERE receiver_id='$current_logged_in_user'";
        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            $unread_messages = array();

            while($row = mysqli_fetch_assoc($result)) {
                array_push($unread_messages, array('message' => $row["message"], 'sender_id' => $row['sender_id'], 'receiver_id' => $row['receiver_id']));
            }

            return $unread_messages;
        } else {
            echo "";
        }
        return null;
    }

    $users = getAllUsers();
    $unread_messages = getAllUnreadMessages();

    function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();
    
        if ($array != null) {
            foreach($array as $val) {
                if (!in_array($val[$key], $key_array)) {
                    $key_array[$i] = $val[$key];
                    $temp_array[$i] = $val;
                }
                $i++;
            }
        }
        return $temp_array;
    }

    $unread_messages_with_no_duplicates = unique_multidim_array($unread_messages, "sender_id");



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

        .message_dot {
            background-color: blue;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .navigations {
            margin-top: 20px;
        }
        
        .navigations button {
            width: 49%;
            border: none;
            padding: 5px;
            transition: transform 0.2s ease-out;
            /* height: 50px; */
        }

        .navigations button:hover {
            background-color: #b2aeae;
        }

        /* input {
            background-color: white !important;
        } */
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body onload="refresh()">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- Container wrapper -->
        <div class="container">
            <!-- Navbar brand -->
            <a class="navbar-brand me-2" href="users.php">
            <i class="fa-solid fa-message"></i>
            &nbsp&nbsp<span style="font-size: 15px;">Welcome <?php echo $_SESSION['username']; ?></span>
            </a>

            <!-- Toggle button -->
            <button
            class="navbar-toggler"
            type="button"
            data-mdb-toggle="collapse"
            data-mdb-target="#navbarButtonsExample"
            aria-controls="navbarButtonsExample"
            aria-expanded="false"
            aria-label="Toggle navigation"
            >
            <i class="fas fa-bars"></i>
            </button>

            <!-- Collapsible wrapper -->
            <div class="collapse navbar-collapse" id="navbarButtonsExample">
            <!-- Left links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                <!-- <a class="nav-link" href="#">Dashboard</a> -->
                </li>
            </ul>
            <!-- Left links -->

            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-link px-3 me-2" id="login">
                Login
                </button>
                <button type="button" class="btn btn-primary me-3" id="signup">
                Sign up
                </button>
                
            </div>
            </div>
            <!-- Collapsible wrapper -->
        </div>
        <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->

    <br/>
    <h1>Find someone to talk to</h1>
    
    <div class="users-wrap">
        <div class="input-group rounded search-bar">
            <input type="search" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="search-addon" />
            <span class="input-group-text border-0" id="search-addon">
                <button type="button" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </span>
        </div>

        <div class="navigations">
            <button id="all-users-btn">All My Friends</button>
            <button id="messages-btn">Messages 
            <?php if (count($unread_messages_with_no_duplicates) > 0) { ?>
                <span style="background-color: blue; padding: 2.5px 5px 2.5px 5px; border-radius: 50%; color: white;"><?php echo count($unread_messages_with_no_duplicates);?></span></button>
            <?php } ?>
        </div>

        <div class="all-users" id="all-users">
            <br/>
            <h5>All My Friends</h5>
            <br/>
            <div class="users" id="users">
                <?php foreach($myFriendsAndRecords as $user) { ?>
                    <a href="chatroom.php?id=<?php echo $user['username'];?>" style="display: flex;">
                        <i class="fas fa-user"></i><p style="margin-left: 10px;"><?php echo $user['name']; ?></p> &nbsp&nbsp
                        <?php if ($unread_messages_with_no_duplicates != null) { ?>
                            <?php foreach ($unread_messages_with_no_duplicates as $message) { ?>
                                <?php if ($message['sender_id'] === $user['username']) { ?>
                                    <?php echo '<span class="message_dot"></span>'; ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </a>
                <?php } ?>
            </div>
        </div>

        <div class="users-who-messaged-you" id="users-who-messaged-you" style="display: none;">
            <br/>
            <h5>Messages</h5>
            <br/>
            <div class="users2" id="users2">
                <?php foreach($users as $user) { ?>
                    <a href="chatroom.php?id=<?php echo $user['username'];?>" style="display: flex;">
                        <?php if ($unread_messages_with_no_duplicates != null) { ?>
                            <?php foreach ($unread_messages_with_no_duplicates as $message) { ?>
                                <?php if ($message['sender_id'] === $user['username']) { ?>
                                    <i class="fas fa-user"></i>&nbsp&nbsp&nbsp<?php echo $user['name'] . ' &nbsp&nbsp&nbsp&nbsp<span class="message_dot"></span>'; ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('login').addEventListener('click', (e) => {
            location.href="index.php";
        });

        document.getElementById('signup').addEventListener('click', (e) => {
            location.href="register.php";
        });
        
        function refresh(){
            $('#users').load(location.href + " #users");
            $('#users2').load(location.href + " #users2");
            $('#messages-btn').load(location.href + " #messages-btn");
        }

        setInterval(function(){
            refresh();
        }, 1000);


        let allUsers = document.getElementById('all-users');
        let messages = document.getElementById('users-who-messaged-you');
        let allMessagesBtn = document.getElementById('all-users-btn');
        let messagesBtn = document.getElementById('messages-btn');

        messagesBtn.addEventListener('click', (e) => {
            allUsers.style.display = 'none';
            messages.style.display = 'block';
        });

        allMessagesBtn.addEventListener('click', (e) => {
            messages.style.display = 'none';
            allUsers.style.display = 'block';
        });

    </script>

    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>

</body>
</html>