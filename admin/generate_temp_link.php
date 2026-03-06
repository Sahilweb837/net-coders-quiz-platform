<?php
include('../db.php');
session_start();
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
die();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the quiz ID from the form submission
    $quiz_id = $_POST['quiz_id'];
    $expire_at = date('Y-m-d H:i:s', strtotime('+6 hours')); // Set expiration time to 6 hours later

    // Generate a unique token for the link
    $token = bin2hex(random_bytes(16)); // Generates a random 32-character token

    // Insert the temporary link into the database
    $stmt = $conn->prepare("INSERT INTO temporary_links (quiz_id, token, expire_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $quiz_id, $token, $expire_at);

    if ($stmt->execute()) {
        $generated_link = "https://solitaireinfosystems.com/cstm_quiz/student/take_quiz.php?token=" . $token;
        echo "Temporary link generated successfully: <a href='$generated_link' target='_blank'>$generated_link</a>";
    } else {
        echo "Error generating link.";
    }
}
?>
