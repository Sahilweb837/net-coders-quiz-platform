 <?php
ob_start();
session_start();
require_once('../db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit;
}

$baseUrl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'];

// Get student name for display
$student_id = intval($_SESSION['student_id']);
$nameStmt = $conn->prepare("SELECT name FROM students WHERE id = ?");
$nameStmt->bind_param("i", $student_id);
$nameStmt->execute();
$student_result = $nameStmt->get_result();
$student_name = "Student";
if ($student_result->num_rows > 0) {
    $student_data = $student_result->fetch_assoc();
    $student_name = $student_data['name'];
}
$first_name = explode(' ', $student_name)[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quiz_id = intval($_POST['quiz_id']);
    $student_id = intval($_SESSION['student_id']);

    // 🔒 Check if quiz already submitted
    $checkStmt = $conn->prepare("SELECT id FROM quiz_results WHERE student_id=? AND quiz_id=?");
    $checkStmt->bind_param("ii", $student_id, $quiz_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        showAlreadySubmittedPage($baseUrl, $first_name);
        exit;
    }

    $score = 0;

    try {

        foreach ($_POST as $key => $value) {

            if (strpos($key, 'question_') === 0) {

                $question_id = intval(str_replace('question_', '', $key));
                $selected_option = strtolower(trim(str_replace('option_', '', $value)));

                // Get correct answer
                $stmt = $conn->prepare("SELECT correct_option FROM questions WHERE id=?");
                $stmt->bind_param("i", $question_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $question = $result->fetch_assoc();

                if ($question) {

                    $correct_option = strtolower(trim($question['correct_option']));

                    if ($selected_option === $correct_option) {
                        $score++;
                    }

                    // Save response
                    $stmt2 = $conn->prepare("
                        INSERT INTO quiz_responses 
                        (student_id, quiz_id, question_id, selected_option, answer) 
                        VALUES (?, ?, ?, ?, ?)
                    ");

                    $stmt2->bind_param(
                        "iiiss",
                        $student_id,
                        $quiz_id,
                        $question_id,
                        $selected_option,
                        $correct_option
                    );

                    $stmt2->execute();
                }
            }
        }

        // Save final result
        $stmt3 = $conn->prepare("
            INSERT INTO quiz_results (student_id, quiz_id, result) 
            VALUES (?, ?, ?)
        ");

        $stmt3->bind_param("iii", $student_id, $quiz_id, $score);
        $stmt3->execute();

        // Prevent form resubmission
        unset($_POST);

        showThankYouPage($baseUrl, $first_name, $score);

    } catch (Exception $e) {
        showThankYouPage($baseUrl, $first_name, 0, $e->getMessage());
    }
}

ob_end_flush();


/* ---------- FUNCTIONS ---------- */

function showThankYouPage($baseUrl, $student_name, $score = 0, $error = null) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Completed - NETCODERS</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --orange: #ff5532;
            --orange-dark: #e03e1f;
            --orange-glow: rgba(255, 85, 50, 0.3);
            --dark-bg: #0b1120;
            --dark-card: #151f2f;
            --text-primary: #ffffff;
            --text-secondary: #e2e8f0;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* ===== ENHANCED ANIMATED BACKGROUND ===== */
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        .bg-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 30%, rgba(255, 85, 50, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 80% 70%, rgba(255, 85, 50, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 40% 80%, rgba(255, 85, 50, 0.12) 0%, transparent 45%);
            animation: gradientShift 15s ease-in-out infinite alternate;
        }

        @keyframes gradientShift {
            0% { transform: scale(1) rotate(0deg); opacity: 0.8; }
            50% { transform: scale(1.2) rotate(2deg); opacity: 1; }
            100% { transform: scale(1.1) rotate(-2deg); opacity: 0.9; }
        }

        /* Floating Boxes */
        .boxes-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .box {
            position: absolute;
            background: rgba(255, 85, 50, 0.03);
            border: 1px solid rgba(255, 85, 50, 0.1);
            border-radius: 8px;
            animation: floatBox 20s infinite ease-in-out;
        }

        .box-1 { width: 120px; height: 120px; top: 10%; left: 5%; transform: rotate(15deg); animation-delay: 0s; }
        .box-2 { width: 180px; height: 180px; top: 20%; right: 10%; transform: rotate(-10deg); animation-delay: 2s; }
        .box-3 { width: 200px; height: 200px; top: 50%; left: -30px; transform: rotate(25deg); animation-delay: 4s; }
        .box-4 { width: 150px; height: 150px; bottom: 15%; right: 15%; transform: rotate(-20deg); animation-delay: 1s; }
        .box-5 { width: 250px; height: 250px; bottom: -50px; left: 20%; transform: rotate(30deg); animation-delay: 3s; }

        @keyframes floatBox {
            0% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.3; }
            25% { transform: translateY(-30px) rotate(5deg) scale(1.05); opacity: 0.5; }
            50% { transform: translateY(20px) rotate(-5deg) scale(0.95); opacity: 0.4; }
            75% { transform: translateY(-15px) rotate(8deg) scale(1.02); opacity: 0.5; }
            100% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.3; }
        }

        /* Particles */
        .particles-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 85, 50, 0.2);
            border-radius: 50%;
            animation: floatParticle 15s infinite linear;
        }

        .particle-1 { width: 4px; height: 4px; top: 30%; left: 20%; animation-duration: 12s; }
        .particle-2 { width: 6px; height: 6px; top: 70%; left: 50%; animation-duration: 18s; animation-delay: 2s; }
        .particle-3 { width: 3px; height: 3px; top: 40%; left: 80%; animation-duration: 14s; animation-delay: 1s; }

        @keyframes floatParticle {
            0% { transform: translateY(0) translateX(0); opacity: 0.2; }
            25% { transform: translateY(-50px) translateX(30px); opacity: 0.5; }
            50% { transform: translateY(20px) translateX(-40px); opacity: 0.3; }
            75% { transform: translateY(-30px) translateX(20px); opacity: 0.5; }
            100% { transform: translateY(0) translateX(0); opacity: 0.2; }
        }

        /* Grid */
        .grid-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 85, 50, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 85, 50, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
            animation: gridMove 30s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(10px) translateY(10px); }
            100% { transform: translateX(0) translateY(0); }
        }

        /* Success Card Animation */
        .success-card {
            background: rgba(21, 31, 47, 0.85);
            backdrop-filter: blur(12px);
            border: 3px solid var(--orange);
            border-radius: 48px;
            padding: 50px 40px;
            max-width: 550px;
            width: 100%;
            text-align: center;
            box-shadow: 0 30px 60px rgba(255, 85, 50, 0.3);
            animation: cardAppear 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            z-index: 10;
        }

        @keyframes cardAppear {
            0% {
                opacity: 0;
                transform: scale(0.6) translateY(50px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .success-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,85,50,0.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: pulseGlow 3s infinite;
        }

        .success-card::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -50%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,85,50,0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: pulseGlow 4s infinite reverse;
        }

        @keyframes pulseGlow {
            0% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.2); }
            100% { opacity: 0.3; transform: scale(1); }
        }

        .greeting-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 85, 50, 0.15);
            border: 1px solid rgba(255, 85, 50, 0.3);
            border-radius: 60px;
            padding: 10px 20px;
            margin-bottom: 25px;
            animation: slideInRight 0.6s ease 0.2s both;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .greeting-badge i {
            color: var(--orange);
            font-size: 1.2rem;
        }

        .greeting-badge span {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .greeting-badge strong {
            color: var(--orange);
            font-weight: 700;
        }

        .icon-circle {
            width: 120px;
            height: 120px;
            background: rgba(255, 85, 50, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            border: 3px solid var(--orange);
            animation: rotateIn 0.8s ease, pulseIcon 2s infinite 0.8s;
        }

        @keyframes rotateIn {
            from { transform: rotate(-180deg) scale(0); opacity: 0; }
            to { transform: rotate(0) scale(1); opacity: 1; }
        }

        @keyframes pulseIcon {
            0% { box-shadow: 0 0 0 0 var(--orange-glow); }
            70% { box-shadow: 0 0 0 20px rgba(255, 85, 50, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 85, 50, 0); }
        }

        .icon-circle i {
            font-size: 4rem;
            color: var(--orange);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h1 {
            color: white;
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
            animation: fadeInUp 0.6s ease 0.3s both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 span {
            color: var(--orange);
            position: relative;
            display: inline-block;
        }

        h1 span::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(255, 85, 50, 0.3);
            border-radius: 10px;
            z-index: -1;
            animation: widthGrow 0.8s ease 0.6s both;
        }

        @keyframes widthGrow {
            from { width: 0; }
            to { width: 100%; }
        }

        .greeting-text {
            color: var(--text-secondary);
            font-size: 1.2rem;
            margin-bottom: 25px;
            animation: fadeInUp 0.6s ease 0.4s both;
        }

        .score-badge {
            background: rgba(255, 85, 50, 0.15);
            border: 2px solid var(--orange);
            border-radius: 80px;
            padding: 20px 30px;
            display: inline-block;
            margin: 20px 0;
            animation: scaleIn 0.6s ease 0.5s both;
        }

        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .score-badge h2 {
            color: white;
            font-size: 2.5rem;
            margin: 0;
            line-height: 1;
        }

        .score-badge small {
            color: var(--orange);
            font-size: 1rem;
            display: block;
            margin-top: 5px;
        }

        .locked-message {
            background: rgba(255, 85, 50, 0.1);
            border: 1px solid var(--orange);
            border-radius: 40px;
            padding: 15px 25px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--orange);
            font-size: 1rem;
            margin: 20px 0;
            animation: slideInLeft 0.6s ease 0.6s both;
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .locked-message i {
            font-size: 1.2rem;
            animation: lockShake 2s infinite;
        }

        @keyframes lockShake {
            0%, 100% { transform: rotate(0); }
            10%, 30%, 50%, 70%, 90% { transform: rotate(-5deg); }
            20%, 40%, 60%, 80% { transform: rotate(5deg); }
        }

        .timer-container {
            margin: 25px 0 15px;
            animation: fadeIn 0.6s ease 0.7s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .timer-text {
            color: var(--text-muted);
            font-size: 1rem;
            margin-bottom: 5px;
        }

        #timer {
            color: var(--orange);
            font-weight: 700;
            font-size: 2rem;
            display: inline-block;
            min-width: 60px;
            animation: timerPulse 1s infinite;
        }

        @keyframes timerPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: white;
            padding: 16px 40px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px var(--orange-glow);
            border: none;
            margin-top: 15px;
            animation: buttonPop 0.6s ease 0.8s both;
        }

        @keyframes buttonPop {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }

        .dashboard-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 35px rgba(255, 85, 50, 0.5);
        }

        .dashboard-btn i {
            transition: transform 0.3s ease;
        }

        .dashboard-btn:hover i {
            transform: translateX(5px);
        }

        /* Error Card */
        .error-card {
            background: rgba(21, 31, 47, 0.85);
            backdrop-filter: blur(12px);
            border: 3px solid #ef4444;
            border-radius: 48px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .error-icon {
            width: 80px;
            height: 80px;
            background: rgba(239, 68, 68, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 2px solid #ef4444;
        }

        .error-icon i {
            font-size: 3rem;
            color: #ef4444;
        }

        .alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid #ef4444;
            border-radius: 20px;
            color: white;
            padding: 15px;
            margin: 20px 0;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .success-card { padding: 30px 20px; }
            h1 { font-size: 2.2rem; }
            .icon-circle { width: 100px; height: 100px; }
            .icon-circle i { font-size: 3rem; }
            .score-badge h2 { font-size: 2rem; }
            .greeting-badge { font-size: 0.9rem; }
        }
    </style>
</head>
<body>

    <!-- Animated Background -->
    <div class="bg-container">
        <div class="bg-gradient"></div>
        <div class="boxes-layer">
            <div class="box box-1"></div>
            <div class="box box-2"></div>
            <div class="box box-3"></div>
            <div class="box box-4"></div>
            <div class="box box-5"></div>
        </div>
        <div class="particles-layer">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
        </div>
        <div class="grid-layer"></div>
    </div>

    <?php if ($error): ?>
        <!-- Error Card -->
        <div class="error-card">
            <div class="error-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h2 style="color:white; margin-bottom:15px;">Submission Error</h2>
            <div class="alert">
                <?= htmlspecialchars($error) ?>
            </div>
            <div class="timer-container">
                <div class="timer-text">Redirecting in <span id="timer">5</span> seconds...</div>
            </div>
            <a href="<?= $baseUrl ?>/cstm_quiz/student/index.php" class="dashboard-btn">
                <i class="bi bi-speedometer2"></i> Go to Dashboard
            </a>
        </div>
    <?php else: ?>
        <!-- Success Card -->
        <div class="success-card">
            
            <!-- Student Greeting Badge -->
            <div class="greeting-badge">
                <i class="bi bi-person-circle"></i>
                <span>Great job, <strong><?= htmlspecialchars($student_name) ?>!</strong></span>
                <i class="bi bi-emoji-smile"></i>
            </div>

            <!-- Animated Icon -->
            <div class="icon-circle">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <!-- Main Title -->
            <h1>Quiz <span>Completed!</span></h1>

            <!-- Personalized Message -->
            <div class="greeting-text">
                Your responses have been recorded successfully.
            </div>

            <!-- Score Display -->
            <div class="score-badge">
                <h2><?= $score ?> / 20</h2>
                <small>Your Score</small>
            </div>

            <!-- Lock Message -->
            <div class="locked-message">
                <i class="bi bi-lock-fill"></i>
                <span>Test is now permanently locked</span>
                <i class="bi bi-lock-fill"></i>
            </div>

            <!-- Timer -->
            <div class="timer-container">
                <div class="timer-text">Auto-redirecting in</div>
                <span id="timer">5</span> seconds
            </div>

            <!-- Dashboard Button -->
            <a href="<?= $baseUrl ?>/cstm_quiz/student/index.php" class="dashboard-btn">
                <i class="bi bi-speedometer2"></i> Go to Dashboard Now
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    <?php endif; ?>

    <!-- Timer Script -->
    <script>
        let timeLeft = 5;
        const timerElement = document.getElementById('timer');
        
        if (timerElement) {
            const countdown = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                // Add pulse effect when low
                if (timeLeft <= 2) {
                    timerElement.style.animation = 'timerPulse 0.5s infinite';
                }
                
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    window.location.href = "<?= $baseUrl ?>/cstm_quiz/student/index.php";
                }
            }, 1000);
        }
    </script>
