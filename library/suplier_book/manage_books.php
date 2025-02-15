<?php
session_start();
include('includes/fig.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

// Number of records per page
$records_per_page = 5;

// Get current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = '';
if (!empty($search)) {
    $search_condition = " WHERE BookName LIKE :search 
                         OR ISBNNumber LIKE :search 
                         OR tblcategory.CategoryName LIKE :search 
                         OR tblauthors.AuthorName LIKE :search";
}

// Get total number of records for pagination
$count_query = "SELECT COUNT(*) as total FROM tblbooks 
                LEFT JOIN tblcategory ON tblcategory.id = tblbooks.CatId
                LEFT JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId" . $search_condition;

$count_stmt = $dbh->prepare($count_query);
if (!empty($search)) {
    $search_param = "%$search%";
    $count_stmt->bindParam(':search', $search_param);
}
$count_stmt->execute();
$total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get books with pagination and search
$query = "SELECT tblbooks.*, tblcategory.CategoryName, tblauthors.AuthorName 
          FROM tblbooks 
          LEFT JOIN tblcategory ON tblcategory.id = tblbooks.CatId
          LEFT JOIN tblauthors ON tblauthors.id = tblbooks.AuthorId" .
    $search_condition .
    " ORDER BY tblbooks.id ASC 
          LIMIT :start, :records_per_page";

$stmt = $dbh->prepare($query);
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
}
$stmt->bindParam(':start', $start_from, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $book_id = $_GET['id'];

    try {
        // First, get the book image filename
        $imageQuery = "SELECT bookImage FROM tblbooks WHERE id = :book_id";
        $imageStmt = $dbh->prepare($imageQuery);
        $imageStmt->bindParam(':book_id', $book_id);
        $imageStmt->execute();
        $book = $imageStmt->fetch(PDO::FETCH_ASSOC);

        $dbh->beginTransaction();

        // Delete the book record
        $sql = "DELETE FROM tblbooks WHERE id = :book_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':book_id', $book_id);
        $stmt->execute();

        // If there was an image, delete it from the uploads directory
        if (!empty($book['bookImage'])) {
            $imagePath = "uploads/" . $book['bookImage'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $dbh->commit();
        echo "<script>alert('Book deleted successfully.'); window.location.href='manage_books.php';</script>";
    } catch (Exception $e) {
        $dbh->rollBack();
        echo "<script>alert('Error deleting book: " . $e->getMessage() . "'); location.reload();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
            padding-top: 5%;
        }

        .button {
            position: absolute;
            top: 20px;
            left: 20px;
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
            background-color: #4CAF50;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-box {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-container {
            position: relative;
            display: inline-block;
        }

        .search-box input {
            padding: 10px;
            width: 400px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
            padding-right: 30px;
        }

        .clear-search {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            padding-left: 50%;
            display: none;
        }

        .clear-search:hover {
            color: #333;

        }

        .search-box button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-box button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }


        .book-image {
            width: 100px;
            height: 120px;
            object-fit: cover;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }

        .pagination a:hover {
            background-color: #45a049;
        }

        .pagination .active {
            background-color: #45a049;
            font-weight: bold;
        }

        .pagination .disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            align-items: center;
            justify-content: center;

        }

        .action-buttons a {
            padding: 7px 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;

        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .status-active {
            background-color:rgb(184, 255, 17);
            color: white;
        }

        .status-inactive {
            background-color: #f44336;
            color: white;
        }

        .edit-btn {
            background-color: #4CAF50;
        }

        .delete-btn {
            background-color: #f44336;
        }
    </style>
</head>

<body>
    <div class="button">
        <a href="dashboard.php" class="back-button">← </a>
    </div>

    <div class="container">
        <h2>Manage Books</h2>

        <!-- Search Box -->
        <div class="search-box">
            <form method="GET" action="" id="searchForm">
                <div class="search-container">
                    <input type="text"
                        name="search"
                        id="searchInput"
                        placeholder="Search by Book Name, ISBN, Category, or Author"
                        value="<?php echo htmlspecialchars($search); ?>">
                    <i class="fas fa-times clear-search" id="clearSearch" title="Clear search"></i>
                </div>
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Books Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Book Image</th>
                    <th>Book Name</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>ISBN</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = ($page - 1) * $records_per_page + 1;
                foreach ($books as $book):
                    $status_class = $book['status'] == 'active' ? 'status-active' : 'status-inactive';
                    $status_text = $book['status'] == 'active' ? 'Active' : 'Inactive';
                ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td>
                            <?php if (!empty($book['bookImage'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($book['bookImage']); ?>"
                                    alt="Book Cover" class="book-image">
                            <?php else: ?>
                                <span>No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($book['BookName']); ?></td>
                        <td><?php echo htmlspecialchars($book['CategoryName']); ?></td>
                        <td><?php echo htmlspecialchars($book['AuthorName']); ?></td>
                        <td><?php echo htmlspecialchars($book['ISBNNumber']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit.php?id=<?php echo $book['id']; ?>" class="edit-btn">Edit</a>
                                <a href="manage_books.php?action=delete&id=<?php echo $book['id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">First</a>
                <a href="?page=<?php echo ($page - 1) . (!empty($search) ? '&search=' . urlencode($search) : ''); ?>">Previous</a>
            <?php endif; ?>

            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);

            for ($i = $start_page; $i <= $end_page; $i++):
                $active_class = $i == $page ? 'active' : '';
            ?>
                <a href="?page=<?php echo $i . (!empty($search) ? '&search=' . urlencode($search) : ''); ?>"
                    class="<?php echo $active_class; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo ($page + 1) . (!empty($search) ? '&search=' . urlencode($search) : ''); ?>">Next</a>
                <a href="?page=<?php echo $total_pages . (!empty($search) ? '&search=' . urlencode($search) : ''); ?>">Last</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const clearButton = document.getElementById('clearSearch');
            const searchForm = document.getElementById('searchForm');

            // Show/hide clear button based on input content
            function toggleClearButton() {
                clearButton.style.display = searchInput.value ? 'block' : 'none';
            }

            // Initial state
            toggleClearButton();

            // Show/hide clear button when typing
            searchInput.addEventListener('input', toggleClearButton);

            // Clear search and redirect to page without search parameter
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                window.location.href = window.location.pathname; // Removes all query parameters
            });

            // Show clear button when page loads with search value
            if (searchInput.value) {
                clearButton.style.display = 'block';
            }
        });
    </script>
</body>

</html>