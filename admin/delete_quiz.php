<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.html");
    exit();
}

include('../db.php');

if (isset($_GET['id'])) {
    $quiz_id = $_GET['id'];

    // First, delete all questions associated with the quiz
    $delete_questions_sql = "DELETE FROM questions WHERE quiz_id = ?";
    $stmt = $conn->prepare($delete_questions_sql);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();

    // Then, delete the quiz itself
    $delete_quiz_sql = "DELETE FROM quizzes WHERE id = ?";
    $stmt = $conn->prepare($delete_quiz_sql);
    $stmt->bind_param("i", $quiz_id);
    
    if ($stmt->execute()) {
        echo "Quiz and its questions have been deleted successfully.";
        header("Location: manage_quizzes.php");
        exit();
    } else {
        echo "Error deleting quiz: " . $conn->error;
    }
} else {
    echo "No quiz ID provided!";
}

$conn->close();
?>
