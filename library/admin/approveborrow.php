<?php 
session_start();
include('includes/config.php');

// Handle accept action
if (isset($_POST['approve'])) {
    $borrowId = $_POST['borrowId'];

    try {
        // Update Is_approve to 1 for the specified row
        $sql = "UPDATE tblissuedbookdetails SET Is_approve = 1 WHERE id = :borrowId";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':borrowId', $borrowId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $_SESSION['msg'] = "Borrow request approved!";
        } else {
            $_SESSION['msg'] = "Error: Could not approve the borrow request.";
        }
    } catch (PDOException $e) {
        $_SESSION['msg'] = "Error: " . $e->getMessage();
    }
}

// Handle reject action
if (isset($_POST['reject'])) {
    $borrowId = $_POST['borrowId'];

    try {
        // Update Is_approve to 0 for the specified row
        $sql = "UPDATE tblissuedbookdetails SET Is_approve = 0 WHERE id = :borrowId";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':borrowId', $borrowId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $_SESSION['msg'] = "Borrow request rejected!";
        } else {
            $_SESSION['msg'] = "Error: Could not reject the borrow request.";
        }
    } catch (PDOException $e) {
        $_SESSION['msg'] = "Error: " . $e->getMessage();
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
    <title>Online Library Management System | Approve Borrow Requests</title>
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

        .btn-action {
            margin: 0 5px;
        }
    </style>
</head>
<body>
<?php include('includes/header.php');?>

<div class="container">
    <h4 class="header-line">Manage Borrow Requests</h4>

    <?php 
    // Display success or error messages
    if (isset($_SESSION['msg'])) { ?>
        <div class="alert alert-success">
            <?php 
            echo htmlspecialchars($_SESSION['msg']); 
            unset($_SESSION['msg']); 
            ?>
        </div>
    <?php } ?>

    <?php if (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger">
            <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']); 
            ?>
        </div>
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
                $sql = "SELECT tblstudents.FullName, tblbooks.BookName, tblbooks.ISBNNumber, tblissuedbookdetails.IssuesDate, tblissuedbookdetails.ReturnDate, tblissuedbookdetails.id 
                        FROM tblissuedbookdetails 
                        LEFT JOIN tblstudents ON tblstudents.StudentId = tblissuedbookdetails.StudentId 
                        LEFT JOIN tblbooks ON tblbooks.id = tblissuedbookdetails.BookId 
                        WHERE tblissuedbookdetails.Is_approve IS NULL
                        ORDER BY tblissuedbookdetails.IssuesDate DESC";
                $stmt = $dbh->prepare($sql);
                $stmt->execute();
                $requests = $stmt->fetchAll(PDO::FETCH_OBJ);
                $cnt = 1;

                foreach ($requests as $request) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cnt); ?></td>
                        <td><?php echo htmlspecialchars($request->FullName); ?></td>
                        <td><?php echo htmlspecialchars($request->BookName); ?></td>
                        <td><?php echo htmlspecialchars($request->ISBNNumber); ?></td>
                        <td><?php echo htmlspecialchars($request->IssuesDate); ?></td>
                        <td><?php echo htmlspecialchars($request->ReturnDate); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="borrowId" value="<?php echo $request->id; ?>">
                                <button type="submit" name="approve" class="btn btn-success btn-action">Approve</button>
                                <button type="submit" name="reject" class="btn btn-danger btn-action">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        $cnt++; 
                    ?>  
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('includes/footer.php');?>

</body>
</html>
