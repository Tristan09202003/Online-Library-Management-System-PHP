<?php 
session_start();
include('includes/config.php'); // Include your PDO database connection

// Ensure admin is logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

// Fetch current admin details
$username = $_SESSION['alogin'];
$sql = "SELECT FullName, AdminEmail, UserName FROM admin WHERE UserName = :username"; // Use correct column names
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission for updating details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newFullName = $_POST['fullName'];
    $newEmail = $_POST['adminEmail'];
    $newUsername = $_POST['username'];
    $newPassword = $_POST['newPassword'] ?? ''; // New password input

    // Prepare the update query
    $sql = "UPDATE admin SET FullName = :fullName, AdminEmail = :adminEmail, UserName = :username WHERE UserName = :current_username";
    $stmt = $dbh->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':fullName', $newFullName);
    $stmt->bindParam(':adminEmail', $newEmail);
    $stmt->bindParam(':username', $newUsername);
    $stmt->bindParam(':current_username', $username);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Update password if provided
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the new password
            $sql = "UPDATE admin SET Password = :password WHERE UserName = :current_username";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':current_username', $username);
            $stmt->execute();
        }

        $_SESSION['alogin'] = $newUsername; // Update session variable if username changed
        $_SESSION['msg'] = "Details updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating details.";
    }

    // Refresh the admin details after update
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':username', $newUsername);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Admin Profile | Online Library Management System</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        .profile-box {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="content-wrapper">
    <div class="container">
        <h4 class="header-line">Admin Profile</h4>

        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> <?php echo htmlentities($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php } ?>
        <?php if (isset($_SESSION['msg'])) { ?>
            <div class="alert alert-success">
                <strong>Success:</strong> <?php echo htmlentities($_SESSION['msg']); unset($_SESSION['msg']); ?>
            </div>
        <?php } ?>

        <div class="profile-box">
            <form action="admin-profile.php" method="POST" id="profileForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fullName">Name:</label>
                            <input type="text" name="fullName" class="form-control" value="<?php echo htmlspecialchars($admin['FullName']); ?>" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="username">User Name:</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($admin['UserName']); ?>" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="adminEmail">Admin Email:</label>
                            <input type="email" name="adminEmail" class="form-control" value="<?php echo htmlspecialchars($admin['AdminEmail']); ?>" readonly required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="currentPassword">Current Password:</label>
                            <input type="password" name="currentPassword" class="form-control" placeholder="Enter current password" id="currentPassword" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password:</label>
                            <input type="password" name="newPassword" class="form-control" placeholder="Enter new password" id="newPassword" readonly>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password:</label>
                            <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm new password" id="confirmPassword" readonly>
                        </div>
                    </div>
                </div>

                <button type="button" id="editButton" class="btn btn-primary">Edit</button>
                <button type="submit" id="saveButton" class="btn btn-success" style="display: none;">Save Changes</button>
                <button type="button" id="cancelButton" class="btn btn-secondary" style="display: none;">Cancel</button>
            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script src="assets/js/jquery-1.10.2.js"></script>
<script>
    // JavaScript to toggle between view and edit mode
    document.getElementById('editButton').onclick = function() {
        toggleEditMode(true);
    };
    document.getElementById('cancelButton').onclick = function() {
        toggleEditMode(false);
    };

    function toggleEditMode(enableEdit) {
        const form = document.getElementById('profileForm');
        const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.readOnly = !enableEdit;
            if (enableEdit) {
                // Show password fields if editing
                if (input.name === 'currentPassword') {
                    input.type = 'text'; // Change to text to show password
                }
            } else {
                // Hide password fields when not editing
                if (input.name === 'currentPassword') {
                    input.type = 'password'; // Change back to password to hide it
                }
            }
        });

        document.getElementById('editButton').style.display = enableEdit ? 'none' : 'inline-block';
        document.getElementById('saveButton').style.display = enableEdit ? 'inline-block' : 'none';
        document.getElementById('cancelButton').style.display = enableEdit ? 'inline-block' : 'none';
    }
</script>
</body>
</html>
