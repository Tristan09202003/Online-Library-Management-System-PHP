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
$sql = "SELECT FullName, AdminEmail, UserName, ProfileImage FROM admin WHERE UserName = :username";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission for updating details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $newFullName = $_POST['fullName'];
    $newEmail = $_POST['adminEmail'];
    $imageName = null;

    // Handle image upload
    if (!empty($_FILES['fileImg']['name'])) {
        $src = $_FILES['fileImg']['tmp_name'];
        $imageName = uniqid() . '_' . $_FILES['fileImg']['name'];
        $target = "assets/img/" . $imageName;
        move_uploaded_file($src, $target);
    }

    // Prepare the update query
    $sql = "UPDATE admin SET FullName = :fullName, AdminEmail = :adminEmail";
    
    // Add image to update if a new image was uploaded
    if ($imageName) {
        $sql .= ", ProfileImage = :profileImage";
    }

    $sql .= " WHERE UserName = :username";
    $stmt = $dbh->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':fullName', $newFullName);
    $stmt->bindParam(':adminEmail', $newEmail);
    $stmt->bindParam(':username', $username);
    
    if ($imageName) {
        $stmt->bindParam(':profileImage', $imageName);
    }
    
    // Execute the statement
    if ($stmt->execute()) {
        // Update password if provided
        if (!empty($_POST['newPassword'])) {
            $newPassword = $_POST['newPassword'];
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE admin SET Password = :password WHERE UserName = :username";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
        }

        // Refresh admin details
        $stmt = $dbh->prepare("SELECT FullName, AdminEmail, UserName, ProfileImage FROM admin WHERE UserName = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        echo '<script>alert("Profile updated successfully!");</script>';
    } else {
        echo '<script>alert("Error updating profile.");</script>';
    }
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
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            padding: 20px;
            border-radius: 8px;
        }

        .avatar-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .avatar-section img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #007bff;
        }

        .info-section {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .profile-field {
            display: flex;
            flex-direction: column;
        }

        .profile-field label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .profile-field input,
        .profile-field .static-value {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            height: 40px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            
        }
        .col-md-12 {
            color:white;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h2 class="header-line">Admin Profile</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="panel panel-primary">
                        <div class="panel-heading">Profile Details</div>
                        <div class="panel-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="profile-grid">
                                    <!-- Avatar Section -->
                                    <div class="avatar-section">
                                        <img src="assets/img/<?php echo htmlentities($admin['ProfileImage'] ?: 'noprofil.jpg'); ?>" id="image" alt="Profile Picture" />
                                        <input type="file" name="fileImg" id="fileImg" accept=".jpg, .jpeg, .png" style="display:none;" />
                                        <label for="fileImg" class="btn btn-primary btn-sm mt-3">
                                            <i class="fa fa-camera"></i> Change Photo
                                        </label>
                                    </div>

                                    <!-- Info Section -->
                                    <div class="info-section">
                                        <div class="profile-field">
                                            <label>Full Name</label>
                                            <input class="form-control" type="text" name="fullName"
                                                value="<?php echo htmlentities($admin['FullName']); ?>" required />
                                        </div>

                                        <div class="profile-field">
                                            <label>Username</label>
                                            <div class="static-value form-control">
                                                <?php echo htmlentities($admin['UserName']); ?>  
                                            </div>
                                        </div>

                                        <div class="profile-field">
                                            <label>Email Address</label>
                                            <input class="form-control" type="email" name="adminEmail"
                                                value="<?php echo htmlentities($admin['AdminEmail']); ?>" required />
                                        </div>

                                        </div>

                                        <div class="btn-group">
                                            <button type="submit" name="update" class="btn btn-success">
                                                <i class="fa fa-save"></i> Update Profile
                                            </button>
                                            <a href="change-admin-password.php" class="btn btn-primary">
                                                <i class="fa fa-lock"></i> Change Password
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script>
        document.getElementById('fileImg').addEventListener('change', function(event) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image').src = e.target.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    </script>
</body>
</html>