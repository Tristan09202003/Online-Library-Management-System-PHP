<?php
session_start();
include('includes/fig.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

// Get book ID from URL
$book_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$book_id) {
    header("Location: manage_books.php");
    exit();
}

// Define upload directory
$targetDir = "uploads/";

// Create uploads directory if it doesn't exist
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Fetch existing book data
$bookQuery = "SELECT b.*, c.CategoryName, a.AuthorName 
              FROM tblbooks b 
              LEFT JOIN tblcategory c ON c.id = b.CatId 
              LEFT JOIN tblauthors a ON a.id = b.AuthorId 
              WHERE b.id = :book_id";
$stmt = $dbh->prepare($bookQuery);
$stmt->bindParam(':book_id', $book_id);
$stmt->execute();
$book = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookName = $_POST['bookName'];
    $categoryName = $_POST['category_name'];
    $authorName = $_POST['author_name'];
    $isbn = $_POST['isbn'];
    $status = $_POST['status'];
    
    try {
        $dbh->beginTransaction();
        
        // Update or insert new category if provided
        if (!empty($categoryName)) {
            $catSql = "INSERT INTO tblcategory (CategoryName, Status, CreationDate) 
                       VALUES (:catName, 1, NOW())
                       ON DUPLICATE KEY UPDATE CategoryName = :catName";
            $catStmt = $dbh->prepare($catSql);
            $catStmt->bindParam(':catName', $categoryName);
            $catStmt->execute();
            $categoryId = $dbh->lastInsertId() ?: $book['CatId'];
        }
        
        // Update or insert new author if provided
        if (!empty($authorName)) {
            $authorSql = "INSERT INTO tblauthors (AuthorName, CreationDate) 
                         VALUES (:authorName, NOW())
                         ON DUPLICATE KEY UPDATE AuthorName = :authorName";
            $authorStmt = $dbh->prepare($authorSql);
            $authorStmt->bindParam(':authorName', $authorName);
            $authorStmt->execute();
            $authorId = $dbh->lastInsertId() ?: $book['AuthorId'];
        }
        
        // Handle file upload
        $bookImage = $book['bookImage']; // Keep existing image by default
        
        if(isset($_FILES["bookImage"]) && $_FILES["bookImage"]["error"] == 0) {
            $imageFileType = strtolower(pathinfo($_FILES["bookImage"]["name"], PATHINFO_EXTENSION));
            $newFileName = $isbn . '.' . $imageFileType;
            $targetFile = $targetDir . $newFileName;
            
            if($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png") {
                if (move_uploaded_file($_FILES["bookImage"]["tmp_name"], $targetFile)) {
                    $bookImage = $newFileName;
                } else {
                    throw new Exception("Failed to upload image. Please check directory permissions.");
                }
            } else {
                throw new Exception("Only JPG, JPEG & PNG files are allowed.");
            }
        }

        // Update tblbooks
        $sql = "UPDATE tblbooks 
                SET BookName = :bookName, 
                    CatId = :catId, 
                    AuthorId = :authorId, 
                    ISBNNumber = :isbn, 
                    bookImage = :bookImage,
                    status = :status,
                    UpdationDate = NOW()
                WHERE id = :book_id";
        
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':bookName', $bookName);
        $stmt->bindParam(':catId', $categoryId);
        $stmt->bindParam(':authorId', $authorId);
        $stmt->bindParam(':isbn', $isbn);
        $stmt->bindParam(':bookImage', $bookImage);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':book_id', $book_id);
        
        $stmt->execute();
        $dbh->commit();
        
        echo "<script>alert('Book updated successfully!'); window.location.href='manage_books.php';</script>";
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
    <title>Edit Book</title>
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

        .current-image {
            margin-top: 10px;
            max-width: 200px;
        }
    </style>
</head>
<body>
    <a href="manage_books.php" class="back-button">← </a>
    <div class="container">
        <h2>Edit Book</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="bookName">Book Name</label>
                <input type="text" id="bookName" name="bookName" value="<?php echo htmlspecialchars($book['BookName']); ?>" 
            </div>

            <div class="form-group">
                <label for="category_name">Category Name</label>
                <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($book['CategoryName']); ?>" 
            </div>

            <div class="form-group">
                <label for="author_name">Author Name</label>
                <input type="text" id="author_name" name="author_name" value="<?php echo htmlspecialchars($book['AuthorName']); ?>" 
            </div>

            <div class="form-group">
                <label for="isbn">ISBN Number</label>
                <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($book['ISBNNumber']); ?>" 
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="active" <?php echo $book['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $book['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="bookImage">Book Image</label>
                <?php if(!empty($book['bookImage'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($book['bookImage']); ?>" alt="Current Book Image" class="current-image">
                    <p>Current image: <?php echo htmlspecialchars($book['bookImage']); ?></p>
                <?php endif; ?>
                <input type="file" id="bookImage" name="bookImage" accept=".jpg,.jpeg,.png">
            </div>

            <button type="submit">Update Book</button>
        </form>
    </div>
</body>
</html>