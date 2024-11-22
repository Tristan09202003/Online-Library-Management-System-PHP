<?php
session_start(); 
// Include database connection
include('includes/config.php');

// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Collect and sanitize form data
    $studentId = $_POST['studentId'];
    $studentName = $_POST['studentName'];
    $isbn = $_POST['isbn'];
    $bookTitle = $_POST['bookTitle']; // Added bookTitle
    $borrowDate = $_POST['borrowDate'];
    $returnDate = $_POST['returnDate'];

    // Prepare SQL query
    $sql = "INSERT INTO tblborrowbook (studentId, name, isbn_number, book_title, borrow_date, return_date) 
            VALUES (:studentId, :studentName, :isbn, :bookTitle, :borrowDate, :returnDate)"; // Updated query

    try {
        // Prepare statement and bind parameters with types
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':studentId', $studentId, PDO::PARAM_INT);
        $stmt->bindParam(':studentName', $studentName, PDO::PARAM_STR);
        $stmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->bindParam(':bookTitle', $bookTitle, PDO::PARAM_STR); // Bind bookTitle
        $stmt->bindParam(':borrowDate', $borrowDate, PDO::PARAM_STR);
        $stmt->bindParam(':returnDate', $returnDate, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            $_SESSION['msg'] = "Book borrowed successfully!";
            header('Location: borrowbook.php'); // Redirect back to the form with success
            exit; // Ensure the script stops after redirection
        } else {
            $_SESSION['error'] = "Error borrowing book. Please try again.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Borrow Book</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <style>
        /* Style for the box */
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }

        /* Style for form elements */
        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php');?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row pad-botm">
                <div class="col-md-12">
                    <h4 class="header-line">Borrow Book Form</h4>
                </div>
            </div>
            <div class="form-container">
                <!-- Display success or error message -->
                <?php if (isset($_SESSION['msg'])) { ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['msg']; ?>
                    </div>
                <?php unset($_SESSION['msg']); } ?>
                <?php if (isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; ?>
                    </div>
                <?php unset($_SESSION['error']); } ?>

                <form action="borrowbook.php" method="POST">
                    <label for="studentId">Student ID:</label>
                    <input type="text" id="studentId" name="studentId" required>

                    <label for="studentName">Student Name:</label>
                    <input type="text" id="studentName" name="studentName" required>

                    <label for="bookTitle">Book Title:</label>
                    <input type="text" id="bookTitle" name="bookTitle" required> <!-- Added field for book title -->

                    <label for="isbn">ISBN Number:</label>
                    <input type="text" id="isbn" name="isbn" required>

                    <label for="borrowDate">Borrow Date:</label>
                    <input type="date" id="borrowDate" name="borrowDate" required>

                    <label for="returnDate">Return Date:</label>
                    <input type="date" id="returnDate" name="returnDate" required>

                    <button type="submit" name="submit">Borrow a Book</button>
                </form>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php');?>

    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
