<?php
session_start();

// =============================================
// Configuration and Initialization
// =============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'campusedge_quiz');
define('DB_USER', 'campusedge_quiz');
define('DB_PASS', 'MLOno(DK?WKa!+pR');
define('DEBUG_MODE', true);
define('QUESTIONS_PER_PAGE', 1);

// Error reporting settings
error_reporting(DEBUG_MODE ? E_ALL : 0);
ini_set('display_errors', DEBUG_MODE ? '1' : '0');

// =============================================
// Database Connection with Error Handling
// =============================================
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("<div class='error'>System unavailable. Please try again later.<br>" . 
        (DEBUG_MODE ? $e->getMessage() : "") . "</div>");
}

// =============================================
// Security Functions
// =============================================
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validateStudentId($id) {
    return preg_match('/^\d+$/', $id) && $id > 0;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

// Function to check if student ID exists
function studentExists($conn, $student_id) {
    $stmt = $conn->prepare("SELECT id FROM Students WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Function to create a new student record
function createStudent($conn, $student_id, $name, $email, $college, $semester, $mobile) {
    $stmt = $conn->prepare("INSERT INTO Students (id, name, email, college, semester, mobile_number, technical_test_score, psychometric_test_score) VALUES (?, ?, ?, ?, ?, ?, 0.00, 0.00)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("isssis", $student_id, $name, $email, $college, $semester, $mobile);
    if (!$stmt->execute()) {
        throw new Exception("Error creating student: " . $stmt->error);
    }
}

// Function to check if quiz has been attempted
function hasAttemptedQuiz($conn, $student_id, $quiz_id, $quiz_type) {
    $stmt = $conn->prepare("SELECT id FROM quiz_attempts WHERE student_id = ? AND quiz_id = ? AND quiz_type = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iss", $student_id, $quiz_id, $quiz_type);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Function to record quiz attempt with score
function recordQuizAttempt($conn, $student_id, $quiz_id, $quiz_type, $score) {
    $stmt = $conn->prepare("INSERT INTO quiz_attempts (student_id, quiz_id, quiz_type, score, attempt_date) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("issd", $student_id, $quiz_id, $quiz_type, $score);
    if (!$stmt->execute()) {
        throw new Exception("Error recording attempt: " . $stmt->error);
    }
}

// Function to get student details
function getStudentDetails($conn, $student_id) {
    $stmt = $conn->prepare("SELECT name, email, college, semester, mobile_number, technical_test_score, psychometric_test_score FROM Students WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// =============================================
// Quiz Processing
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_registration'])) {
        // Process registration
        $student_id = sanitizeInput($_POST['student_id']);
        $name = sanitizeInput($_POST['name']);
        $email = sanitizeInput($_POST['email']);
        $college = sanitizeInput($_POST['college']);
        $semester = sanitizeInput($_POST['semester']);
        $mobile = sanitizeInput($_POST['mobile']);

        if (!validateStudentId($student_id)) {
            die("<div class='error'>Invalid student ID</div>");
        }
        if (!validateEmail($email)) {
            die("<div class='error'>Invalid email address</div>");
        }
        if (!validatePhone($mobile)) {
            die("<div class='error'>Invalid mobile number (must be 10 digits)</div>");
        }
        if (!is_numeric($semester) || $semester < 1 || $semester > 8) {
            die("<div class='error'>Invalid semester (must be between 1 and 8)</div>");
        }

        try {
            if (studentExists($conn, $student_id)) {
                die("<div class='error'>Student ID $student_id already exists</div>");
            }
            createStudent($conn, $student_id, $name, $email, $college, $semester, $mobile);
            $_SESSION['student_id'] = $student_id;
            displayQuizSelection($conn);
        } catch (Exception $e) {
            die("<div class='error'>Error registering student: " . 
                (DEBUG_MODE ? $e->getMessage() : "Please try again later") . "</div>");
        }

    } elseif (isset($_POST['submit_quiz'])) {
        // Process quiz submission
        $quiz_id = sanitizeInput($_POST['quiz_id']);
        $student_id = sanitizeInput($_POST['student_id']);
        $quiz_type = sanitizeInput($_POST['quiz_type']);
        
        if (!validateStudentId($student_id)) {
            die("<div class='error'>Invalid student ID</div>");
        }

        try {
            if (!studentExists($conn, $student_id)) {
                die("<div class='error'>Student ID $student_id does not exist. Please register first.</div>");
            }
        } catch (Exception $e) {
            die("<div class='error'>Error verifying student: " . 
                (DEBUG_MODE ? $e->getMessage() : "Please try again later") . "</div>");
        }

        try {
            $conn->begin_transaction();
            
            $table_name = ($quiz_type === 'psychometric') ? 'psychometric_questions' : 'tech_questions';
            $stmt = $conn->prepare("SELECT id FROM $table_name WHERE quiz_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("s", $quiz_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            
            if ($result === false) {
                throw new Exception("Error fetching questions: " . $conn->error);
            }
            
            $total_questions = $result->num_rows;
            $score = 0;
            
            while ($row = $result->fetch_assoc()) {
                $question_id = $row['id'];
                $user_answer = isset($_POST['question_' . $question_id]) 
                    ? sanitizeInput($_POST['question_' . $question_id]) 
                    : null;
                
                if ($quiz_type === 'psychometric') {
                    $score++;
                } else {
                    if ($total_questions <= 15) {
                        if ($question_id <= 5) {
                            $correct_answer = 'B';
                        } elseif ($question_id <= 10) {
                            $correct_answer = 'C';
                        } else {
                            $correct_answer = 'D';
                        }
                    } else {
                        $correct_answer = 'A';
                    }
                    
                    if ($user_answer && $user_answer === $correct_answer) {
                        $score++;
                    }
                }
            }
            
            $final_score = ($total_questions > 0) ? ($score / $total_questions) * 20 : 0;
            
            $column_name = ($quiz_type === 'psychometric') ? 'psychometric_test_score' : 'technical_test_score';
            $update_stmt = $conn->prepare("UPDATE Students 
                                         SET $column_name = IFNULL($column_name, 0) + ? 
                                         WHERE id = ?");
            if (!$update_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $update_stmt->bind_param("di", $final_score, $student_id);
            
            if (!$update_stmt->execute()) {
                throw new Exception("Error updating score: " . $update_stmt->error);
            }
            
            recordQuizAttempt($conn, $student_id, $quiz_id, $quiz_type, $final_score);
            
            $conn->commit();
            
            // Store score in session for result display
            $_SESSION['final_score'] = $final_score;
            $_SESSION['quiz_type'] = $quiz_type;
            $_SESSION['quiz_id'] = $quiz_id;
            $_SESSION['student_id'] = $student_id;
            
            // Redirect to results page
            header('Location: finalthankyou.html');
            exit();
            
        } catch (Exception $e) {
            $conn->rollback();
            die("<div class='error'>Error processing quiz: " . 
                (DEBUG_MODE ? $e->getMessage() : "Please try again later") . "</div>");
        }
        
    } elseif (isset($_POST['start_quiz'])) {
        // Start quiz process
        $quiz_id = sanitizeInput($_POST['quiz_id']);
        $quiz_type = sanitizeInput($_POST['quiz_type']);
        $student_id = sanitizeInput($_POST['student_id']);
        
        if (!validateStudentId($student_id)) {
            die("<div class='error'>Invalid student ID</div>");
        }
        
        try {
            if (!studentExists($conn, $student_id)) {
                header('Location: technical.php?action=register');
                exit();
            }
        } catch (Exception $e) {
            die("<div class='error'>Error verifying student: " . 
                (DEBUG_MODE ? $e->getMessage() : "Please try again later") . "</div>");
        }
        
        try {
            if (hasAttemptedQuiz($conn, $student_id, $quiz_id, $quiz_type)) {
                die("<div class='error'>You have already attempted the " . ucfirst($quiz_type) . " Test ($quiz_id). Only one attempt is allowed per test.</div>");
            }
        } catch (Exception $e) {
            die("<div class='error'>Error checking quiz attempts: " . 
                (DEBUG_MODE ? $e->getMessage() : "Please try again later") . "</div>");
        }
        
        $table_name = ($quiz_type === 'psychometric') ? 'psychometric_questions' : 'tech_questions';
        
        try {
            $stmt = $conn->prepare("SELECT * FROM $table_name WHERE quiz_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("s", $quiz_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            
            if ($result === false) {
                throw new Exception("Error fetching questions: " . $conn->error);
            }
            
            if ($result->num_rows > 0) {
                $_SESSION['quiz_questions'] = $result->fetch_all(MYSQLI_ASSOC);
                $_SESSION['quiz_id'] = $quiz_id;
                $_SESSION['quiz_type'] = $quiz_type;
                $_SESSION['current_question'] = 0;
                $_SESSION['answers'] = array();
                $_SESSION['start_time'] = time();
                $_SESSION['student_id'] = $student_id;
                
                displayQuestion(0);
                exit();
            } else {
                echo "<div class='alert alert-info'>No questions found for this test.</div>";
            }
            
        } catch (Exception $e) {
            die("<div class='error'>Error starting quiz: " . 
                (DEBUG_MODE ? $e->getMessage() : "Please try again later") . "</div>");
        }
    } elseif (isset($_POST['next_question']) || isset($_POST['prev_question'])) {
        if (!isset($_SESSION['quiz_questions']) || !isset($_SESSION['current_question'])) {
            die("<div class='error'>Quiz session expired. Please start the quiz again.</div>");
        }
        
        $current = $_SESSION['current_question'];
        $total = count($_SESSION['quiz_questions']);
        
        if (isset($_POST['answer'])) {
            $question_id = $_SESSION['quiz_questions'][$current]['id'];
            $_SESSION['answers'][$question_id] = sanitizeInput($_POST['answer']);
        }
        
        if (isset($_POST['next_question'])) {
            $new_index = min($current + 1, $total - 1);
        } else {
            $new_index = max($current - 1, 0);
        }
        
        $_SESSION['current_question'] = $new_index;
        displayQuestion($new_index);
        exit();
    }
} elseif (isset($_GET['question']) && is_numeric($_GET['question']) && isset($_SESSION['quiz_questions'])) {
    $index = (int)$_GET['question'];
    $total = count($_SESSION['quiz_questions']);
    
    if ($index >= 0 && $index < $total) {
        $_SESSION['current_question'] = $index;
        displayQuestion($index);
        exit();
    } else {
        die("<div class='error'>Invalid question number</div>");
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'show_results') {
    displayResults($conn);
    exit();
} elseif (isset($_GET['action']) && $_GET['action'] === 'register') {
    displayRegistrationForm();
    exit();
}

// If no valid action, display registration or quiz selection
if (!isset($_SESSION['student_id']) || !studentExists($conn, $_SESSION['student_id'])) {
    displayRegistrationForm();
} else {
    displayQuizSelection($conn);
}

// =============================================
// Helper Functions
// =============================================
function displayRegistrationForm() {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Registration</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Inter', sans-serif;
            }

            body {
                background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .container {
                max-width: 600px;
                width: 100%;
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                padding: 40px;
                margin: 20px auto;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
            }

            .header h1 {
                font-size: 2.5rem;
                color: #2c3e50;
                margin-bottom: 10px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                font-size: 1.1rem;
                color: #2c3e50;
                margin-bottom: 5px;
            }

            .form-group input {
                width: 100%;
                padding: 12px;
                border: 1px solid #dfe6e9;
                border-radius: 8px;
                font-size: 1rem;
                transition: border-color 0.3s;
            }

            .form-group input:focus {
                outline: none;
                border-color: #1e90ff;
            }

            .submit-btn {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 8px;
                background: #1e90ff;
                color: white;
                font-size: 1.1rem;
                cursor: pointer;
                transition: background 0.3s, transform 0.2s;
            }

            .submit-btn:hover {
                background: #3498db;
                transform: translateY(-2px);
            }

            .error {
                background: #e74c3c;
                color: white;
                padding: 15px;
                border-radius: 8px;
                text-align: center;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Student Registration</h1>
                <p>Please fill in your details to start the assessment</p>
            </div>
            <form method='POST'>
                <div class='form-group'>
                    <label for='student_id'>Student ID</label>
                    <input type='text' name='student_id' id='student_id' required>
                </div>
                <div class='form-group'>
                    <label for='name'>Full Name</label>
                    <input type='text' name='name' id='name' required>
                </div>
                <div class='form-group'>
                    <label for='email'>Email</label>
                    <input type='email' name='email' id='email' required>
                </div>
                <div class='form-group'>
                    <label for='college'>College</label>
                    <input type='text' name='college' id='college' required>
                </div>
                <div class='form-group'>
                    <label for='semester'>Semester (1-8)</label>
                    <input type='number' name='semester' id='semester' min='1' max='8' required>
                </div>
                <div class='form-group'>
                    <label for='mobile'>Mobile Number</label>
                    <input type='text' name='mobile' id='mobile' pattern="[0-9]{10}" required>
                </div>
                <button type='submit' name='submit_registration' class='submit-btn'>Register</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    ob_end_flush();
}

function displayQuestion($index) {
    if (!isset($_SESSION['quiz_questions']) || !isset($_SESSION['quiz_id']) || !isset($_SESSION['quiz_type'])) {
        die("<div class='error'>Quiz session expired. Please start the quiz again.</div>");
    }
    
    $questions = $_SESSION['quiz_questions'];
    $total_questions = count($questions);
    
    if ($index < 0 || $index >= $total_questions) {
        die("<div class='error'>Invalid question number</div>");
    }
    
    $current_question = $questions[$index];
    $quiz_id = $_SESSION['quiz_id'];
    $quiz_type = $_SESSION['quiz_type'];
    $student_id = $_SESSION['student_id'];
    
    $time_elapsed = time() - $_SESSION['start_time'];
    $time_remaining = max(1800 - $time_elapsed, 0);
    
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= ucfirst($quiz_type) ?> Test</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Inter', sans-serif;
            }

            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .container {
                max-width: 900px;
                width: 100%;
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                padding: 30px;
                margin: 20px auto;
            }

            .timer-container {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #1e90ff;
                color: white;
                padding: 10px 20px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 18px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                transition: background-color 0.3s;
            }

            .timer-container.timer-warning {
                background: #e74c3c;
                animation: pulse 1s infinite;
            }

            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }

            .quiz-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .quiz-title {
                font-size: 2.5rem;
                color: #2c3e50;
                margin-bottom: 10px;
            }

            .progress-container {
                background: #ecf0f1;
                border-radius: 8px;
                height: 10px;
                margin-bottom: 20px;
                overflow: hidden;
            }

            .progress-bar {
                width: <?= ($index + 1) / $total_questions * 100 ?>%;
                height: 100%;
                background: #1e90ff;
                transition: width 0.3s ease-in-out;
            }

            .question-card {
                background: #f9f9f9;
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                transition: transform 0.3s ease;
            }

            .question-card:hover {
                transform: translateY(-5px);
            }

            .question-number {
                font-size: 1.2rem;
                color: #7f8c8d;
                margin-bottom: 10px;
            }

            .question-text {
                font-size: 1.5rem;
                color: #2c3e50;
                margin-bottom: 20px;
                line-height: 1.4;
            }

            .options-container {
                display: grid;
                gap: 15px;
            }

            .option {
                display: flex;
                align-items: center;
                background: #ffffff;
                padding: 15px;
                border-radius: 8px;
                cursor: pointer;
                transition: background 0.3s, transform 0.2s;
                border: 1px solid #dfe6e9;
            }

            .option:hover {
                background: #e8f4ff;
                transform: translateX(5px);
            }

            .option input[type="radio"] {
                margin-right: 10px;
                accent-color: #1e90ff;
            }

            .option-label {
                font-size: 1.1rem;
                color: #2c3e50;
            }

            .navigation-buttons {
                display: flex;
                justify-content: space-between;
                margin-top: 20px;
            }

            .nav-btn, .submit-btn {
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 1.1rem;
                cursor: pointer;
                transition: background 0.3s, transform 0.2s;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .nav-btn {
                background: #dfe6e9;
                color: #2c3e50;
            }

            .nav-btn:hover:not(:disabled) {
                background: #b2bec3;
                transform: translateY(-2px);
            }

            .nav-btn:disabled {
                background: #ecf0f1;
                cursor: not-allowed;
            }

            .submit-btn {
                background: #27ae60;
                color: white;
            }

            .submit-btn:hover {
                background: #219653;
                transform: translateY(-2px);
            }

            .error {
                background: #e74c3c;
                color: white;
                padding: 15px;
                border-radius: 8px;
                text-align: center;
                margin: 20px auto;
                max-width: 600px;
            }
        </style>
    </head>
    <body>
        <div class='timer-container' id='timer'>
            <?= floor($time_remaining / 60) ?>:<?= str_pad($time_remaining % 60, 2, '0', STR_PAD_LEFT) ?>
        </div>
        
        <div class='container'>
            <div class='quiz-header'>
                <h1 class='quiz-title'><?= ucfirst($quiz_type) ?> Test</h1>
                <p>Answer all questions to complete the assessment</p>
            </div>
            
            <div class='progress-container'>
                <div class='progress-bar'></div>
            </div>
            
            <form method='POST' id='quizForm'>
                <input type='hidden' name='quiz_id' value='<?= $quiz_id ?>'>
                <input type='hidden' name='quiz_type' value='<?= $quiz_type ?>'>
                <input type='hidden' name='student_id' value='<?= $student_id ?>'>
                
                <div class='question-card'>
                    <div class='question-number'>
                        Question <?= $index + 1 ?> of <?= $total_questions ?>
                    </div>
                    <div class='question-text'>
                        <?= htmlspecialchars($current_question['question']) ?>
                    </div>
                    
                    <div class='options-container'>
                        <?php foreach (['A', 'B', 'C', 'D'] as $option): ?>
                            <?php if (!empty($current_question['option_' . $option])): ?>
                                <label class='option'>
                                    <input type='radio' name='answer' value='<?= $option ?>' 
                                        <?= (isset($_SESSION['answers'][$current_question['id']]) && $_SESSION['answers'][$current_question['id']] == $option) ? 'checked' : '' ?>
                                        required>
                                    <span class='option-label'><?= htmlspecialchars($current_question['option_' . $option]) ?></span>
                                </label>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class='navigation-buttons'>
                    <button type='submit' name='prev_question' class='nav-btn' <?= $index == 0 ? 'disabled' : '' ?>>
                        <i class='fas fa-arrow-left'></i> Previous
                    </button>
                    
                    <?php if ($index < $total_questions - 1): ?>
                        <button type='submit' name='next_question' class='nav-btn'>
                            Next <i class='fas fa-arrow-right'></i>
                        </button>
                    <?php else: ?>
                        <button type='submit' name='submit_quiz' class='submit-btn'>
                            <i class='fas fa-paper-plane'></i> Submit Test
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <script>
            let timeLeft = <?= $time_remaining ?>;
            const timerElement = document.getElementById('timer');
            
            function updateTimer() {
                timeLeft--;
                
                if (timeLeft <= 0) {
                    document.getElementById('quizForm').submit();
                    return;
                }
                
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 300) {
                    timerElement.classList.add('timer-warning');
                }
                
                setTimeout(updateTimer, 1000);
            }
            
            updateTimer();
            
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
    </body>
    </html>
    <?php
    ob_end_flush();
}

function displayQuizSelection($conn) {
    $technical_quizzes = [
        ['id' => 'P', 'name' => 'PHP Quiz', 'icon' => 'fab fa-php'],
        ['id' => 'A', 'name' => 'Android Quiz', 'icon' => 'fab fa-android'],
        ['id' => 'Python', 'name' => 'Python Quiz', 'icon' => 'fab fa-python'],
        ['id' => 'C&C++_Placement_Test', 'name' => 'C & C++', 'icon' => 'fas fa-code'],
        ['id' => 'Java_Quiz', 'name' => 'Java Quiz', 'icon' => 'fab fa-java'],
        ['id' => 'Networking', 'name' => 'Networking Quiz', 'icon' => 'fas fa-network-wired']
    ];

    $psychometric_quizzes = [];
    try {
        $result = $conn->query("SELECT DISTINCT quiz_id FROM psychometric_questions");
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $quiz_id = $row['quiz_id'];
                $name = ucfirst(str_replace('_', ' ', $quiz_id));
                $icon_map = [
                    'personality' => 'fas fa-user-tie',
                    'iq' => 'fas fa-brain',
                    'emotional' => 'fas fa-heart',
                    'aptitude' => 'fas fa-lightbulb'
                ];
                $icon = $icon_map[strtolower($quiz_id)] ?? 'fas fa-question-circle';
                
                $psychometric_quizzes[] = [
                    'id' => $quiz_id,
                    'name' => $name,
                    'icon' => $icon
                ];
            }
        }
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            error_log("Error fetching psychometric quizzes: " . $e->getMessage());
        }
    }

    $student_id = $_SESSION['student_id'];
    $attempted_quizzes = [];
    try {
        if (studentExists($conn, $student_id)) {
            $stmt = $conn->prepare("SELECT quiz_id, quiz_type FROM quiz_attempts WHERE student_id = ?");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $attempted_quizzes[$row['quiz_type'] . '_' . $row['quiz_id']] = true;
            }
        }
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            error_log("Error fetching quiz attempts: " . $e->getMessage());
        }
    }

    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Assessment Center</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Inter', sans-serif;
            }

            body {
                background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .container {
                max-width: 1200px;
                width: 100%;
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                padding: 40px;
                margin: 20px auto;
            }

            .header {
                text-align: center;
                margin-bottom: 40px;
            }

            .header h1 {
                font-size: 2.8rem;
                color: #2c3e50;
                margin-bottom: 10px;
            }

            .tabs {
                display: flex;
                justify-content: center;
                margin-bottom: 30px;
                gap: 10px;
            }

            .tab-btn {
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 1.1rem;
                cursor: pointer;
                background: #dfe6e9;
                color: #2c3e50;
                transition: background 0.3s, transform 0.2s;
            }

            .tab-btn.active, .tab-btn:hover {
                background: #1e90ff;
                color: white;
                transform: translateY(-2px);
            }

            .tab-content {
                display: none;
            }

            .tab-content.active {
                display: block;
            }

            .section-title {
                font-size: 1.8rem;
                color: #2c3e50;
                margin-bottom: 20px;
                text-align: center;
            }

            .quiz-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 20px;
            }

            .quiz-card {
                background: #ffffff;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                position: relative;
            }

            .quiz-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            }

            .quiz-card.completed {
                opacity: 0.7;
                cursor: not-allowed;
            }

            .badge {
                position: absolute;
                top: 10px;
                right: 10px;
                background: #27ae60;
                color: white;
                padding: 5px 10px;
                border-radius: 12px;
                font-size: 0.9rem;
                font-weight: 500;
            }

            .quiz-card.completed .badge {
                background: #7f8c8d;
            }

            .quiz-card-header {
                background: linear-gradient(90deg, #1e90ff 0%, #3498db 100%);
                padding: 20px;
                text-align: center;
                color: white;
            }

            .quiz-icon {
                font-size: 2rem;
                margin-bottom: 10px;
                animation: float 3s ease-in-out infinite;
            }

            @keyframes float {
                0% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0); }
            }

            .quiz-card-body {
                padding: 20px;
                text-align: center;
            }

            .quiz-desc {
                color: #7f8c8d;
                margin-bottom: 15px;
                font-size: 1rem;
            }

            .start-quiz-btn {
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                background: #1e90ff;
                color: white;
                font-size: 1.1rem;
                cursor: pointer;
                transition: background 0.3s, transform 0.2s;
                display: flex;
                align-items: center;
                gap: 8px;
                margin: 0 auto;
            }

            .start-quiz-btn:hover:not(:disabled) {
                background: #3498db;
                transform: translateY(-2px);
            }

            .start-quiz-btn:disabled {
                background: #7f8c8d;
                cursor: not-allowed;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Assessment Center</h1>
                <p>Select your test from the available options below. Complete one test at a time. Each test can only be attempted once.</p>
            </div>
            
            <div class='tabs'>
                <button class='tab-btn active' data-tab='technical'>Technical Tests</button>
                <button class='tab-btn' data-tab='psychometric'>Psychometric Tests</button>
            </div>
            
            <div id='technical' class='tab-content active'>
                <h3 class='section-title'>Technical Skills Assessment</h3>
                <div class='quiz-grid'>
                    <?php foreach ($technical_quizzes as $quiz): ?>
                        <?php $is_completed = isset($attempted_quizzes['technical_' . $quiz['id']]); ?>
                        <div class='quiz-card <?= $is_completed ? 'completed' : '' ?>'>
                            <div class='badge'><?= $is_completed ? 'Completed' : 'New' ?></div>
                            <div class='quiz-card-header'>
                                <div class='quiz-icon floating'><i class='<?= $quiz['icon'] ?>'></i></div>
                                <h3><?= $quiz['name'] ?></h3>
                            </div>
                            <div class='quiz-card-body'>
                                <p class='quiz-desc'>Test your knowledge in <?= $quiz['name'] ?></p>
                                <form method='POST'>
                                    <input type='hidden' name='quiz_id' value='<?= $quiz['id'] ?>'>
                                    <input type='hidden' name='quiz_type' value='technical'>
                                    <input type='hidden' name='student_id' value='<?= $student_id ?>'>
                                    <button type='submit' name='start_quiz' class='start-quiz-btn' <?= $is_completed ? 'disabled' : '' ?>>
                                        <i class='fas fa-play'></i> <?= $is_completed ? 'Completed' : 'Start Test' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div id='psychometric' class='tab-content'>
                <h3 class='section-title'>Psychometric Evaluation</h3>
                <div class='quiz-grid'>
                    <?php foreach ($psychometric_quizzes as $quiz): ?>
                        <?php $is_completed = isset($attempted_quizzes['psychometric_' . $quiz['id']]); ?>
                        <div class='quiz-card <?= $is_completed ? 'completed' : '' ?>'>
                            <div class='badge'><?= $is_completed ? 'Completed' : 'New' ?></div>
                            <div class='quiz-card-header'>
                                <div class='quiz-icon floating'><i class='<?= $quiz['icon'] ?>'></i></div>
                                <h3><?= $quiz['name'] ?></h3>
                            </div>
                            <div class='quiz-card-body'>
                                <p class='quiz-desc'>Evaluate your <?= $quiz['name'] ?> skills</p>
                                <form method='POST'>
                                    <input type='hidden' name='quiz_id' value='<?= $quiz['id'] ?>'>
                                    <input type='hidden' name='quiz_type' value='psychometric'>
                                    <input type='hidden' name='student_id' value='<?= $student_id ?>'>
                                    <button type='submit' name='start_quiz' class='start-quiz-btn' <?= $is_completed ? 'disabled' : '' ?>>
                                        <i class='fas fa-play'></i> <?= $is_completed ? 'Completed' : 'Start Test' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabBtns = document.querySelectorAll('.tab-btn');
                const tabContents = document.querySelectorAll('.tab-content');
                
                tabBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tabId = this.getAttribute('data-tab');
                        
                        tabBtns.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        
                        tabContents.forEach(content => {
                            content.classList.remove('active');
                            if (content.id === tabId) {
                                content.classList.add('active');
                            }
                        });
                    });
                });
            });
        </script>
    </body>
    </html>
    <?php
    ob_end_flush();
}

