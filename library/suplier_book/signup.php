<?php
session_start();
include('includes/fig.php');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Basic validation
    $errors = [];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }

    // Check password length
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long!";
    }

    // Check if email already exists
    $check_email_sql = "SELECT * FROM suppliers WHERE email = :email";
    $check_stmt = $dbh->prepare($check_email_sql);
    $check_stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        $errors[] = "Email already exists!";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $sql = "INSERT INTO suppliers (name, email, password) VALUES (:name, :email, :password)";
        
        try {
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                // Set success message in session
                $_SESSION['signup_success'] = "Account created successfully!";
                
                // Redirect to login page
                echo "<script>
                    alert('Account created successfully!');
                    window.location.href = 'log_in.php';
                </script>";
                exit();
            }
        } catch(PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .signup-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }

        .signup-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .input-field {
            margin-bottom: 15px;
        }

        .input-field label {
            font-size: 14px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .input-field input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .input-field input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .input-field input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign up</h2>
        
        <?php
        // Display errors if any
        if (!empty($errors)) {
            echo '<div class="error-message">';
            foreach ($errors as $error) {
                echo '<p>' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
        ?>

        <form method="POST" action="">
            <div class="input-field">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" placeholder="Enter your full name" required 
                       value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
            </div>
            <div class="input-field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required
                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
            </div>
            <div class="input-field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Create a password" required>
            </div>
            <div class="input-field">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
            </div>
            <div class="input-field">
                <input type="submit" value="Create Account">
            </div>
        </form>
        <div class="login-link">
            Already have an account? <a href="log_in.php">Login here</a>
        </div>
    </div>
</body>
</html>