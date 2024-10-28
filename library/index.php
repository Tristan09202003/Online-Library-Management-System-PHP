<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | </title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

</head>
<style>

        .welcome-container {
            display: flex;
            align-items:center;
            padding: 40px;
        }

        .text-section {
            flex: 1;
            padding-left: 7%; /* Space between text and separator */
            margin-left: 5%;
            padding-bottom: 5%;
        }
        .text-section h1 {
            font-size: 30px; /* Larger font size */
            font-weight: bold; /* Bold text */
            color: whitesmoke; 
            text-align: start;
            padding-bottom: 15px;
            font-style: italic; 
        }

        .text-section p {
            font-style: italic;
            font-size: medium;
            font-size: 14px;
            color:#ccc;
            line-height: 1.5;
        }

        .separator {
            width: 1px;
            height: 500px; /* Adjust height as needed */
            background-color: #ccc; /* Line color */
        }

        .image-section {
            flex: 1;
            display: flex;
            justify-content: center;
            margin-right: 10%;
        }

        .image-section img {
            max-width: 100%; /* Responsive image */
            height: auto;
        }
    </style>
<body>
    <!------MENU SECTION START-->
<?php include('includes/header.php');?>
<!-- MENU SECTION END-->
<div class="welcome-container">
        <div class="text-section">
            <h1>Welcome Mighty Kingfisher!</h1>
            <p>This is the Online Library of Southern Leyte State University, providing and dedicated to supporting your research and learning needs,
               a user-friendly platform to explore a wealth of information anytime, anywhere.</p>
        </div>
        <div class="separator"></div>
        <div class="image-section">
            <img src="assets/img/kingfisher.png" alt="Welcome Image">
        </div>
    </div>
<!---LOGIN PABNEL END-->            
             
 
    </div>
    </div>
     <!-- CONTENT-WRAPPER SECTION END-->
 <?php include('includes/footer.php');?>
      <!-- FOOTER SECTION END-->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
      <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>

</body>
</html>
