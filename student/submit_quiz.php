<?php
ob_start();
session_start();
require_once('../db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit;
}

$baseUrl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'];
$errorMessages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_id = intval($_POST['quiz_id']);
    $student_id = intval($_SESSION['student_id']);

    // ❌ Agar already submitted → sidha message
    $stmt = $conn->prepare("SELECT id FROM quiz_results WHERE student_id = ? AND quiz_id = ?");
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        showAlreadySubmittedPage($baseUrl);
    }

    $score = 0;

    try {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'question_') === 0) {
                $question_id = intval(str_replace('question_', '', $key));
                $selected_option = str_replace('option_', '', $value);

                $stmt = $conn->prepare("SELECT correct_option FROM questions WHERE id = ?");
                $stmt->bind_param("i", $question_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $question = $result->fetch_assoc();

                if ($question) {
                    $correct_option = $question['correct_option'];
                    if (strtolower($correct_option) === strtolower($selected_option)) {
                        $score++;
                    }

                    $stmt2 = $conn->prepare("INSERT INTO quiz_responses 
                        (student_id, quiz_id, question_id, selected_option, answer) 
                        VALUES (?, ?, ?, ?, ?)");
                    $stmt2->bind_param("iiiss", $student_id, $quiz_id, $question_id, $selected_option, $correct_option);
                    $stmt2->execute();
                }
            }
        }

        // ✅ Final score save
        $stmt = $conn->prepare("INSERT INTO quiz_results (student_id, quiz_id, result) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $student_id, $quiz_id, $score);
        $stmt->execute();

        // ✅ Thank you page
        showThankYouPage($errorMessages, $baseUrl);

    } catch (Exception $e) {
        $errorMessages[] = "Unexpected error: " . $e->getMessage();
        showThankYouPage($errorMessages, $baseUrl);
    }
}

ob_end_flush();

/* ---------- FUNCTIONS ---------- */
function showThankYouPage($errors, $baseUrl) { ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Quiz Completed</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:#302b63;color:white;">
        <div class="p-5 rounded shadow text-center" style="background:rgba(0,0,0,0.6);max-width:600px;">
            <h3>✅ Quiz Completed!</h3>
            <p>Your responses have been saved successfully.</p>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-warning text-start">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <p class="mt-3">Redirecting in <span id="timer">5</span> seconds...</p>
            <a href="<?= $baseUrl ?>/cstm_quiz/student/index.php" class="btn btn-primary mt-3">Go to Dashboard</a>
        </div>
        <script>
            let t=5, el=document.getElementById('timer');
            setInterval(()=>{ t--; el.textContent=t; if(t<=0){ window.location.href="<?= $baseUrl ?>/cstm_quiz/student/index.php";}},1000);
        </script>
    </body>
    </html>
<?php exit; }

function showAlreadySubmittedPage($baseUrl) { ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Already Attempted</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:#24243e;color:white;">
        <div class="p-5 rounded shadow text-center" style="background:rgba(0,0,0,0.6);max-width:600px;">
            <h3>⚠️ Quiz Already Submitted</h3>
            <a href="<?= $baseUrl ?>/cstm_quiz/student/index.php" class="btn btn-primary mt-3">Go to Dashboard</a>
        </div>
    </body>
    </html>
<?php exit; }
