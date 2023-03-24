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
                array_push($users, array('username' => $row["username"], 'name' => $row['name'], 'city' => $row['city']));
            }

            return $users;
        } else {
            echo "No users yet, start a conversation";
        }
        return null;
    }

    $users = getAllUsers();

    function addToInvitesTable($invitee_id, $inviter_id) {

        $sql = "INSERT INTO invitations(inviter_id, invitee_id) VALUES('$inviter_id', '$invitee_id')";

        global $connection;

        if (!mysqli_query($connection, $sql)) {
            echo 'Error: ' . mysqli_err();
        }

    }

    function inviteUser() {
        $inviter_id = $_SESSION['username'];
        $invitee_id = $_POST['invite'];

        addToInvitesTable($invitee_id, $inviter_id);
    }

    if (isset($_POST['invite'])) {
        inviteUser();
    }

    function getAllInvites() {
        global $connection;
        $invites = array();
        $logged_in_user = $_SESSION['username'];

        $sql = "SELECT * FROM invitations WHERE invitee_id='$logged_in_user'";

        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $inviter = $row['inviter_id'];

                $sql2 = "SELECT username, name, city FROM users WHERE username='$inviter'";

                $result2 = mysqli_query($connection, $sql2);
                $data = mysqli_fetch_array($result2);

                array_push($invites, array('username' => $data['username'], 'name' => $data['name'], 'city' => $data['city']));
            }
        } else {

        }
        return $invites;
    }

    $invites = getAllInvites();

    function getAllSentInvites() {
        global $connection;
        $invites = array();
        $logged_in_user = $_SESSION['username'];

        $sql = "SELECT * FROM invitations WHERE inviter_id='$logged_in_user'";

        $result = mysqli_query($connection, $sql);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $invitee = $row['invitee_id'];

                $sql2 = "SELECT username, name, city FROM users WHERE username='$invitee'";

                $result2 = mysqli_query($connection, $sql2);
                $data = mysqli_fetch_array($result2);

                array_push($invites, array('username' => $data['username'], 'name' => $data['name'], 'city' => $data['city']));
            }
        } else {

        }
        return $invites;
    }


    function removeInvite($inviter_id, $invitee_id) {

        $sqlquery = "DELETE FROM invitations WHERE inviter_id = '$inviter_id' AND invitee_id='$invitee_id'";

        global $connection;

        if (mysqli_query($connection, $sqlquery)) {
            // successfully deleted
        } else {
            echo "Error: " . $sqlquery . "<br>" . mysqli_error($connection);
        }

    }

    function addToFriendsTable($inviter_id, $invitee_id) {

        $sql = "INSERT INTO friends(friend1, friend2) VALUES('$inviter_id', '$invitee_id')";

        global $connection;

        if (!mysqli_query($connection, $sql)) {
            echo "Error: " . $sql . "<br/>" . mysqli_error($connection);
        }

    }

    function acceptRequest() {
        $inviter_id = $_POST['accept'];
        $invitee_id = $_SESSION['username'];

        removeInvite($inviter_id, $invitee_id);
        addToFriendsTable($inviter_id, $invitee_id);
        header("Location: findfriends.php");
    }
    
    if (isset($_POST['accept'])) {
        acceptRequest();
    }    

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

    foreach($friends = getAllFriends() as $friend) {
        if ($friend['friend1'] !== $_SESSION['username']) {
            array_push($myFriends, $friend['friend1']);
        } else if ($friend['friend2'] !== $_SESSION['username']) {
            array_push($myFriends, $friend['friend2']);
        }
    }

    $requested = array();

    // print_r(getAllSentInvites());

    foreach($requests = getAllSentInvites() as $request) {
        array_push($requested, $request['username']);
    }

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

        #search {
            width: fit-content;
            margin: 40px auto 0 auto;
        }

        #search button {
            border: none;
            padding: 5px 15px 5px 15px;
        }

        .person {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
            max-width: 700px;
            display: flex;
            padding: 40px;
            border-radius: 8px;
            height: 180px;
        }

        .person h1 {
            font-size: 20px;
        }

        .person div:last-of-type {
            margin-left: auto;

            /* position: absolute;
            top: 50%;
            -ms-transform: translateY(-50%);
            transform: translateY(-50%); */
        }

        .buttons button {
            background-color: #3b71ca;
            border: none;
            color: white;
            padding: 5px 15px 5px 15px;
            border-radius: 4px;
            
        }

        .container {
            display: grid;
            grid-template-columns: 70% 30%;
        }

        .friends-to-find {
            max-width: 700px;
        }

        .image {
            border: solid white 5px;
            border-radius: 50%;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
            max-height: 100px;
            max-width: 100px;
            margin-right: 20px;
        }

        .image img {
            border-radius: 50%;
        }

        .invite {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            max-width: 500px;
            padding: 40px;
            display: flex;
        }

        .invite div:last-of-type {
            margin-left: auto;
        }

        .invite button {
            border: none;
            padding: 5px 15px 5px 15px;
        }

        #messageButton {
            background-color: #efefef;
            color: black;
        }


    </style>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

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


    <div class="search" id="search">
        <input name="search" placeholder="Find someone to add" />
        <button type="submit">Search</button>
    </div>

    <br>
    <div class="container">
        <div class="friends-to-find">
            <?php foreach($users as $user) { ?>
                <!-- <a href="#" class="users-link"> -->
                    <?php if ($user['username'] != $_SESSION['username']) { ?>
                        <div class="person">
                            <div class="image">
                                <img src="images/thumbnail.png" alt="user-image" height="100%" width="100%" />
                            </div>
                            <div class="info">
                                <h1><?php echo $user['name']; ?></h1>
                                <div>Lives in <?php echo $user['city']; ?></div>
                                <!-- <div>100 friends</div> -->
                            </div>
                            <div class="buttons" style="z-index: 1;">

                                <?php if (in_array($user['username'], $requested)) { ?>
                                    <form action="findfriends.php" method="POST" id="inviteForm">
                                        <button style="opacity: .7;" name="invite" value="<?php echo $user['username']; ?>" disabled>Pending invite</button>
                                    </form>
                                <?php } else if (!in_array($user['username'], $myFriends)) { ?>
                                    <form action="findfriends.php" method="POST" id="inviteForm">
                                        <button name="invite" value="<?php echo $user['username']; ?>">Invite</button>
                                    </form>
                                <?php } else { ?>
                                    <button id="messageButton" onclick="window.location.href='chatroom.php?id=<?php echo $user['username']; ?>';" name="message" value="<?php echo $user['username']; ?>">Message</button>
                                <?php } ?>

                                

                            </div>
                        </div>
                    <?php } ?>
                    
                <!-- </a> -->
            <?php } ?>   
        
        </div>

        <div class="invites">
            <h1 style="font-size: 25px;">Invites (<?php echo count($invites); ?>)</h1>
            <?php foreach($invites as $invite) { ?>
                <div class="invite">
                    <div class="info">
                        <div class="nameOfInviter"><h5><?php echo $invite['name']; ?></h5></div>
                        <div class="addressOfInviter"><?php echo $invite['city']; ?></div>
                    </div>
                    
                    <div class="accept-btn-wrap">
                        <form action="" method="POST">
                            <button name="accept" value="<?php echo $invite['username']; ?>">Accept</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>


    <script>
        document.getElementById('login').addEventListener('click', (e) => {
            location.href="index.php";
        });

        document.getElementById('signup').addEventListener('click', (e) => {
            location.href="register.php";
        });
    </script>


    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
</body>
</html>