function displayResults($conn) {
    if (!isset($_SESSION['final_score']) || !isset($_SESSION['quiz_type']) || !isset($_SESSION['quiz_id']) || !isset($_SESSION['student_id'])) {
        header('Location: technical.php');
        exit();
    }

    $student_id = $_SESSION['student_id'];
    $quiz_type = $_SESSION['quiz_type'];
    $quiz_id = $_SESSION['quiz_id'];
    $final_score = $_SESSION['final_score'];

    try {
        $student_details = getStudentDetails($conn, $student_id);
    } catch (Exception $e) {
        die("<div class='error'>Error fetching student details: " . 
            (DEBUG_MODE ? $e->getMessage() : "Please try again later") . "</div>");
    }

    // Clear session data
    session_unset();
    session_destroy();

    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quiz Results</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Inter', sans-serif;
            }

            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .container {
                max-width: 800px;
                width: 100%;
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                padding: 30px;
                margin: 20px auto;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
            }

            .header h1 {
                font-size: 2.5rem;
                color: #2c3e50;
                margin-bottom: 10px;
            }

            .result-card {
                background: #f9f9f9;
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            }

            .result-card h2 {
                font-size: 1.8rem;
                color: #2c3e50;
                margin-bottom: 15px;
            }

            .result-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            .result-table th, .result-table td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #dfe6e9;
            }

            .result-table th {
                background: #1e90ff;
                color: white;
            }

            .score-container {
                text-align: center;
                margin-bottom: 20px;
            }

            .score {
                font-size: 2rem;
                color: #27ae60;
                font-weight: 600;
            }

            .back-btn {
                display: block;
                width: 200px;
                margin: 0 auto;
                padding: 12px;
                border: none;
                border-radius: 8px;
                background: #1e90ff;
                color: white;
                font-size: 1.1rem;
                cursor: pointer;
                text-align: center;
                text-decoration: none;
                transition: background 0.3s, transform 0.2s;
            }

            .back-btn:hover {
                background: #3498db;
                transform: translateY(-2px);
            }

            @media (max-width: 600px) {
                .container {
                    padding: 20px;
                }

                .header h1 {
                    font-size: 2rem;
                }

                .result-table th, .result-table td {
                    padding: 8px;
                    font-size: 0.9rem;
                }
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Test Results</h1>
                <p>Thank you for completing the assessment!</p>
            </div>
            
            <div class='result-card'>
                <h2>Student Details</h2>
                <table class='result-table'>
                    <tr>
                        <th>Name</th>
                        <td><?= htmlspecialchars($student_details['name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= htmlspecialchars($student_details['email']) ?></td>
                    </tr>
                    <tr>
                        <th>College</th>
                        <td><?= htmlspecialchars($student_details['college']) ?></td>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <td><?= htmlspecialchars($student_details['semester']) ?></td>
                    </tr>
                    <tr>
                        <th>Mobile</th>
                        <td><?= htmlspecialchars($student_details['mobile_number']) ?></td>
                    </tr>
                </table>
            </div>
            
            <div class='result-card'>
                <h2>Test Details</h2>
                <div class='score-container'>
                    <p>Test: <?= ucfirst($quiz_type) ?> - <?= htmlspecialchars($quiz_id) ?></p>
                    <p class='score'>Score: <?= number_format($final_score, 2) ?> / 20.00</p>
                </div>
            </div>
            
            <a href='technical.php' class='back-btn'>Back to Assessment Center</a>
        </div>
    </body>
    </html>
    <?php
    ob_end_flush();
}

$conn->close();
?>