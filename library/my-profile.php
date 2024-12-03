<?php
session_start();
include('includes/config.php');
error_reporting(0);

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['update'])) {
        $sid = $_SESSION['stdid'];
        $fname = $_POST['fullanme'];
        $mobileno = $_POST['mobileno'];
        $imageName = $_FILES['fileImg']['name'];

        if ($imageName) {
            $src = $_FILES['fileImg']['tmp_name'];
            $imageName = uniqid() . '_' . $imageName;
            $target = "assets/img/" . $imageName;
            move_uploaded_file($src, $target);

            $sql = "UPDATE tblstudents SET FullName=:fname, MobileNumber=:mobileno, image=:image WHERE StudentId=:sid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':image', $imageName, PDO::PARAM_STR);
        } else {
            $sql = "UPDATE tblstudents SET FullName=:fname, MobileNumber=:mobileno WHERE StudentId=:sid";
            $query = $dbh->prepare($sql);
        }

        $query->bindParam(':sid', $sid, PDO::PARAM_STR);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
        $query->execute();

        echo '<script>alert("Your profile has been updated")</script>';
    }

    $sid = $_SESSION['stdid'];
    $sql = "SELECT * FROM tblstudents WHERE StudentId=:sid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':sid', $sid, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
?>

    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <title>Online Library Management System | Student Profile</title>
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
                gap: 15px;
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
                
            }

            .btn-group {
                display: flex;
                gap: 10px;
                margin-top: 20px;
            }
        </style>
    </head>

    <body>
        <?php include('includes/header.php'); ?>

        <div class="content-wrapper">
            <div class="container">
                <div class="row pad-botm">
                    <div class="col-md-12">
                        <h2 class="header-line">My Profile</h2>
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
                                            <img src="assets/img/<?php echo htmlentities($result->image) ?: 'noprofil.jpg'; ?>" id="image" alt="Profile Picture" />
                                            <input type="file" name="fileImg" id="fileImg" accept=".jpg, .jpeg, .png" style="display:none;" />
                                            <label for="fileImg" class="btn btn-primary btn-sm mt-3">
                                                <i class="fa fa-camera"></i> Change Photo
                                            </label>
                                        </div>

                                        <!-- Info Section -->
                                        <div class="info-section">
                                            <div class="profile-field">
                                                <label>Full Name</label>
                                                <input class="form-control" type="text" name="fullanme"
                                                    value="<?php echo htmlentities($result->FullName); ?>" required />
                                            </div>

                                            <div class="profile-field">
                                                <label>Mobile Number</label>
                                                <input class="form-control" type="text" name="mobileno"
                                                    value="<?php echo htmlentities($result->MobileNumber); ?>" required />
                                            </div>

                                            <div class="profile-field">
                                                <label>Email Address</label>
                                                <input class="form-control" type="email"
                                                    value="<?php echo htmlentities($result->EmailId); ?>" readonly />
                                            </div>

                                            <div class="profile-field">
                                                <label>Student ID</label>
                                                <div class="static-value form-control">
                                                    <?php echo htmlentities($result->StudentId); ?>
                                                </div>
                                            </div>

                                            <div class="btn-group">
                                                <button type="submit" name="update" class="btn btn-success">
                                                    <i class="fa fa-save"></i> Update Profile
                                                </button>
                                                <a href="change-password.php" class="btn btn-primary">
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

<?php } ?>