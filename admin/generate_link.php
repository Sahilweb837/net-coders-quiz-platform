<?php
session_start();
if (!isset($_SESSION['admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id'])) {
    $quizId = intval($_POST['quiz_id']);
    $token = bin2hex(random_bytes(16)); // Generate secure token
    $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1-hour expiration

    $stmt = $conn->prepare("INSERT INTO temporary_links (quiz_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $quizId, $token, $expiresAt);

    if ($stmt->execute()) {
        $link = "http://yourdomain.com/quiz.php?token=$token";
        echo json_encode(['status' => 'success', 'link' => $link]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to generate link']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
