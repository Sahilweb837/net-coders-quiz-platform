<?php
session_start();
include('../db.php');

$student_id = $_SESSION['student_id'];
$response = ['is_last_test' => false, 'next_test_url' => null];

// Get tests taken by student
$tests_taken = $conn->query("SELECT quiz_id FROM quiz_responses WHERE student_id = $student_id")->fetch_all(MYSQLI_ASSOC);
$taken_ids = array_column($tests_taken, 'quiz_id');

// Check if student has taken any test
if (count($taken_ids) == 0) {
    // Get first test (Aptitude)
    $first_test = $conn->query("SELECT id FROM quizzes WHERE title LIKE '%aptitude%' LIMIT 1")->fetch_assoc();
    if ($first_test) {
        $response['next_test_url'] = "quiz.php?quiz_id=".$first_test['id'];
    }
} elseif (count($taken_ids) == 1) {
    // Get second test (Critical)
    $second_test = $conn->query("SELECT id FROM quizzes WHERE title LIKE '%critical%' AND id NOT IN (".implode(',', $taken_ids).") LIMIT 1")->fetch_assoc();
    if ($second_test) {
        $response['next_test_url'] = "quiz.php?quiz_id=".$second_test['id'];
    } else {
        $response['is_last_test'] = true;
    }
} else {
    $response['is_last_test'] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
?>