</body>
</html>
<?php exit; }


function showAlreadySubmittedPage($baseUrl, $student_name) { ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Already Attempted - NETCODERS</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --orange: #ff5532;
            --orange-dark: #e03e1f;
            --orange-glow: rgba(255, 85, 50, 0.3);
            --dark-bg: #0b1120;
            --dark-card: #151f2f;
            --text-primary: #ffffff;
            --text-secondary: #e2e8f0;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        .bg-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 30%, rgba(255, 85, 50, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 80% 70%, rgba(255, 85, 50, 0.1) 0%, transparent 50%);
            animation: gradientShift 15s ease-in-out infinite alternate;
        }

        @keyframes gradientShift {
            0% { transform: scale(1) rotate(0deg); opacity: 0.8; }
            50% { transform: scale(1.2) rotate(2deg); opacity: 1; }
            100% { transform: scale(1.1) rotate(-2deg); opacity: 0.9; }
        }

        .box {
            position: absolute;
            background: rgba(255, 85, 50, 0.03);
            border: 1px solid rgba(255, 85, 50, 0.1);
            border-radius: 8px;
            animation: floatBox 20s infinite ease-in-out;
        }

        .box-1 { width: 120px; height: 120px; top: 10%; left: 5%; transform: rotate(15deg); }
        .box-2 { width: 180px; height: 180px; bottom: 15%; right: 10%; transform: rotate(-10deg); animation-delay: 2s; }

        @keyframes floatBox {
            0% { transform: translateY(0) rotate(0deg); opacity: 0.3; }
            50% { transform: translateY(-30px) rotate(5deg); opacity: 0.5; }
            100% { transform: translateY(0) rotate(0deg); opacity: 0.3; }
        }

        .locked-card {
            background: rgba(21, 31, 47, 0.85);
            backdrop-filter: blur(12px);
            border: 3px solid #ef4444;
            border-radius: 48px;
            padding: 50px 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: cardShake 0.5s ease;
            position: relative;
            overflow: hidden;
            z-index: 10;
        }

        @keyframes cardShake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .locked-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .greeting-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 60px;
            padding: 10px 20px;
            margin-bottom: 25px;
        }

        .greeting-badge i, .greeting-badge strong {
            color: #ef4444;
        }

        .lock-icon {
            width: 120px;
            height: 120px;
            background: rgba(239, 68, 68, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            border: 3px solid #ef4444;
            animation: lockPulse 2s infinite;
        }

        @keyframes lockPulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .lock-icon i {
            font-size: 4rem;
            color: #ef4444;
            animation: lockWiggle 2s infinite;
        }

        @keyframes lockWiggle {
            0%, 100% { transform: rotate(0); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }

        h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
        }

        h1 span {
            color: #ef4444;
        }

        .warning-text {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 25px;
        }

        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: white;
            padding: 16px 35px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px var(--orange-glow);
            margin-top: 20px;
        }

        .dashboard-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px rgba(255, 85, 50, 0.5);
        }
    </style>
</head>
<body>

    <!-- Animated Background -->
    <div class="bg-container">
        <div class="bg-gradient"></div>
        <div class="box box-1"></div>
        <div class="box box-2"></div>
    </div>

    <!-- Locked Card -->
    <div class="locked-card">
        
        <!-- Student Greeting -->
        <div class="greeting-badge">
            <i class="bi bi-person-circle"></i>
            <span>Hi, <strong><?= htmlspecialchars($student_name) ?></strong></span>
        </div>

        <!-- Lock Icon -->
        <div class="lock-icon">
            <i class="bi bi-lock-fill"></i>
        </div>

        <!-- Title -->
 
        <!-- Message -->
        <div class="warning-text">
            You have already attempted this quiz.<br>
            Each test can only be taken once.
        </div>

        <!-- Dashboard Button -->
        <a href="<?= $baseUrl ?>/cstm_quiz/student/index.php" class="dashboard-btn">
            <i class="bi bi-speedometer2"></i> Return to Dashboard
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

</body>
</html>

<?php exit; }
?>