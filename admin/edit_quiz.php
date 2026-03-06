<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.html");
    exit();
}

include('../db.php');

// Fetch existing quiz details
if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
    $sql = "SELECT * FROM quizzes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $quiz = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $time_limit = $_POST['time_limit']; // Get the time limit (in seconds)

    $sql = "UPDATE quizzes SET title = ?, description = ?, time_limit = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $title, $description, $time_limit, $quiz_id);

    if ($stmt->execute()) {
        echo "Quiz updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="add-quiz">
        <h1>Edit Quiz</h1>
        <form action="" method="POST">
            <label for="title">Quiz Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($quiz['title']) ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($quiz['description']) ?></textarea>

            <label for="time_limit">Time Limit (in seconds):</label>
            <input type="number" id="time_limit" name="time_limit" value="<?= htmlspecialchars($quiz['time_limit']) ?>" min="60" required>

            <button type="submit">Update Quiz</button>
        </form>
    </div>                                                                                                                                          
</body>
</html>
