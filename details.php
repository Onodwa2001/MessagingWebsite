<?php 

    session_start();

    include('./connect.php');

    $user = array();

    function getRecordsOfClickedUser() {
        global $connection, $user;

        $clickedUser = $_GET['id'];

        $sql = "SELECT * FROM users WHERE username = '$clickedUser'";

        $result = mysqli_query($connection, $sql);
        $data = mysqli_fetch_array($result);

        array_push($user, array('image' => $data['image'], 'username' => $data['username'], 'name' => $data['name'], 'city' => $data['city']));
    }

    getRecordsOfClickedUser();

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


    $friendsArray = array();

    foreach ($friends = getAllFriends() as $friend) {
        if ($friend['friend1'] != $_SESSION['username']) {
            array_push($friendsArray, $friend['friend1']);
        } else if ($friend['friend2'] != $_SESSION['username']) {
            array_push($friendsArray, $friend['friend2']);
        }
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

        .profile-wrap {
            margin-top: 100px;
        }

        .profile {
            background-color: #efefef;
            width: 700px;
            margin: 0 auto 0 auto;
            position: relative;
            padding: 50px 50px 50px 0px;
            border-radius: 30px;
        }

        .profile-image {
            width: fit-content;
            border: solid white 5px;
            /* box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1); */
            margin: 0 auto 0 auto;
            border-radius: 50% 50%;
            position: absolute;
            min-height: 150px;
            min-width: 150px;
            background-color: #efefef;
            margin-top: -75px;
            margin-left: -20px;
        }

        .profile-image img {
            max-width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50% 50%;
        }

        .nav-links i {
            font-size: 14px;
        }

        .navlink .clicked {
            background-color: #efefef;
        }

        .message {
            border: none;
            background-color: #3b71ca;
            border-radius: 8px;
            padding: 5px 10px 5px 10px;
            color: white;
        }
        
        .invite {
            border: none;
            background-color: #3b71ca;
            border-radius: 8px;
            padding: 5px 10px 5px 10px;
            color: white;
        }

        .view {
            border: none;
            background-color: #3b71ca;
            border-radius: 8px;
            padding: 5px 10px 5px 10px;
            color: white;
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
            <a class="navbar-brand me-2" href="friends.php">
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

            <div class="d-flex align-items-center nav-links">
                <button type="button" class="btn btn-link px-3 me-2 navlink" id="profile">
                    <i class="fa-solid fa-user"></i>
                </button>
                <button type="button" class="btn btn-link px-3 me-2 navlink" id="chats">
                    <i class="fa-solid fa-comments"></i>
                </button>
                <button type="button" class="btn btn-link px-3 me-2 navlink" id="findfriends">
                    <i class="fa-solid fa-user-group"></i>
                </button>
                <button type="button" class="btn btn-link px-3 me-2 navlink" id="login">
                    <i class="fa-solid fa-right-to-bracket"></i>
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

    <div class="profile-wrap">
        <div class="profile">
            <div class="profile-image">
                <img src="uploads/<?php echo $user[0]['image'] === '' ? '6522516.png' : $user[0]['image']; ?>" alt="profile picture">
            </div>

            <div class="profile-data" style="text-align: center">
                <h1><?php echo $user[0]['name']; ?></h1>
                <div>Username: <?php echo $user[0]['username']; ?></div>
                <div>Lives in: <?php echo $user[0]['city']; ?></div>

                <br>

                <!-- CHECK IF USER IS YOUR FRIEND, OR SENT AN INVITE ALREADY -->

                <?php 

                    $logged = $_SESSION['username'];

                    if ($user[0]['username'] === $logged) {
                        echo '<button class="view" onclick="window.location.href=\'profile.php\'">View</button>';
                    } else if (in_array($user[0]['username'], $friendsArray)) { // is my friend
                        echo '<button class="message" onclick="window.location.href=\'chatroom.php?id=' . $user[0]['username'] . '\'">Message</button>';
                    } else if (!in_array($user[0]['username'], $friendsArray)) { // 
                        echo '<form action="findfriends.php" method="POST" id="inviteForm">
                                <button class="invite" name="invite" value="' . $user[0]['username'] . '">Invite</button>
                            </form>';
                    }
                
                ?>
                
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

        document.getElementById('findfriends').addEventListener('click', (e) => {
            location.href="findfriends.php";
        });

        document.getElementById('chats').addEventListener('click', (e) => {
            location.href="friends.php";
        });
        
        document.getElementById('profile').addEventListener('click', (e) => {
            location.href="profile.php";
        });

        let lastClickedLink = null;

        const links = document.querySelectorAll('.navlink');

        links.forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                
                if (lastClickedLink) {
                    lastClickedLink.classList.remove('clicked');
                }
                
                this.classList.add('clicked');
                lastClickedLink = this;
            });
        });

    </script>

    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.js"></script>
</body>
</html>