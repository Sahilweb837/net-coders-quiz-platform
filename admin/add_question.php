<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<header>
    <img src="../assests/register_form_latest.png" alt="Solitaire Infosystems Logo" class="logo">
</header>
<div class="add-question">
    <h1>Add a New Question</h1>
    <form action="save_question.php" method="POST">
        <label for="test_category">Test Category:</label>
        <select id="test_category" name="test_category" required>
            <option value="Python">Python</option>
            <option value="Java">Java</option>
            <option value="C">C</option>
            <option value="C++">C++</option>
        </select>

        <label for="question_text">Question:</label>
        <textarea id="question_text" name="question_text" rows="4" required></textarea>

        <label for="option_a">Option A:</label>
        <input type="text" id="option_a" name="option_a" required>

        <label for="option_b">Option B:</label>
        <input type="text" id="option_b" name="option_b" required>

        <label for="option_c">Option C:</label>
        <input type="text" id="option_c" name="option_c" required>

        <label for="option_d">Option D:</label>
        <input type="text" id="option_d" name="option_d" required>

        <label for="correct_option">Correct Option:</label>
        <select id="correct_option" name="correct_option" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>

        <button type="submit">Save Question</button>
    </form>
</div>
</body>
</html>
 