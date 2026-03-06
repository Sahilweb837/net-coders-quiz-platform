<?php
session_start();
include('../db.php');
$baseUrl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_id = $_POST['quiz_id'];
    $student_id = $_SESSION['student_id'];
    $score = 0;

    // Calculate the score
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_') === 0) {
            $question_id = str_replace('question_', '', $key);
            $value = str_replace('option_', '', $value);
            
            // Fetch correct option for each question
            $stmt = $conn->prepare("SELECT correct_option FROM questions WHERE id = ?");
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $question = $result->fetch_assoc();
            if (strtolower($question['correct_option']) === strtolower($value)) {
                $score++;
            }
        }
    }

    // Save the result
    $stmt = $conn->prepare("INSERT INTO quiz_responses (student_id, quiz_id, result) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $student_id, $quiz_id, $score);
    $stmt->execute();

    // Time carry-over
    if (isset($_POST['remaining_time'])) {
        $_SESSION['remaining_time'] = intval($_POST['remaining_time']);
    } else {
        $_SESSION['remaining_time'] = 0;
    }

    // Check if the group is complete
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $quiz = $stmt->get_result()->fetch_assoc();
    $group_id = $quiz['group_id'];

    // Fetch all quizzes in the group
    $stmt = $conn->prepare("SELECT id FROM quizzes WHERE group_id = ?");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $group_quizzes = $stmt->get_result();
    $completed = true;
    while ($row = $group_quizzes->fetch_assoc()) {
        $stmt = $conn->prepare("SELECT * FROM quiz_responses WHERE student_id = ? AND quiz_id = ?");
        $stmt->bind_param("ii", $student_id, $row['id']);
        $stmt->execute();
        $response = $stmt->get_result();
        if ($response->num_rows === 0) {
            $completed = false;
            break;
        }
    }

    // If the group is complete, mark it as completed
    if ($completed) {
        $_SESSION['group_completed'] = $group_id;
    }

    // Redirect to the next quiz or dashboard
    $stmt = $conn->prepare("SELECT id FROM quizzes WHERE id > ? ORDER BY id ASC LIMIT 1");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $next_quiz = $stmt->get_result()->fetch_assoc();

    if ($next_quiz) {
        header("Location: ". $baseUrl."/cstm_quiz/student/final_quiz.php?quiz_id=" . $next_quiz['id']);
    } else {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Quiz Completed</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    background-color: #f8f9fa;
                      background-image: url(Quizizz-test-1.png);
                     background-repeat: no-repeat;
                     background-size: cover;
                }
                .modal-content {
                    border-radius: 15px;
                }
                .modal-header, .modal-footer {
                    border: none;
                }
            </style>
        </head>
        <body>
            <div class="modal fade show" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" style="display: block; background: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center">
                        <div class="modal-header justify-content-center">
                            <h5 class="modal-title text-success" id="successModalLabel">Success!</h5>
                        </div>
                        <div class="modal-body">
                            <p class="lead">You have successfully completed the test!</p>
                            <p>Redirecting you to the dashboard in <span id="redirect-timer">3</span> seconds...</p>
                        </div>
                        <div class="modal-footer justify-content-center" style="display:none">
                            <a href="'.$baseUrl.'/cstm_quiz/tech.php" class="btn btn-primary">Go to Tech Page Now</a>
                        </div>
                    </div>
                </div>
            </div>
        
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                let countdown = 3;
                const timerElement = document.getElementById("redirect-timer");
                const interval = setInterval(() => {
                    countdown--;
                    timerElement.textContent = countdown;
                    if (countdown <= 0) {
                        clearInterval(interval);
                        window.location.href = "'.$baseUrl.'/cstm_quiz/tech.php";
                    }
                }, 1000);
            </script>
        </body>
        </html>';
    }
    exit;
}
?>
