<?php
session_start();
include('includes/fig.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

// Define upload directory
$targetDir = "uploads/";

// Create uploads directory if it doesn't exist
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookName = $_POST['bookName'];
    $categoryName = $_POST['category_name'];
    $authorName = $_POST['author_name'];
    $isbn = $_POST['isbn'];
    
    try {
        $dbh->beginTransaction();
        
        // Insert new category if provided
        if (!empty($categoryName)) {
            $catSql = "INSERT INTO tblcategory (CategoryName, Status, CreationDate) 
                       VALUES (:catName, 1, NOW())";
            $catStmt = $dbh->prepare($catSql);
            $catStmt->bindParam(':catName', $categoryName);
            $catStmt->execute();
            $categoryId = $dbh->lastInsertId();
        }
        
        // Insert new author if provided
        if (!empty($authorName)) {
            $authorSql = "INSERT INTO tblauthors (AuthorName, CreationDate) 
                         VALUES (:authorName, NOW())";
            $authorStmt = $dbh->prepare($authorSql);
            $authorStmt->bindParam(':authorName', $authorName);
            $authorStmt->execute();
            $authorId = $dbh->lastInsertId();
        }
        
        // Handle file upload
        $bookImage = "";
        
        if(isset($_FILES["bookImage"]) && $_FILES["bookImage"]["error"] == 0) {
            // Get file extension
            $imageFileType = strtolower(pathinfo($_FILES["bookImage"]["name"], PATHINFO_EXTENSION));
            
            // Generate unique filename using ISBN
            $newFileName = $isbn . '.' . $imageFileType;
            $targetFile = $targetDir . $newFileName;
            
            // Check file type
            if($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png") {
                // Attempt to move the file
                if (move_uploaded_file($_FILES["bookImage"]["tmp_name"], $targetFile)) {
                    $bookImage = $newFileName;
                } else {
                    throw new Exception("Failed to upload image. Please check directory permissions.");
                }
            } else {
                throw new Exception("Only JPG, JPEG & PNG files are allowed.");
            }
        }

        // Insert into tblbooks
        $sql = "INSERT INTO tblbooks (BookName, CatId, AuthorId, ISBNNumber, bookImage, RegDate) 
                VALUES (:bookName, :catId, :authorId, :isbn, :bookImage, NOW())";
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':bookName', $bookName);
        $stmt->bindParam(':catId', $categoryId);
        $stmt->bindParam(':authorId', $authorId);
        $stmt->bindParam(':isbn', $isbn);
        $stmt->bindParam(':bookImage', $bookImage);
        
        $stmt->execute();
        $dbh->commit();
        
        echo "<script>alert('Book added successfully!');</script>";
    } catch(Exception $e) {
        $dbh->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
            
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 3%;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="file"] {
            padding: 10px 0;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #666;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #555;
            background-color: #45a049;
        }
    </style>
</head>
<a href="dashboard.php" class="back-button">← </a>
<body>
    <div class="container">
        
        <h2>Add New Book</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="bookName">Book Name</label>
                <input type="text" id="bookName" name="bookName" required>
            </div>

            <div class="form-group">
                <label for="category_name">Category Name</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>

            <div class="form-group">
                <label for="author_name">Author Name</label>
                <input type="text" id="author_name" name="author_name" required>
            </div>

            <div class="form-group">
                <label for="isbn">ISBN Number</label>
                <input type="text" id="isbn" name="isbn" required>
            </div>

            <div class="form-group">
                <label for="bookImage">Book Image</label>
                <input type="file" id="bookImage" name="bookImage" accept=".jpg,.jpeg,.png" required>
            </div>

            <button type="submit">Add Book</button>
        </form>
    </div>
</body>
</html>