<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

// Get user name from session
$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .logout-btn {
            background-color: white;
            color: #4CAF50;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-btn:hover {
            background-color: #f0f0f0;
        }

        .dashboard-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }

        .welcome-message {
            text-align: center;
            margin-bottom: 40px;
        }

        .buttons-container {
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        .dashboard-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: #333;
            width: 300px;
            height: 150px;
            transition: transform 0.2s;
            margin-top: 3%;
        }

        .dashboard-button:hover {
            transform: translateY(-20px);
        }

        .button-icon {
            font-size: 60px;
            margin-bottom: 15px;
            color: #4CAF50;
        }

        .button-text {
            font-size: 18px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Supplier Dashboard</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-container">
        <div class="welcome-message">
            <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <p>What would you like to do today?</p>
        </div>

        <div class="buttons-container">
            <a href="add_book.php" class="dashboard-button">
                <div class="button-icon">📚</div>
                <div class="button-text">Add New Book</div>
            </a>
            <a href="manage_books.php" class="dashboard-button">
                <div class="button-icon">📋</div>
                <div class="button-text">Manage Books</div>
            </a>
        </div>
    </div>
</body>
</html>