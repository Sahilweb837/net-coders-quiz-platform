<?php
session_start();
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.html");
//     exit();
// }
include('../db.php');

if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];

    // Fetch quiz details
    $quiz_sql = "SELECT title FROM quizzes WHERE id = ?";
    $stmt = $conn->prepare($quiz_sql);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $quiz_result = $stmt->get_result();
    $quiz = $quiz_result->fetch_assoc();

    // Handle CRUD operations
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_question'])) {
            // Add Question
            $question_text = $_POST['question_text'];
            $option_a = $_POST['option_a'];
            $option_b = $_POST['option_b'];
            $option_c = $_POST['option_c'];
            $option_d = $_POST['option_d'];
            $correct_option = $_POST['correct_option'];

            $sql = "INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssss", $quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);

            if ($stmt->execute()) {
                echo "Question added successfully!";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }

    // Handle delete operation
    if (isset($_GET['delete_question_id'])) {
        $delete_question_id = $_GET['delete_question_id'];
        $delete_sql = "DELETE FROM questions WHERE id = ? AND quiz_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("ii", $delete_question_id, $quiz_id);

        if ($stmt->execute()) {
            echo "Question deleted successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Handle edit operation
    if (isset($_GET['edit_question_id'])) {
        $edit_question_id = $_GET['edit_question_id'];
        $get_question_sql = "SELECT * FROM questions WHERE id = ? AND quiz_id = ?";
        $stmt = $conn->prepare($get_question_sql);
        $stmt->bind_param("ii", $edit_question_id, $quiz_id);
        $stmt->execute();
        $question = $stmt->get_result()->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_question'])) {
            // Edit Question
            $question_text = $_POST['question_text'];
            $option_a = $_POST['option_a'];
            $option_b = $_POST['option_b'];
            $option_c = $_POST['option_c'];
            $option_d = $_POST['option_d'];
            $correct_option = $_POST['correct_option'];

            $update_sql = "UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE id = ? AND quiz_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssssii", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option, $edit_question_id, $quiz_id);

            if ($stmt->execute()) {
                echo "Question updated successfully!";
                header("Location: add_questions.php?quiz_id=$quiz_id");
                exit();
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
} else {
    header("Location: manage_quizzes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions for Quiz</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
        }
        .modal-header, .modal-body, .modal-footer {
            margin-bottom: 15px;
        }
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        button {
            padding: 10px 20px;
            cursor: pointer;
        }
        .question {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<header>
    <img src="../assests/register_form_latest.png" alt="Solitaire Infosystems Logo" class="logo">
</header>
<div class="add-question">
    <h1>Manage Questions for Test: <?= htmlspecialchars($quiz['title']) ?></h1>

    <!-- Add Question Form -->
    <h2>Add New Question</h2>
    <form action="" method="POST">
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

        <button type="submit" name="add_question">Add Question</button>
    </form>

    <!-- List Existing Questions -->
    <h2>Existing Questions</h2>
    <?php
    $questions_sql = "SELECT * FROM questions WHERE quiz_id = ?";
    $stmt = $conn->prepare($questions_sql);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();

    while ($question = $questions_result->fetch_assoc()) {
        echo "<div class='question'>
                <h3>Question: " . htmlspecialchars($question['question_text']) . "</h3>
                <p>Option A: " . htmlspecialchars($question['option_a']) . "</p>
                <p>Option B: " . htmlspecialchars($question['option_b']) . "</p>
                <p>Option C: " . htmlspecialchars($question['option_c']) . "</p>
                <p>Option D: " . htmlspecialchars($question['option_d']) . "</p>
                <p>Correct Option: " . htmlspecialchars($question['correct_option']) . "</p>
                <a href='#' onclick='openEditModal(" . $question['id'] . ", \"" . htmlspecialchars($question['question_text']) . "\", \"" . htmlspecialchars($question['option_a']) . "\", \"" . htmlspecialchars($question['option_b']) . "\", \"" . htmlspecialchars($question['option_c']) . "\", \"" . htmlspecialchars($question['option_d']) . "\", \"" . htmlspecialchars($question['correct_option']) . "\")'>Edit</a> | 
                <a href='#' onclick='openDeleteModal(" . $question['id'] . ")'>Delete</a>
              </div>";
    }
    ?>
</div>

<!-- Modal for Editing Question -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Question</h2>
        </div>
        <div class="modal-body">
            <form action="" method="POST">
                <input type="hidden" id="edit_question_id" name="edit_question_id">
                <label for="edit_question_text">Question:</label>
                <textarea id="edit_question_text" name="question_text" rows="4" required></textarea>

                <label for="edit_option_a">Option A:</label>
                <input type="text" id="edit_option_a" name="option_a" required>

                <label for="edit_option_b">Option B:</label>
                <input type="text" id="edit_option_b" name="option_b" required>

                <label for="edit_option_c">Option C:</label>
                <input type="text" id="edit_option_c" name="option_c" required>

                <label for="edit_option_d">Option D:</label>
                <input type="text" id="edit_option_d" name="option_d" required>

                <label for="edit_correct_option">Correct Option:</label>
                <select id="edit_correct_option" name="correct_option" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>

                <button type="submit" name="edit_question">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Delete Confirmation -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Delete Question</h2>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this question?</p>
            <button id="confirmDeleteButton" onclick="deleteQuestion()">Yes</button>
            <button onclick="closeModal()">No</button>
        </div>
    </div>
</div>

<script>
    // Open Edit Modal and fill in data
    function openEditModal(id, question, option_a, option_b, option_c, option_d, correct_option) {
        document.getElementById('edit_question_id').value = id;
        document.getElementById('edit_question_text').value = question;
        document.getElementById('edit_option_a').value = option_a;
        document.getElementById('edit_option_b').value = option_b;
        document.getElementById('edit_option_c').value = option_c;
        document.getElementById('edit_option_d').value = option_d;
        document.getElementById('edit_correct_option').value = correct_option;
        document.getElementById('editModal').style.display = 'flex';
    }

    // Open Delete Modal
    function openDeleteModal(id) {
        document.getElementById('confirmDeleteButton').setAttribute('data-id', id);
        document.getElementById('deleteModal').style.display = 'flex';
    }

    // Delete question
    function deleteQuestion() {
        const questionId = document.getElementById('confirmDeleteButton').getAttribute('data-id');
        window.location.href = "?delete_question_id=" + questionId;
    }

    // Close modal
    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
        document.getElementById('deleteModal').style.display = 'none';
    }
</script>
</body>
</html>
 