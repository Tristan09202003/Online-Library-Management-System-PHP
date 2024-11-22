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
        $imageName = $_FILES['fileImg']['name']; // Get uploaded image name

        // Check if a new image was uploaded
        if ($imageName) {
            $src = $_FILES['fileImg']['tmp_name'];
            $imageName = uniqid() . '_' . $imageName;  // Generate a unique image name
            $target = "assets/img/" . $imageName;  // Path to save the uploaded file
            move_uploaded_file($src, $target);  // Move the file to the img directory

            // Update query including the image column
            $sql = "UPDATE tblstudents SET FullName=:fname, MobileNumber=:mobileno, image=:image WHERE StudentId=:sid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':image', $imageName, PDO::PARAM_STR);
        } else {
            // Update query without changing the image
            $sql = "UPDATE tblstudents SET FullName=:fname, MobileNumber=:mobileno WHERE StudentId=:sid";
            $query = $dbh->prepare($sql);
        }

        // Bind other parameters
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
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Student Profile</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>
    <?php include('includes/header.php'); ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">My Profile</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 col-md-offset-1">
                    <div class="panel panel-danger">
                        <div class="panel-heading">My Profile</div>
                        <div class="panel-body">
                            <form method="post" enctype="multipart/form-data">
                                <!-- Profile Image Upload Section -->
                                <div class="form-group upload">
                                    <label>Profile Image</label>
                                    <img src="assets/img/<?php echo htmlentities($result->image) ?: 'noprofil.jpg'; ?>" id="image" width="200" height="125" />

                                    <div class="rightRound" id="upload">
                                        <input type="file" name="fileImg" id="fileImg" accept=".jpg, .jpeg, .png" />
                                        <i class="fa fa-camera"></i>
                                    </div>
                                </div>

                                <!-- Profile Status -->
                                <div class="form-group">
                                    <label>Profile Status:</label>
                                    <?php echo $result->Status == 1 ? '<span style="color: green">Active</span>' : '<span style="color: red">Blocked</span>'; ?>
                                </div>

                                <!-- Full Name -->
                                <div class="form-group">
                                    <label>Enter Full Name</label>
                                    <input class="form-control" type="text" name="fullanme" value="<?php echo htmlentities($result->FullName); ?>" required />
                                </div>

                                <!-- Mobile Number -->
                                <div class="form-group">
                                    <label>Mobile Number:</label>
                                    <input class="form-control" type="text" name="mobileno" value="<?php echo htmlentities($result->MobileNumber); ?>" required />
                                </div>

                                <!-- Email (Read-only) -->
                                <div class="form-group">
                                    <label>Enter Email</label>
                                    <input class="form-control" type="email" value="<?php echo htmlentities($result->EmailId); ?>" readonly />
                                </div>

                                <div class="form-group">
                                    <label>Student ID:</label>
                                    <?php echo htmlentities($result->StudentId); ?>
                                </div>


                                <!-- Submit Button -->
                                <button type="submit" name="update" class="btn btn-primary">Update Now</button>
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
    <script src="assets/js/profile_image.js"></script>
</body>

</html>

<?php } ?>
