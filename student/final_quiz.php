<?php
session_start();
include('../db.php'); // Include database connection file

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {     
    header("Location: ../index.php"); // Redirect to login if not logged in
    exit; 
}

$baseUrl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'];
$student_id = $_SESSION['student_id'];
$quiz_id = $_GET['quiz_id'] ?? null;

// Fetch quiz details for "Psychometric Test" only
$quiz = $conn->query("SELECT * FROM quizzes WHERE id = $quiz_id AND status = 'published' AND title = 'Psychometric Test'")->fetch_assoc();

// Check if the quiz exists and is "Psychometric Test"
if (!$quiz) {
    header("Location: tech.php"); // Redirect if the quiz is not found or not the Psychometric Test
    exit;
}

// Fetch questions for the quiz
$questions = $conn->query("SELECT * FROM questions WHERE quiz_id = $quiz_id ORDER BY id ASC");

// Check if the student has already attempted this quiz
$response_check = $conn->query("SELECT * FROM quiz_responses WHERE student_id = {$student_id} AND quiz_id = {$quiz_id} LIMIT 1");
if ($response_check->num_rows > 0) {
    echo "<div class='alert alert-danger'>You have already attempted this test.</div>";
    exit;
}

// Set the remaining time for the quiz (e.g., 1 minute = 60 seconds)
$remaining_time_seconds = 300; // Adjust the time as needed (300 = 5 minutes)
$minutes = floor($remaining_time_seconds / 60);
$seconds = $remaining_time_seconds % 60;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-image: url(Quizizz-test-1.png);
            background-repeat: no-repeat;
            background-size: cover;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1565c0;
            margin-bottom: 20px;
            text-align: center;
        }

        .container {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 800px;
            margin: 40px auto;
        }

        .timer {
            font-size: 1.1rem;
            font-weight: bold;
            color: #e53935;
            text-align: right;
            margin-bottom: 10px;
        }

        .btn-primary, .btn-secondary {
            padding: 12px 20px;
            font-size: 14px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #1565c0;
            border: none;
        }

        .btn-primary:hover {
            background-color: #003c8f;
        }

        .btn-secondary {
            background-color: #90a4ae;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #607d8b;
        }

        .btn-success {
            background-color: #43a047;
            border: none;
            border-radius: 12px;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #2e7d32;
        }

        .question-card {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: none; /* Initially hide all questions */
        }

        .question-card.active {
            display: block; /* Show active question */
        }

        .question-card:hover {
             box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .form-check-label {
            font-size: 1rem;
            color: #333;
            padding-left: 10px;
            display: block;
            line-height: 1.5;
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .question-container {
            margin-top: 20px;
        }

        .question-list {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .question-list button {
            background-color: #eeeeee;
            color: #1565c0;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .question-list button.active {
            background-color: #1565c0;
            color: #ffffff;
        }

        #prevButton, #nextButton {
            width: 120px;
            padding: 12px;
        }

        #nextButton {
            margin-left: auto;
        }

        #prevButton:disabled, #nextButton:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="question-header">
            <h1><?= htmlspecialchars($quiz['title']) ?></h1>
            <div class="timer" id="timer">
                <?= $minutes ?>:<?= $seconds < 10 ? '0' : '' ?><?= $seconds ?> mins
            </div>
        </div>
        <form method="POST" action="final_submit.php">
            <div class="question-container">
                <?php 
                $i = 1; 
                while ($question = $questions->fetch_assoc()): 
                ?>
                    <div class="question-card" id="question_<?= $i ?>">
                        <h4><?= $i ?>. <?= htmlspecialchars($question['question_text']) ?></h4>
                        <?php foreach (['option_a', 'option_b', 'option_c', 'option_d'] as $option): ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="question_<?= $question['id'] ?>" value="<?= $option ?>" id="q<?= $question['id'] . $option ?>">
                                <label class="form-check-label" for="q<?= $question['id'] . $option ?>"> <?= htmlspecialchars($question[$option]) ?> </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php 
                    $i++;
                endwhile; 
                ?>
            </div>
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
            <input type="hidden" name="student_id" value="<?= $student_id ?>">
            <input type="hidden" name="remaining_time" id="remaining_time">
            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="prevButton" class="btn btn-secondary">Previous</button>
                <button type="button" id="nextButton" class="btn btn-primary">Next</button>
                <button type="submit" class="btn btn-success">Submit Test</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentQuestionIndex = 0;
            const questions = document.querySelectorAll('.question-card');
            const totalQuestions = questions.length;

            // Show the last question by default
            showQuestion(currentQuestionIndex);

            // Timer countdown
            let remainingTime = 300; // 5 minutes in seconds
            const timerElement = document.getElementById('timer');
            setInterval(function() {
                if (remainingTime > 0) {
                    remainingTime--;
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;
                    timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds} mins`;
                }
            }, 1000);

            // Function to show the active question
            function showQuestion(index) {
                questions.forEach((question, i) => {
                    question.classList.remove('active');
                    if (i === index) {
                        question.classList.add('active');
                    }
                });
                updateQuestionNavigation();
            }

            // Update the navigation buttons based on current question
            function updateQuestionNavigation() {
                const prevButton = document.getElementById('prevButton');
                const nextButton = document.getElementById('nextButton');
                prevButton.disabled = currentQuestionIndex === 0;
                nextButton.disabled = currentQuestionIndex === totalQuestions - 1;
            }

            // Event listeners for navigation buttons
            document.getElementById('prevButton').addEventListener('click', function() {
                if (currentQuestionIndex > 0) {
                    currentQuestionIndex--;
                    showQuestion(currentQuestionIndex);
                }
            });

            document.getElementById('nextButton').addEventListener('click', function() {
                if (currentQuestionIndex < totalQuestions - 1) {
                    currentQuestionIndex++;
                    showQuestion(currentQuestionIndex);
                }
            });
        });
    </script>
</body>

</html>
