<?php
session_start();
include('../db.php'); // Ensure this file connects to the correct database

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Hardcoded admin credentials for simplicity (username = admin, password = 1234)
    if ($inputUsername === 'admin' && $inputPassword === '1234') {
        // Start session and redirect to the dashboard
        $_SESSION['admin'] = $inputUsername;
        header("Location: /cstm_quiz/admin/main.php");
        exit();
    } else {
        $error = "<div style='color:red'>Invalid credentials. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            padding: 10px;
            text-align: center;
        }

        header .logo {
            width: 200px;
        }

        .login-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }

        .error-message {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header>
        <img src="../assests/register_form_latest.png" alt="Solitaire Infosystems Logo" class="logo">
    </header>
    <div class="login-container">
        <h1>Admin Login</h1>
        <!-- Display error message -->
        <?php if (!empty($error)) : ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
 