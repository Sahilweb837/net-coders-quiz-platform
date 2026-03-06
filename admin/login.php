<?php
session_start();
include('../db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (md5($inputPassword) == $row['password']) {
            
            $_SESSION['admin'] = $inputUsername;
            header("Location: http://localhost/cstm_quiz/admin/dashboard.php");
            exit();
        } else {
         echo   $error = "Invalid credentials. Please try again.";
             //header("Location: https://solitaireinfosystems.com/cstm_quiz/admin");
        }
    } else {
       echo  $error = "Invalid credentials. Please try again.";
         //header("Location: https://solitaireinfosystems.com/cstm_quiz/admin");
    }
}
?>
