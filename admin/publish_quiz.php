<?php
include('../db.php');

if (isset($_POST['quiz_id']) && isset($_POST['students'])) {
    $quizId = $_POST['quiz_id'];
    $students = $_POST['students'];

    // Mark quiz as published
    $conn->query("UPDATE quizzes SET is_published = 1 WHERE id = $quizId");

    // Insert student participation in quiz
    foreach ($students as $studentId) {
        $conn->query("INSERT INTO quiz_test_participation (quiz_id, student_id) VALUES ($quizId, $studentId)");
    }

    echo "Quiz published and students assigned successfully!";
} else {
    echo "Invalid request.";
}
?>
