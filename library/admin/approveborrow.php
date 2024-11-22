<?php 
session_start();
include('includes/config.php');

// Handle accept or reject action
if (isset($_POST['approve'])) {
    $borrowId = $_POST['borrowId'];
    $action = 'accepted'; // Action to approve

    // Fetch the borrow request details first
    $sql = "SELECT * FROM tblborrowbook WHERE id = :borrowId";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':borrowId', $borrowId, PDO::PARAM_INT);
    $stmt->execute();
    $request = $stmt->fetch(PDO::FETCH_OBJ);

    // If the borrow request exists, move to tblissuedbookdetails
    if ($request) {
        // Prepare the insert query
        $sql = "INSERT INTO tblissuedbookdetails (BookId, StudentID, IssuesDate, ReturnDate, RetrunStatus) 
                VALUES (:BookId, :StudentID, :IssuesDate, :ReturnDate, :RetrunStatus)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':BookId', $request->book_id);  // Ensure this matches column in tblborrowbook
        $stmt->bindParam(':StudentID', $request->student_id);  // Ensure this matches
        $stmt->bindParam(':IssuesDate', $request->borrow_date);
        $stmt->bindParam(':ReturnDate', $request->return_date);
        $stmt->bindParam(':RetrunStatus', $returnStatus); // This could be 'Not Returned' or similar

        // Execute insert
        if ($stmt->execute()) {
            // After inserting into issued books, delete from tblborrowbook
            $sql = "DELETE FROM tblborrowbook WHERE id = :borrowId";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':borrowId', $borrowId, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['msg'] = "Borrow request accepted and moved to Issued Books!";
        } else {
            $_SESSION['msg'] = "Error: Failed to issue the book.";
        }
    } else {
        $_SESSION['msg'] = "Error: Borrow request not found.";
    }
}

if (isset($_POST['reject'])) {
    $borrowId = $_POST['borrowId'];
    $action = 'rejected'; // Action to reject

    // Delete the rejected borrow request
    $sql = "DELETE FROM tblborrowbook WHERE id = :borrowId";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':borrowId', $borrowId, PDO::PARAM_INT);
    $stmt->execute();
    
    $_SESSION['msg'] = "Borrow request rejected and deleted!";
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
        .table-box {
            border: 2px solid #ccc;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .table-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-box th, .table-box td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .table-box th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<?php include('includes/header.php');?>

<div class="container">
    <h4 class="header-line">Manage Borrow Requests</h4>

    <?php if (isset($_SESSION['msg'])) { ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['msg']; ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php } ?>

    <div class="table-box">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>ISBN Number</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch all borrow requests
                $sql = "SELECT * FROM tblborrowbook";
                $stmt = $dbh->prepare($sql);
                $stmt->execute();
                $requests = $stmt->fetchAll(PDO::FETCH_OBJ);

                foreach ($requests as $request) {
                    ?>
                    <tr>
                        <td><?php echo $request->id; ?></td>
                        <td><?php echo $request->name; ?></td>
                        <td><?php echo $request->book_title; ?></td>
                        <td><?php echo $request->isbn_number; ?></td>
                        <td><?php echo $request->borrow_date; ?></td>
                        <td><?php echo $request->return_date; ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="borrowId" value="<?php echo $request->id; ?>">
                                <button type="submit" name="approve" class="btn btn-success">Approve</button>
                                <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('includes/footer.php');?>

<script src="assets/js/jquery-1.10.2.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/custom.js"></script>

</body>
</html>
