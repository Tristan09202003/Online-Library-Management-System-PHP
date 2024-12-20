<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

if (isset($_GET['stdid'])) {
    $studentId = $_GET['stdid'];

    // Fetch user details from the database
    $sql = "SELECT * FROM tblstudents WHERE StudentId = :studentId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':studentId', $studentId, PDO::PARAM_STR);
    $query->execute();
    $userDetails = $query->fetch(PDO::FETCH_OBJ);

    if (!$userDetails) {
        echo '<script>alert("User not found."); window.location.href="reg-students.php";</script>';
        exit;
    }
} else {
    echo '<script>alert("No Student ID provided."); window.location.href="reg-students.php";</script>';
    exit;
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Manage Reg Students</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- DATATABLE STYLE  -->
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

</head>

<body>
    <?php include('includes/header.php'); ?>

    <style>
        .user-details-container {
            display: flex;
            align-items: flex-start;
            /* Align the image and details to the top */
            gap: 20px;
            /* Add space between image and details */
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            background-color: #f9f9f9;
            width: 900px;
            min-height: 400px;
            margin: 40px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding-top: 50px;
        }



        .img_container_outer img {
            width: 300px;
            /* Set the desired width */
            height: 300px;
            /* Set the desired height */
            object-fit: cover;
            /* Ensures the image scales without distortion */
            border: 2px solid #ddd;
            /* Optional border styling */
            border-color: #1898d8;
            /* Optional shadow */

        }


        .user-detail {
            flex: 1;
            /* Take up the remaining space */
            display: flex;
            flex-direction: column;
            gap: 20px;
            /* Add spacing between the details */
        }

        .inner-detail {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding-bottom: px;
        }

        .inner-detail strong {
            font-weight: bold;
            color: #333;
            font-size: medium;
        }

        .inner-detail span {
            color: #555;
            font-style: normal;
            font-size: medium;
        }

        .back-btn-container {
            padding-right: 100px;
        }
    </style>
    <div class="container">
        <div class="col-md-12">
            <h4 class="header-line">User Details</h4>
            <div class="user-details-container">
                <!-- Profile Image Section -->
                <div class="img_container_outer">
                    <img src="../assets/img/<?php echo htmlentities($userDetails->image ?: 'user2.png'); ?>" alt="Profile Image" />
                </div>
                    
                <!-- User Details Section -->
                <div class="user-detail">
                    <div class="user-info">
                        <div class="inner-detail">
                            <strong>Student ID:</strong>
                            <span><?php echo htmlentities($userDetails->StudentId); ?></span>
                        </div>
                        <div class="inner-detail">
                            <strong>Full Name:</strong>
                            <span><?php echo htmlentities($userDetails->FullName); ?></span>
                        </div>
                        <div class="inner-detail">
                            <strong>Email:</strong>
                            <span><?php echo htmlentities($userDetails->EmailId); ?></span>
                        </div>
                        <div class="inner-detail">
                            <strong>Mobile Number:</strong>
                            <span><?php echo htmlentities($userDetails->MobileNumber); ?></span>
                        </div>
                        <div class="inner-detail">
                            <strong>Registration Date:</strong>
                            <span><?php echo htmlentities($userDetails->RegDate); ?></span>
                        </div>
                        <div class="inner-detail">
                            <strong>Status:</strong>
                            <span><?php echo $userDetails->Status == 1 ? "Active" : "Blocked"; ?></span>
                        </div>

                    </div>
                    <div class="back-btn-container">
                        <a href="reg-students.php" class="btn btn-primary" class="text-light">Back to Users List</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>


    <!-- FOOTER SECTION END-->
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
    <!-- CORE JQUERY  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
    <!-- DATATABLE SCRIPTS  -->
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/profile_image.js"></script>
</body>

</html>