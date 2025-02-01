<?php
session_start();
include('includes/config.php');

// Check if the user is logged in and if we have a valid StudentId
if (isset($_SESSION['stdid'])) {
    // Get the logged-in student's ID
    $studentId = $_SESSION['stdid'];

    try {
        // Prepare the SQL query to fetch the notification for the logged-in user
        $sql = "SELECT Notification FROM tblstudents WHERE StudentId = :studentId";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':studentId', $studentId, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        // Check if there is a notification
        if ($result && !empty($result->Notification)) {
            // Display the notification
            echo "<p>" . htmlspecialchars($result->Notification) . "</p>";

            // After displaying, clear the notification from the database
            // Reset the notification (optional, if you want to clear after viewing)
            $sqlUpdate = "UPDATE tblstudents SET Notification = '' WHERE StudentId = :studentId";
            $stmtUpdate = $dbh->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':studentId', $studentId, PDO::PARAM_INT);
            $stmtUpdate->execute(); // Execute the update to reset the notification
        } else {
            echo "<p>No new notifications for this user.</p>";
        }
    } catch (PDOException $e) {
        // Catch any PDO errors and display them
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<p>User not logged in. Please log in first.</p>";
}
?>
