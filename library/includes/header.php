<?php
// Check if the session is already started before calling session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="navbar navbar-inverse set-radius-zero">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">
                <img src="assets/img/0.png" />
            </a>
        </div>

        <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true) { ?>
            <div class="right-div">
                <a href="logout.php" class="btn btn-danger pull-right">LOG OUT</a>
            </div>
        <?php } ?>
    </div>
</div>

<?php if (isset($_SESSION['login']) && $_SESSION['login'] === true) { ?>
    <!-- Menu for Logged-in Users -->
    <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">

                            <li><a href="dashboard.php" class="menu-top-active">DASHBOARD</a></li>
                            <li><a href="issued-books.php">Issued Books</a></li>
                            <li><a href="user_notification.php">Notification</a></li>

                            <?php 
                            // Ensure session is started
                            if (isset($_SESSION['stdid'])) {
                                try {
                                    // Get user ID from session
                                    $sid = $_SESSION['stdid'];

                                    // Query to fetch user profile with all details
                                    $sql = "SELECT * FROM tblstudents WHERE StudentId=:sid";
                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':sid', $sid, PDO::PARAM_STR);
                                    $query->execute();

                                    // Fetch result
                                    $result = $query->fetch(PDO::FETCH_OBJ);

                                    // Determine the profile image source
                                    $userImageSrc = $result && $result->image ? "assets/img/" . $result->image : "assets/img/noprofil.jpg";
                                } catch (Exception $e) {
                                    // Handle database errors gracefully
                                    $userImageSrc = "assets/img/noprofil.jpg";
                                    // Log the error for debugging
                                    error_log("Profile image query error: " . $e->getMessage());
                                }
                            } else {
                                // Default image for non-logged-in users
                                $userImageSrc = "assets/img/noprofil.jpg";
                            }
                            ?>
                            <style>
                                .user-profile-circle {
                                    width: 30px;
                                    height: 30px;
                                    border-radius: 50%;
                                    object-fit: cover;
                                    cursor: pointer;
                                    border: 2px solid white;
                                    transition: transform 0.2s;
                                    justify-content: center;
                                }

                                .user-profile-circle:hover {
                                    transform: scale(1.1);
                                }

                                .navbar-nav>li>a.profile-link {
                                    padding: 5px;
                                }
                            </style>
                            <div class="right-div">
                                <a href="my-profile.php" class="profile-link">
                                    <img src="<?php echo $userImageSrc; ?>" class="user-profile-circle" alt="Profile" />
                                </a>
                            </div>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } else { ?>
    <!-- Menu for Guests -->
    <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="login.php">User Login</a></li>
                            <li><a href="signup.php">User Signup</a></li>
                            <li><a href="adminlogin.php">Admin Login</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>