<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include('includes/config.php');

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Process login
if (isset($_POST['login'])) {
    // Sanitize input
    $email = filter_var($_POST['emailid'], FILTER_SANITIZE_EMAIL);
    $password = md5($_POST['password']); // Note: MD5 is not secure, consider using password_hash()

    try {
        // Prepared statement to prevent SQL injection
        $sql = "SELECT EmailId, Password, StudentId, Status FROM tblstudents WHERE EmailId=:email AND Password=:password";
        $query = $dbh->prepare($sql);

        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);

        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            foreach ($results as $result) {
                // Check account status
                if ($result->Status == 1) {
                    // Set session variables securely
                    $_SESSION['stdid'] = $result->StudentId;
                    $_SESSION['login'] = true;
                    $_SESSION['email'] = $result->EmailId;

                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Account blocked
                    $error = "Your account has been blocked. Please contact admin.";
                }
            }
        } else {
            // Invalid login
            $error = "Invalid email or password";
        }
    } catch (PDOException $e) {
        // Log error for debugging
        error_log("Login Error: " . $e->getMessage());
        $error = "An error occurred. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Online Library Management System | Login</title>

    <!-- CSS Links -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <style>
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .dropdown-divider{
            margin-top: 10px;
        }

        .dropdown-item1{
            padding-top: 20px;
            
        }
        
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <!-- Image Section -->
                <div class="col-md-6 col-sm-12 text-center">
                    <div class="kingfisher">
                        <img src="assets/img/kingfisher.png" class="img-fluid" alt="Kingfisher Image" />
                    </div>
                </div>

                <!-- Login Form -->
                <div class="col-md-6 col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            LOGIN FORM
                        </div>
                        <div class="panel-body">
                            <?php
                            // Display error messages
                            if (isset($error)) {
                                echo "<div class='error-message'>$error</div>";
                            }
                            ?>
                            <form role="form" method="post">
                                <div class="form-group">
                                    <label>Enter Email ID</label>
                                    <input class="form-control" type="email" name="emailid" required autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="form-control" type="password" name="password" required autocomplete="off" />
                                    <a class="dropdown-item1" href="#"></a>
                                </div>
                                <button type="submit" name="login" class="btn btn-info">LOGIN</button>
                            </form>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="signup.php">New around here? Sign up</a> 
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <!-- JavaScript -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>