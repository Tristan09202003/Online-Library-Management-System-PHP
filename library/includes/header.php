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
                            <li><a href="#" id="notification-link"></a></li>

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
                            <div class="right-div1">
                                <a href="my-profile.php" class="profile-link">
                                    <img src="<?php echo $userImageSrc; ?>" class="user-profile-circle" alt="Profile" />
                                </a>
                            </div>
                            <style>
                                /* Modal Background Overlay */
                                #modal-overlay {
                                    display: none;
                                    position: fixed;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                    background: rgba(0, 0, 0, 0.5);
                                    z-index: 999;
                                }

                                /* Modal Window */
                                #notification-modal {
                                    display: none;
                                    position: fixed;
                                    top: 20%;
                                    left: 50%;
                                    transform: translateX(-50%);
                                    width: 400px;
                                    background: white;
                                    padding: 20px;
                                    border-radius: 8px;
                                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
                                    z-index: 1000;
                                }

                                #notification-modal h4 {
                                    margin-top: 0;
                                }

                                /* Close Button */
                                .close-btn {
                                    position: absolute;
                                    top: 5px;
                                    right: 5px;
                                    font-size: 18px;
                                    background: none;
                                    border: none;
                                    cursor: pointer;
                                }

                                /* Header Styling */
                                .header {
                                    background-color: #333;
                                    color: white;
                                    padding: 10px 20px;
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                }

                                .header a {
                                    color: white;
                                    text-decoration: none;
                                    margin-left: 20px;
                                    font-size: 16px;
                                    cursor: pointer;
                                }

                                .header a:hover {
                                    text-decoration: underline;
                                }

                                /* Profile image */
                                .user-profile-circle {
                                    width: 40px;
                                    height: 40px;
                                    border-radius: 50%;
                                    object-fit: cover;
                                    cursor: pointer;
                                    border: 2px solid white;
                                    transition: transform 0.2s;
                                }

                                .user-profile-circle:hover {
                                    transform: scale(1.1);
                                }

                                .right-div1 {
                                    float: right;
                                    padding: 17px;
                                }
                            </style>
                            
                            <?php

                            if (isset($_POST['user_id'])) {
                                $userId = $_POST['user_id'];

                                // Fetch notifications for the user
                                $sql = "SELECT Notifications FROM tblstudents WHERE StudentId = :id";
                                $stmt = $dbh->prepare($sql);
                                $stmt->bindParam(':id', $userId, PDO::PARAM_STR);
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_OBJ);

                                if ($result && $result->Notifications) {
                                    $notifications = json_decode($result->Notifications, true);
                                    foreach ($notifications as $notif) {
                                        echo "<p>" . htmlspecialchars($notif['message']) .
                                            "<small style='display: block; color: #888;'>" .
                                            htmlspecialchars($notif['timestamp']) . "</small></p>";
                                    }
                                } else {
                                    echo "<p>No new notifications.</p>";
                                }
                            }
                            ?>

                            <!-- Modal Overlay -->
                            <div id="modal-overlay"></div>

                            <!-- Notification Modal -->
                            <div id="notification-modal">
                                <button id="close-modal" class="close-btn">&times;</button>
                                <h4>Your Notifications</h4>
                                <div id="notification-content" style="max-height: 300px; overflow-y: auto;">
                                    <!-- Notifications will load here -->
                                </div>
                            </div>

                            <script>
                                // When the user clicks the Notification link
                                document.getElementById('notification-link').addEventListener('click', function(e) {
                                    e.preventDefault(); // Prevent the default link behavior (no page navigation)

                                    // Show modal and overlay
                                    document.getElementById('modal-overlay').style.display = 'block';
                                    document.getElementById('notification-modal').style.display = 'block';

                                    // Fetch and load notifications
                                    fetchNotifications();
                                });

                                // Close the modal when the close button is clicked
                                document.getElementById('close-modal').addEventListener('click', function() {
                                    // Hide modal and overlay
                                    document.getElementById('notification-modal').style.display = 'none';
                                    document.getElementById('modal-overlay').style.display = 'none';
                                });

                                // Function to fetch notifications via AJAX
                                function fetchNotifications() {
                                    const userId = <?php echo json_encode($_SESSION['stdid']); ?>; // User's ID from session

                                    // AJAX call to fetch notifications
                                    const xhr = new XMLHttpRequest();
                                    xhr.open('POST', 'fetch_notifications.php', true);
                                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                    xhr.onload = function() {
                                        if (xhr.status === 200) {
                                            document.getElementById('notification-content').innerHTML = xhr.responseText;
                                        } else {
                                            document.getElementById('notification-content').innerHTML = '<p>Error loading notifications.</p>';
                                        }
                                    };
                                    xhr.send('user_id=' + userId);
                                }
                            </script>

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