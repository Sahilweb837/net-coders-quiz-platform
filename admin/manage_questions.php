<?php
session_start();
 

include('../db.php');

$sql = "SELECT * FROM questions";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<header>
            <img src="../assests/register_form_latest.png" alt="Solitaire Infosystems Logo" class="logo">
        </header>
    <div class="manage-questions">
        <h1>Manage Questions</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Option A</th>
                <th>Option B</th>
                <th>Option C</th>
                <th>Option D</th>
                <th>Correct</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['question_text'] ?></td>
                    <td><?= $row['option_a'] ?></td>
                    <td><?= $row['option_b'] ?></td>
                    <td><?= $row['option_c'] ?></td>
                    <td><?= $row['option_d'] ?></td>
                    <td><?= $row['correct_option'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
