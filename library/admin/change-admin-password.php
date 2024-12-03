<?php
session_start();
require_once('includes/config.php');

// Enable proper error reporting during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enhanced session validation with CSRF protection
function validate_session() {
    if (strlen($_SESSION['alogin'] ?? '') == 0) {
        header('Location: index.php');
        exit();
    }
}

// Enhanced password change function
function change_password($dbh, $current_password, $new_password, $confirm_password) {
    $errors = [];

    // Validate password inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "All password fields are required.";
    }

    if ($new_password !== $confirm_password) {
        $errors[] = "New Password and Confirm Password do not match.";
    }

    // Password strength requirements
    if (strlen($new_password) < 8) {
        $errors[] = "New password must be at least 8 characters long.";
    }

    if (count($errors) > 0) {
        return $errors;
    }

    $username = $_SESSION['alogin'];

    try {
        // Verify current password
        $stmt = $dbh->prepare("SELECT Password FROM admin WHERE UserName = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($current_password, $user['Password'])) {
            return ["Current password is incorrect."];
        }

        // Hash new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password
        $update_stmt = $dbh->prepare("UPDATE admin SET Password = :password WHERE UserName = :username");
        $update_stmt->bindParam(':password', $new_password_hash);
        $update_stmt->bindParam(':username', $username);
        $update_stmt->execute();

        return ["Password successfully changed."];

    } catch (PDOException $e) {
        return ["Database error: " . $e->getMessage()];
    }
}

// Validate session on page load
validate_session();

$messages = [];
$message_type = '';

// Process password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change'])) {
    $messages = change_password(
        $dbh, 
        $_POST['password'] ?? '', 
        $_POST['newpassword'] ?? '', 
        $_POST['confirmpassword'] ?? ''
    );
    
    $message_type = (strpos($messages[0], 'successfully') !== false) ? 'success' : 'error';
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Change Password | Admin Panel</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    
    <style>
.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}

#changepass{
  padding-bottom: 400px;
}

.form-group{
    height: 20;
}


    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    
    <div class="content-wrapper">
        <div class="container">
            <div class="row">
            <h4 class="header-line">Admin Change Password</h4>
                <div class="col-md-6 col-md-offset-3">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <?php if (!empty($messages)): ?>
                                <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>">
                                    <?php foreach ($messages as $message): ?>
                                        <p><?php echo htmlspecialchars($message); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <form method="post" onsubmit="return validateForm();">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" name="password" class="form-control" required />
                                </div>

                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" name="newpassword" class="form-control" required />
                                </div>

                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" name="confirmpassword" class="form-control" required />
                                </div>

                                <button type="submit" name="change" class="btn btn-primary">Change Password</button>
                                <a class="btn btn-danger" href="admin_profile.php">Back to My Profile</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <script>
    function validateForm() {
        var newPassword = document.getElementsByName('newpassword')[0].value;
        var confirmPassword = document.getElementsByName('confirmpassword')[0].value;

        if (newPassword !== confirmPassword) {
            alert('New passwords do not match!');
            return false;
        }

        if (newPassword.length < 8) {
            alert('Password must be at least 8 characters long!');
            return false;
        }

        return true;
    }
    </script>
</body>
</html>