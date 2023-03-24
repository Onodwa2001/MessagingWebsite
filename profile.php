<?php 

    session_start();

    include('./connect.php');

    $user = array();

    function getRecordsOfLoggedUser() {
        global $connection, $user;

        $loggedUser = $_SESSION['username'];

        $sql = "SELECT * FROM users WHERE username = '$loggedUser'";

        $result = mysqli_query($connection, $sql);
        $data = mysqli_fetch_array($result);

        array_push($user, array('image' => $data['image'], 'username' => $data['username'], 'name' => $data['name'], 'city' => $data['city']));
    }

    getRecordsOfLoggedUser();


    function updateRecord($value, $attribute) {
        global $connection;

        $loggedUser = $_SESSION['username'];

        $sql = "UPDATE users SET " . $attribute . " = '" . $value . "' WHERE username = '$loggedUser';";

        if (!mysqli_query($connection, $sql)) {
            echo "Error: " . $sql . "<br/>" . mysqli_error($connection);
        }
    }

    if (isset($_POST['save_changes'])) {
        if ($_POST['name'] !== '') {
            $name = $_POST['name'];
            updateRecord($name, 'name');
            header('Location: profile.php');
        }

        if ($_POST['username'] !== '') {
            $username = $_POST['username'];
            updateRecord($username, 'username');
            header('Location: profile.php');
        }

        if ($_POST['city'] !== '') {
            $city = $_POST['city'];
            updateRecord($city, 'city');
            header('Location: profile.php');
        }


        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        

        if (substr($file_name, -4) === '.png' || substr($file_name, -5) === '.jpeg' || substr($file_name, -4) === '.jpg') {
            move_uploaded_file($file_tmp, "uploads/" . $file_name);
            updateRecord($file_name, 'image');
        } else {
            echo 'That is not png nor jpeg/jpg';
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
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
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

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-mdb-toggle="modal" data-mdb-target="#exampleModal">Edit details</button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit your details</h5>
                                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                                </div>
                            <div class="modal-body">

                                    <input type="file" class="form-control" name="image" /><br>
                                
                                    <input name="name" placeholder="Full Name" class="form-control" /><br>

                                    <input name="username" placeholder="Username" class="form-control" /><br>

                                    <input name="city" placeholder="City" class="form-control" />

                            </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" name="save_changes">Save changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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