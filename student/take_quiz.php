 <?php
session_start();
include('../db.php');

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$quiz_id = intval($_GET['quiz_id'] ?? ($_SESSION['current_quiz'] ?? 1));
$_SESSION['current_quiz'] = $quiz_id;

// ===== CHECK IF USER HAS ALREADY TAKEN THIS TEST =====
$checkStmt = $conn->prepare("SELECT id FROM quiz_results WHERE student_id = ? AND quiz_id = ?");
if ($checkStmt) {
    $checkStmt->bind_param("ii", $student_id, $quiz_id);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    // If already taken, show already submitted card
    $already_submitted = $checkStmt->num_rows > 0;
} else {
    $already_submitted = false;
}

// Also check quiz_responses table for any responses (backup check)
$respStmt = $conn->prepare("SELECT id FROM quiz_responses WHERE student_id = ? AND quiz_id = ? LIMIT 1");
if ($respStmt) {
    $respStmt->bind_param("ii", $student_id, $quiz_id);
    $respStmt->execute();
    $respStmt->store_result();
    $has_responses = $respStmt->num_rows > 0;
} else {
    $has_responses = false;
}

// If already submitted, show message
$show_already_submitted = $already_submitted || $has_responses;

// Only fetch quiz data if not already submitted
if (!$show_already_submitted) {
    // Fetch quiz info
    $quizStmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
    $quizStmt->bind_param("i", $quiz_id);
    $quizStmt->execute();
    $quiz = $quizStmt->get_result()->fetch_assoc();

    // If quiz doesn't exist
    if (!$quiz) {
        header("Location: ../student/index.php?error=quiz_not_found");
        exit;
    }

    // Fetch questions
    $qStmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
    $qStmt->bind_param("i", $quiz_id);
    $qStmt->execute();
    $questions_result = $qStmt->get_result();
    
    // Convert to array for shuffling
    $questions_array = [];
    while ($row = $questions_result->fetch_assoc()) {
        $questions_array[] = $row;
    }

    // If no questions
    if (count($questions_array) === 0) {
        header("Location: ../student/index.php?error=no_questions");
        exit;
    }

    // ===== SHUFFLE QUESTIONS RANDOMLY PER USER =====
    // Use a seed based on student_id + quiz_id for consistent shuffling per user
    $seed = crc32($student_id . '_' . $quiz_id);
    srand($seed);
    
    // Fisher-Yates shuffle with seeded random
    for ($i = count($questions_array) - 1; $i > 0; $i--) {
        $j = rand(0, $i);
        $temp = $questions_array[$i];
        $questions_array[$i] = $questions_array[$j];
        $questions_array[$j] = $temp;
    }
    
    // Store shuffled questions in session for this quiz
    $_SESSION['shuffled_questions_' . $quiz_id] = $questions_array;
    
    // Also shuffle options for each question
    foreach ($questions_array as &$question) {
        $options = [];
        if (!empty($question['option_a'])) $options[] = ['key' => 'option_a', 'value' => $question['option_a']];
        if (!empty($question['option_b'])) $options[] = ['key' => 'option_b', 'value' => $question['option_b']];
        if (!empty($question['option_c'])) $options[] = ['key' => 'option_c', 'value' => $question['option_c']];
        if (!empty($question['option_d'])) $options[] = ['key' => 'option_d', 'value' => $question['option_d']];
        
        // Shuffle options
        shuffle($options);
        
        // Store shuffled options back
        $question['shuffled_options'] = $options;
    }
    
    $questions = $questions_array;
    $total_questions = count($questions);
}

// Default timer: 20 minutes (1200 seconds)
$remaining_time = $_SESSION['remaining_time'] ?? 1200;
$minutes = floor($remaining_time / 60);
$seconds = $remaining_time % 60;

// Get student name for greeting
$nameStmt = $conn->prepare("SELECT name FROM students WHERE id = ?");
$nameStmt->bind_param("i", $student_id);
$nameStmt->execute();
$student_name = $nameStmt->get_result()->fetch_assoc()['name'] ?? 'Student';
$first_name = explode(' ', $student_name)[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover" />
    <title>NETCODERS - <?= !$show_already_submitted ? htmlspecialchars($quiz['title'] ?? 'Quiz') : 'Quiz Already Submitted' ?></title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel='shortcut icon' href='https://netcoder.in/wp-content/uploads/2023/04/favicon.png'/>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --orange: #ff5532;
            --orange-dark: #e03e1f;
            --orange-light: #fff1ec;
            --orange-glow: rgba(255, 85, 50, 0.3);
            --dark-bg: #0b1120;
            --dark-card: #151f2f;
            --dark-header: #0e1625;
            --text-primary: #ffffff;
            --text-secondary: #e2e8f0;
            --text-muted: #94a3b8;
            --border-color: #1e2a3a;
            --success: #10b981;
            --success-bg: rgba(16, 185, 129, 0.1);
            --warning: #f59e0b;
            --danger: #ef4444;
            --glass-bg: rgba(21, 31, 47, 0.85);
            --glass-border: rgba(255, 85, 50, 0.2);
        }

        html {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.5;
            position: relative;
            padding: 0;
            margin: 0;
            overflow-x: hidden;
        }

        /* ===== ENHANCED ANIMATED BACKGROUND WITH BOXES AND PARTICLES ===== */
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        /* Gradient Base */
        .bg-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 30%, rgba(255, 85, 50, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 80% 70%, rgba(255, 85, 50, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 40% 80%, rgba(255, 85, 50, 0.12) 0%, transparent 45%),
                        radial-gradient(circle at 70% 20%, rgba(255, 85, 50, 0.08) 0%, transparent 55%);
            animation: gradientShift 15s ease-in-out infinite alternate;
        }

        @keyframes gradientShift {
            0% { transform: scale(1) rotate(0deg); opacity: 0.8; }
            50% { transform: scale(1.2) rotate(2deg); opacity: 1; }
            100% { transform: scale(1.1) rotate(-2deg); opacity: 0.9; }
        }

        /* Floating Boxes Layer */
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
            backdrop-filter: blur(1px);
            animation: floatBox 20s infinite ease-in-out;
        }

        /* Box 1 - Top Left */
        .box-1 {
            width: 120px;
            height: 120px;
            top: 5%;
            left: 3%;
            transform: rotate(15deg);
            animation-delay: 0s;
            background: rgba(255, 85, 50, 0.02);
            border-width: 2px;
        }

        /* Box 2 - Top Right */
        .box-2 {
            width: 180px;
            height: 180px;
            top: 2%;
            right: 5%;
            transform: rotate(-10deg);
            animation-delay: 2s;
            background: rgba(255, 85, 50, 0.03);
            border-width: 3px;
        }

        /* Box 3 - Middle Left */
        .box-3 {
            width: 200px;
            height: 200px;
            top: 40%;
            left: -50px;
            transform: rotate(25deg);
            animation-delay: 4s;
            background: rgba(255, 85, 50, 0.02);
            border-width: 2px;
        }

        /* Box 4 - Middle Right */
        .box-4 {
            width: 150px;
            height: 150px;
            top: 50%;
            right: 2%;
            transform: rotate(-20deg);
            animation-delay: 1s;
            background: rgba(255, 85, 50, 0.03);
            border-width: 3px;
        }

        /* Box 5 - Bottom Left */
        .box-5 {
            width: 250px;
            height: 250px;
            bottom: -50px;
            left: 10%;
            transform: rotate(30deg);
            animation-delay: 3s;
            background: rgba(255, 85, 50, 0.02);
            border-width: 2px;
        }

        /* Box 6 - Bottom Right */
        .box-6 {
            width: 160px;
            height: 160px;
            bottom: 15%;
            right: 10%;
            transform: rotate(-15deg);
            animation-delay: 5s;
            background: rgba(255, 85, 50, 0.03);
            border-width: 3px;
        }

        /* Box 7 - Center */
        .box-7 {
            width: 300px;
            height: 300px;
            top: 30%;
            left: 35%;
            transform: rotate(5deg);
            animation-delay: 2.5s;
            background: rgba(255, 85, 50, 0.01);
            border-width: 1px;
            border-color: rgba(255, 85, 50, 0.05);
        }

        /* Box 8 - Top Center */
        .box-8 {
            width: 100px;
            height: 100px;
            top: 15%;
            left: 45%;
            transform: rotate(45deg);
            animation-delay: 1.5s;
            background: rgba(255, 85, 50, 0.04);
            border-width: 2px;
        }

        /* Box 9 - Bottom Center */
        .box-9 {
            width: 140px;
            height: 140px;
            bottom: 20%;
            left: 40%;
            transform: rotate(-30deg);
            animation-delay: 3.5s;
            background: rgba(255, 85, 50, 0.02);
            border-width: 2px;
        }

        /* Box 10 - Scattered */
        .box-10 {
            width: 80px;
            height: 80px;
            top: 70%;
            left: 15%;
            transform: rotate(60deg);
            animation-delay: 4.5s;
            background: rgba(255, 85, 50, 0.03);
            border-width: 2px;
        }

        @keyframes floatBox {
            0% {
                transform: translateY(0) rotate(0deg) scale(1);
                opacity: 0.3;
            }
            25% {
                transform: translateY(-30px) rotate(5deg) scale(1.05);
                opacity: 0.5;
            }
            50% {
                transform: translateY(20px) rotate(-5deg) scale(0.95);
                opacity: 0.4;
            }
            75% {
                transform: translateY(-15px) rotate(8deg) scale(1.02);
                opacity: 0.5;
            }
            100% {
                transform: translateY(0) rotate(0deg) scale(1);
                opacity: 0.3;
            }
        }

        /* Particle Layer */
        .particles-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: rgba(255, 85, 50, 0.2);
            border-radius: 50%;
            pointer-events: none;
            animation: floatParticle 15s infinite linear;
        }

        /* Generate 30 particles with different sizes and positions */
        .particle-1 { width: 4px; height: 4px; top: 10%; left: 20%; animation-duration: 12s; animation-delay: 0s; }
        .particle-2 { width: 6px; height: 6px; top: 30%; left: 50%; animation-duration: 18s; animation-delay: 2s; }
        .particle-3 { width: 3px; height: 3px; top: 70%; left: 80%; animation-duration: 14s; animation-delay: 1s; }
        .particle-4 { width: 8px; height: 8px; top: 15%; left: 90%; animation-duration: 20s; animation-delay: 3s; }
        .particle-5 { width: 5px; height: 5px; top: 85%; left: 30%; animation-duration: 16s; animation-delay: 4s; }
        .particle-6 { width: 7px; height: 7px; top: 45%; left: 15%; animation-duration: 13s; animation-delay: 1.5s; }
        .particle-7 { width: 4px; height: 4px; top: 60%; left: 95%; animation-duration: 17s; animation-delay: 2.5s; }
        .particle-8 { width: 9px; height: 9px; top: 25%; left: 70%; animation-duration: 15s; animation-delay: 0.5s; }
        .particle-9 { width: 5px; height: 5px; top: 90%; left: 10%; animation-duration: 19s; animation-delay: 3.5s; }
        .particle-10 { width: 6px; height: 6px; top: 5%; left: 40%; animation-duration: 11s; animation-delay: 1.2s; }
        .particle-11 { width: 4px; height: 4px; top: 40%; left: 85%; animation-duration: 16s; animation-delay: 2.2s; }
        .particle-12 { width: 7px; height: 7px; top: 75%; left: 55%; animation-duration: 14s; animation-delay: 3.8s; }
        .particle-13 { width: 5px; height: 5px; top: 20%; left: 10%; animation-duration: 18s; animation-delay: 0.8s; }
        .particle-14 { width: 8px; height: 8px; top: 55%; left: 25%; animation-duration: 12s; animation-delay: 4.2s; }
        .particle-15 { width: 3px; height: 3px; top: 80%; left: 45%; animation-duration: 20s; animation-delay: 1.8s; }
        .particle-16 { width: 6px; height: 6px; top: 35%; left: 60%; animation-duration: 15s; animation-delay: 2.8s; }
        .particle-17 { width: 5px; height: 5px; top: 65%; left: 75%; animation-duration: 17s; animation-delay: 0.2s; }
        .particle-18 { width: 7px; height: 7px; top: 50%; left: 5%; animation-duration: 13s; animation-delay: 3.2s; }
        .particle-19 { width: 4px; height: 4px; top: 95%; left: 65%; animation-duration: 19s; animation-delay: 4.5s; }
        .particle-20 { width: 8px; height: 8px; top: 12%; left: 55%; animation-duration: 11s; animation-delay: 1.4s; }
        .particle-21 { width: 5px; height: 5px; top: 72%; left: 35%; animation-duration: 16s; animation-delay: 2.6s; }
        .particle-22 { width: 6px; height: 6px; top: 28%; left: 80%; animation-duration: 14s; animation-delay: 3.4s; }
        .particle-23 { width: 4px; height: 4px; top: 88%; left: 85%; animation-duration: 18s; animation-delay: 0.6s; }
        .particle-24 { width: 7px; height: 7px; top: 42%; left: 45%; animation-duration: 12s; animation-delay: 4.8s; }
        .particle-25 { width: 5px; height: 5px; top: 18%; left: 30%; animation-duration: 20s; animation-delay: 2.4s; }
        .particle-26 { width: 9px; height: 9px; top: 58%; left: 20%; animation-duration: 15s; animation-delay: 1.6s; }
        .particle-27 { width: 3px; height: 3px; top: 78%; left: 70%; animation-duration: 17s; animation-delay: 3.6s; }
        .particle-28 { width: 6px; height: 6px; top: 32%; left: 95%; animation-duration: 13s; animation-delay: 0.4s; }
        .particle-29 { width: 4px; height: 4px; top: 92%; left: 15%; animation-duration: 19s; animation-delay: 4.2s; }
        .particle-30 { width: 8px; height: 8px; top: 8%; left: 75%; animation-duration: 11s; animation-delay: 2.9s; }

        @keyframes floatParticle {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0.2;
            }
            25% {
                transform: translateY(-50px) translateX(30px) rotate(90deg);
                opacity: 0.5;
            }
            50% {
                transform: translateY(40px) translateX(-40px) rotate(180deg);
                opacity: 0.3;
            }
            75% {
                transform: translateY(-30px) translateX(20px) rotate(270deg);
                opacity: 0.5;
            }
            100% {
                transform: translateY(0) translateX(0) rotate(360deg);
                opacity: 0.2;
            }
        }

        /* Grid Lines Layer */
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

        /* Mobile-specific background adjustments */
        @media (max-width: 768px) {
            .box-1, .box-2, .box-3, .box-4, .box-5, .box-6, .box-7, .box-8, .box-9, .box-10 {
                opacity: 0.15;
            }
            
            .particle {
                opacity: 0.1;
            }
            
            .grid-layer {
                background-size: 30px 30px;
            }
        }

        /* Main App Container */
        .app-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 16px;
            padding-top: 90px;
            position: relative;
            z-index: 10;
        }

        /* ===== COOL HEADER WITH WHITE BG ===== */
        .quiz-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: white;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 3px solid var(--orange);
            z-index: 1000;
            padding: 8px 20px;
            box-shadow: 0 8px 25px rgba(255, 85, 50, 0.25);
        }

        .header-content {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            background: var(--orange);
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            font-weight: 800;
            color: white;
            box-shadow: 0 6px 15px var(--orange-glow);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #1e293b;
        }

        .logo-text span {
            color: var(--orange);
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            padding: 6px 14px 6px 10px;
            border-radius: 40px;
            border: 1px solid #e2e8f0;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            color: white;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .user-badge i {
            color: var(--orange);
            font-size: 0.8rem;
        }

        /* Shuffle Indicator */
        .shuffle-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255, 85, 50, 0.15);
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.75rem;
            color: var(--text-secondary);
            border: 1px solid var(--glass-border);
            margin-left: 10px;
        }

        .shuffle-badge i {
            color: var(--orange);
            font-size: 0.8rem;
        }

        /* Timer Card */
        .timer-card {
             backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 30px;
            padding: 16px 20px;
            margin-bottom: 20px;
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .timer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .timer-icon {
            width: 52px;
            height: 52px;
            background: rgba(255, 85, 50, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--orange);
        }

        .timer-icon i {
            font-size: 1.6rem;
            color: var(--orange);
        }

        .timer-text {
            display: flex;
            flex-direction: column;
        }

        .timer-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timer-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--orange);
            line-height: 1.2;
            transition: color 0.3s ease;
        }

        .progress-pill {
            background: rgba(255, 255, 255, 0.05);
            padding: 8px 18px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-pill span {
            color: var(--orange);
            font-weight: 800;
        }

        /* Progress Bar */
        .progress-container {
             backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 16px;
            margin-bottom: 20px;
            border: 1px solid var(--glass-border);
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .progress-header span {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .progress-header strong {
            color: var(--orange);
            font-size: 1.1rem;
        }

        .progress-bar-bg {
            width: 100%;
            height: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--orange), #ff8866);
            border-radius: 20px;
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Question Card */
        .question-card {
             backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 32px;
            padding: 28px 24px;
            margin-bottom: 20px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.5);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .question-number {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 85, 50, 0.15);
            padding: 8px 18px;
            border-radius: 40px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 85, 50, 0.3);
        }

        .question-number i {
            color: var(--orange);
        }

        .question-number span {
            font-weight: 600;
            color: var(--text-secondary);
        }

        .question-number strong {
            color: var(--orange);
            font-size: 1.1rem;
        }

        .question-text {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 28px;
            line-height: 1.4;
            color: var(--text-primary);
        }

        /* Options */
        .options-container {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .option-item {
            position: relative;
            animation: fadeIn 0.5s ease;
            animation-fill-mode: both;
        }

        .option-item:nth-child(1) { animation-delay: 0.1s; }
        .option-item:nth-child(2) { animation-delay: 0.2s; }
        .option-item:nth-child(3) { animation-delay: 0.3s; }
        .option-item:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .option-item input[type="radio"] {
            display: none;
        }

        .option-label {
            display: block;
            padding: 18px 22px 18px 58px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            font-weight: 500;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            word-break: break-word;
        }

        .option-label::before {
            content: '';
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border: 2px solid var(--text-muted);
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .option-label:hover {
            background: rgba(255, 85, 50, 0.08);
            border-color: var(--orange);
            transform: translateY(-2px);
        }

        input[type="radio"]:checked + .option-label {
            background: rgba(255, 85, 50, 0.15);
            border-color: var(--orange);
            color: white;
            font-weight: 600;
        }

        input[type="radio"]:checked + .option-label::before {
            border-color: var(--orange);
            background: var(--orange);
            box-shadow: inset 0 0 0 5px var(--dark-card);
        }

        /* Navigation Buttons */
        .nav-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            margin-bottom: 25px;
        }

        .nav-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(5px);
        }

        .nav-btn:not(:disabled):hover {
            background: var(--orange);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 12px 30px var(--orange-glow);
        }

        .nav-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: white;
            border: none;
            box-shadow: 0 10px 30px var(--orange-glow);
        }

        /* Question Dots */
        .dots-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin: 25px 0 15px;
        }

        .question-dot {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .question-dot.answered {
            background: var(--orange);
            color: white;
            border-color: var(--orange);
            box-shadow: 0 0 20px var(--orange-glow);
        }

        .question-dot.current {
            transform: scale(1.15);
            border: 2px solid white;
            box-shadow: 0 0 25px var(--orange);
        }

        .question-dot:hover {
            transform: scale(1.1);
            border-color: var(--orange);
        }

        /* Quick Nav FAB */
        .quick-nav-fab {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 10px 35px var(--orange-glow);
            cursor: pointer;
            transition: all 0.25s ease;
            z-index: 100;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .quick-nav-fab:hover {
            transform: scale(1.1);
        }

        .quick-nav-fab:active {
            transform: scale(0.95);
        }

        /* ===== BACK BUTTON POPUP ===== */
        .back-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(11, 17, 32, 0.97);
            backdrop-filter: blur(15px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
        }

        .back-popup-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .back-popup-card {
            background: linear-gradient(145deg, var(--dark-card), #1a2637);
            border-radius: 48px;
            padding: 40px 32px;
            max-width: 450px;
            width: 90%;
            border: 3px solid var(--orange);
            text-align: center;
            transform: scale(0.8) translateY(20px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 30px 60px rgba(255, 85, 50, 0.3);
            position: relative;
            overflow: hidden;
        }

        .back-popup-overlay.show .back-popup-card {
            transform: scale(1) translateY(0);
        }

        .back-popup-card::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -30%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,85,50,0.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .back-popup-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 85, 50, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            border: 3px solid var(--orange);
            animation: popupPulse 2s infinite;
        }

        @keyframes popupPulse {
            0% { box-shadow: 0 0 0 0 var(--orange-glow); }
            70% { box-shadow: 0 0 0 15px rgba(255, 85, 50, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 85, 50, 0); }
        }

        .back-popup-icon i {
            font-size: 4rem;
            color: var(--orange);
        }

        .back-popup-title {
            font-size: 2.4rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: white;
            line-height: 1.2;
        }

        .back-popup-title span {
            color: var(--orange);
        }

        .back-popup-message {
            color: var(--text-secondary);
            margin-bottom: 30px;
            line-height: 1.7;
            font-size: 1.1rem;
            max-width: 320px;
            margin-left: auto;
            margin-right: auto;
        }

        .back-popup-buttons {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .back-popup-btn {
            flex: 1;
            padding: 18px;
            border-radius: 60px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .back-popup-btn-primary {
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: white;
            box-shadow: 0 10px 25px var(--orange-glow);
        }

        .back-popup-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px var(--orange);
        }

        .back-popup-btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            border: 2px solid var(--glass-border);
        }

        .back-popup-btn-secondary:hover {
            background: rgba(255, 85, 50, 0.15);
            border-color: var(--orange);
            transform: translateY(-3px);
        }

        /* General Popups */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(11, 17, 32, 0.95);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .popup-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .popup-card {
             border-radius: 36px;
            padding: 32px 28px;
            max-width: 400px;
            width: 90%;
            border: 2px solid var(--orange);
            text-align: center;
            transform: scale(0.9);
            transition: all 0.3s ease;
            box-shadow: 0 20px 40px rgba(255, 85, 50, 0.2);
        }

        .popup-overlay.show .popup-card {
            transform: scale(1);
        }

        .popup-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 85, 50, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 2px solid var(--orange);
        }

        .popup-icon i {
            font-size: 3rem;
            color: var(--orange);
        }

        .popup-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: var(--text-primary);
        }

        .popup-message {
            color: var(--text-muted);
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 1rem;
        }

        .popup-buttons {
            display: flex;
            gap: 12px;
        }

        .popup-btn {
            flex: 1;
            padding: 16px;
            border-radius: 40px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 1rem;
        }

        .popup-btn-primary {
            background: var(--orange);
            color: white;
        }

        .popup-btn-primary:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
        }

        .popup-btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
        }

        .popup-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .countdown-text {
            font-size: 1.2rem;
            color: var(--orange);
            font-weight: 700;
            margin-top: 20px;
        }

        /* Already Submitted Card */
        .submitted-card {
            background: linear-gradient(145deg, var(--dark-card), #1a2637);
            border-radius: 48px;
            padding: 40px 30px;
            margin: 20px 0;
            border: 3px solid var(--orange);
            text-align: center;
            box-shadow: 0 30px 60px rgba(255, 85, 50, 0.3);
            position: relative;
            overflow: hidden;
        }

        .submitted-card::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -30%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,85,50,0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .submitted-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 85, 50, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            border: 3px solid var(--orange);
            animation: submittedPulse 2s infinite;
        }

        @keyframes submittedPulse {
            0% { box-shadow: 0 0 0 0 var(--orange-glow); }
            70% { box-shadow: 0 0 0 20px rgba(255, 85, 50, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 85, 50, 0); }
        }

        .submitted-icon i {
            font-size: 4rem;
            color: var(--orange);
        }

        .submitted-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
            color: white;
            line-height: 1.2;
        }

        .submitted-title span {
            color: var(--orange);
        }

        .submitted-message {
            color: var(--text-secondary);
            margin-bottom: 25px;
            line-height: 1.7;
            font-size: 1.1rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .submitted-info {
            background: rgba(255, 85, 50, 0.1);
            border-radius: 30px;
            padding: 20px;
            margin: 25px 0;
            border: 1px solid var(--glass-border);
        }

        .submitted-info p {
            color: var(--text-secondary);
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .submitted-info i {
            color: var(--orange);
            margin-right: 10px;
        }

        .submitted-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: white;
            padding: 18px 36px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 10px 25px var(--orange-glow);
            border: none;
            cursor: pointer;
            width: auto;
            min-width: 250px;
        }

        .submitted-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px var(--orange);
        }

        .submitted-btn i {
            font-size: 1.3rem;
        }

        /* Return Button Card (After Submission) */
        .return-card {
            background: linear-gradient(145deg, var(--dark-card), #1a2637);
            border-radius: 36px;
            padding: 30px;
            margin: 20px 0;
            border: 2px solid var(--orange);
            text-align: center;
            box-shadow: 0 20px 40px rgba(255, 85, 50, 0.2);
        }

        .return-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 85, 50, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 2px solid var(--orange);
        }

        .return-icon i {
            font-size: 2.5rem;
            color: var(--orange);
        }

        .return-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 10px;
            color: white;
        }

        .return-text {
            color: var(--text-secondary);
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .return-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: white;
            padding: 16px 32px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 10px 25px var(--orange-glow);
            border: none;
            cursor: pointer;
            width: auto;
            min-width: 200px;
        }

        .return-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px var(--orange);
        }

        .return-btn i {
            font-size: 1.2rem;
        }

        /* Mobile Optimizations */
        @media (max-width: 480px) {
            .app-container {
                padding: 12px;
                padding-top: 85px;
            }

            .logo-text {
                font-size: 1.3rem;
            }

            .logo-icon {
                width: 38px;
                height: 38px;
                font-size: 1.4rem;
            }

            .user-name {
                display: none;
            }

            .user-badge {
                padding: 4px 10px 4px 6px;
            }

            .timer-card {
                padding: 14px 16px;
            }

            .timer-icon {
                width: 46px;
                height: 46px;
            }

            .timer-value {
                font-size: 1.7rem;
            }

            .question-card {
                padding: 22px 18px;
            }

            .question-text {
                font-size: 1.15rem;
            }

            .option-label {
                 font-size: 0.95rem;
            }

            .nav-btn {
                padding: 14px;
                font-size: 0.95rem;
            }

            .question-dot {
                width: 40px;
                height: 40px;
                font-size: 0.95rem;
            }

            .quick-nav-fab {
                width: 52px;
                height: 52px;
                font-size: 1.5rem;
                bottom: 15px;
                right: 15px;
            }

            .back-popup-card {
                padding: 30px 20px;
            }

            .back-popup-title {
                font-size: 2rem;
            }

            .back-popup-message {
                font-size: 1rem;
            }

            .back-popup-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .submitted-card {
                padding: 30px 20px;
            }

            .submitted-title {
                font-size: 2rem;
            }

            .submitted-message {
                font-size: 1rem;
            }

            .submitted-btn {
                padding: 16px 28px;
                font-size: 1.1rem;
                width: 100%;
            }

            .return-card {
                padding: 25px 20px;
            }

            .return-title {
                font-size: 1.5rem;
            }

            .return-btn {
                padding: 14px 28px;
                font-size: 1rem;
                width: 100%;
            }
        }

        /* Touch Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .option-label:hover {
                transform: none;
            }
            
            .nav-btn:hover {
                transform: none;
            }
            
            .question-dot:hover {
                transform: none;
            }
        }
    </style>
</head>
<body>
    <!-- ===== ENHANCED BACKGROUND WITH BOXES, PARTICLES AND GRID ===== -->
    <div class="bg-container">
        <div class="bg-gradient"></div>
        
        <div class="boxes-layer">
            <div class="box box-1"></div>
            <div class="box box-2"></div>
            <div class="box box-3"></div>
            <div class="box box-4"></div>
            <div class="box box-5"></div>
            <div class="box box-6"></div>
            <div class="box box-7"></div>
            <div class="box box-8"></div>
            <div class="box box-9"></div>
            <div class="box box-10"></div>
        </div>
        
        <div class="particles-layer">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
            <div class="particle particle-4"></div>
            <div class="particle particle-5"></div>
            <div class="particle particle-6"></div>
            <div class="particle particle-7"></div>
            <div class="particle particle-8"></div>
            <div class="particle particle-9"></div>
            <div class="particle particle-10"></div>
            <div class="particle particle-11"></div>
            <div class="particle particle-12"></div>
            <div class="particle particle-13"></div>
            <div class="particle particle-14"></div>
            <div class="particle particle-15"></div>
            <div class="particle particle-16"></div>
            <div class="particle particle-17"></div>
            <div class="particle particle-18"></div>
            <div class="particle particle-19"></div>
            <div class="particle particle-20"></div>
            <div class="particle particle-21"></div>
            <div class="particle particle-22"></div>
            <div class="particle particle-23"></div>
            <div class="particle particle-24"></div>
            <div class="particle particle-25"></div>
            <div class="particle particle-26"></div>
            <div class="particle particle-27"></div>
            <div class="particle particle-28"></div>
            <div class="particle particle-29"></div>
            <div class="particle particle-30"></div>
        </div>
        
        <div class="grid-layer"></div>
    </div>

    <!-- Cool Header with White BG -->
    <header class="quiz-header">
        <div class="header-content">
            <div class="logo-area">
                            <img src="../assests/logo.png" alt="NetCoders" height="50px"class="logo-img" onerror="this.src='https://via.placeholder.com/120x48?text=NETCODERS'">

              </div>
            <div class="user-badge">
                <div class="user-avatar"><?= strtoupper(substr($first_name, 0, 1)) ?></div>
                <span class="user-name"><?= htmlspecialchars($first_name) ?></span>
                <i class="bi bi-chevron-down"></i>
            </div>
        </div>
    </header>

    <div class="app-container">
        <?php if ($show_already_submitted): ?>
            <!-- ALREADY SUBMITTED CARD - SHOW WHEN USER TRIES TO RETAKE -->
            <div class="submitted-card">
                <div class="submitted-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h1 class="submitted-title">Already <span>Submitted!</span></h1>
                <p class="submitted-message">You have already taken and submitted this test. Each test can only be attempted once.</p>
                
                <div class="submitted-info">
                    <p><i class="bi bi-calendar-check"></i> Test completed successfully</p>
                    <p><i class="bi bi-lock-fill"></i> Test is now locked</p>
                    <p><i class="bi bi-trophy-fill"></i> Great job!</p>
                </div>
                
                <a href="../student/index.php" class="submitted-btn">
                    <i class="bi bi-arrow-left"></i> Return to Dashboard
                </a>
                
                <div style="margin-top: 20px; font-size: 0.9rem; color: var(--text-muted);">
                    <i class="bi bi-exclamation-circle"></i> Multiple attempts are not allowed
                </div>
            </div>
        <?php else: ?>
            <!-- Timer Card -->
            <div class="timer-card">
                <div class="timer-info">
                    <div class="timer-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="timer-text">
                        <span class="timer-label">Time Remaining</span>
                        <span class="timer-value" id="timerDisplay"><?= sprintf('%02d:%02d', $minutes, $seconds) ?></span>
                    </div>
                </div>
                <div class="progress-pill">
                    <span id="currentQuestionNum">1</span>/<span id="totalQuestions"><?= $total_questions ?></span>
                  
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-header">
                    <span>Overall Progress</span>
                    <strong id="progressPercentage">0%</strong>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-fill" id="quizProgress" style="width: 0%"></div>
                </div>
            </div>

            <!-- Questions Form -->
            <form id="quizForm" method="POST" action="submit_quiz.php">
                <?php 
                $index = 1; 
                foreach ($questions as $q): 
                ?>
                    <div class="question-card" id="q_<?= $index ?>" data-original-id="<?= $q['id'] ?>" style="display: <?= $index === 1 ? 'block' : 'none' ?>;">
                        <div class="question-number">
                            <i class="bi bi-question-circle"></i>
                            <span>Question <strong><?= $index ?></strong> of <?= $total_questions ?></span>
                        </div>
                        <div class="question-text">
                            <?= htmlspecialchars($q['question_text']) ?>
                        </div>
                        <div class="options-container">
                            <?php 
                            // Use shuffled options
                            foreach ($q['shuffled_options'] as $opt): 
                            ?>
                                <div class="option-item">
                                    <input type="radio" 
                                           name="question_<?= $q['id'] ?>" 
                                           value="<?= $opt['key'] ?>" 
                                           id="q<?= $q['id'] ?>_<?= $opt['key'] ?>"
                                           data-original-question="<?= $index ?>">
                                    <label for="q<?= $q['id'] ?>_<?= $opt['key'] ?>" class="option-label">
                                        <?= htmlspecialchars($opt['value']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php 
                $index++; 
                endforeach; 
                ?>

                <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
                <input type="hidden" name="student_id" value="<?= $student_id ?>">
                <input type="hidden" id="remaining_time" name="remaining_time" value="<?= $remaining_time ?>">

                <!-- Navigation Buttons -->
                <div class="nav-buttons">
                    <button type="button" id="prevBtn" class="nav-btn" disabled>
                        <i class="bi bi-chevron-left"></i> Prev
                    </button>
                    <button type="button" id="nextBtn" class="nav-btn">
                        Next <i class="bi bi-chevron-right"></i>
                    </button>
                    <button type="button" id="submitBtn" class="nav-btn submit-btn">
                        <i class="bi bi-check-lg"></i> Submit
                    </button>
                </div>
            </form>

            <!-- Question Dots -->
            <div class="dots-container" id="questionDots"></div>

            <!-- Return Card (Hidden by default, shown after submission) -->
            <div class="return-card" id="returnCard" style="display: none;">
                <div class="return-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <h3 class="return-title">Quiz Completed!</h3>
                <p class="return-text">Thank you for completing the quiz. Your answers have been recorded.</p>
                <a href="../student/index.php" class="return-btn">
                    <i class="bi bi-arrow-left"></i> Return to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$show_already_submitted): ?>
    <!-- Quick Navigation FAB -->
    <div class="quick-nav-fab" id="quickNavFab">
        <i class="bi bi-grid-3x3-gap-fill"></i>
    </div>

    <!-- Quick Navigation Popup -->
    <div class="popup-overlay" id="quickNavPopup">
        <div class="popup-card">
            <div class="popup-icon">
                <i class="bi bi-question-circle"></i>
            </div>
            <h3 class="popup-title">Jump to Question</h3>
            <div class="dots-container" id="quickNavDots" style="justify-content: center;"></div>
            <div class="popup-buttons" style="margin-top: 20px;">
                <button class="popup-btn popup-btn-secondary" id="closeQuickNav">Close</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Popup (Submit) -->
    <div class="popup-overlay" id="confirmPopup">
        <div class="popup-card">
            <div class="popup-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h3 class="popup-title">Submit Quiz?</h3>
            <p class="popup-message">Are you sure you want to submit? You won't be able to change your answers after submission.</p>
            <div class="popup-buttons">
                <button class="popup-btn popup-btn-secondary" id="cancelSubmit">Cancel</button>
                <button class="popup-btn popup-btn-primary" id="confirmSubmit">Submit</button>
            </div>
        </div>
    </div>

    <!-- BACK BUTTON POPUP -->
    <div class="back-popup-overlay" id="backPopup">
        <div class="back-popup-card">
            <div class="back-popup-icon">
                <i class="bi bi-arrow-left-circle"></i>
            </div>
            <h3 class="back-popup-title">Wait! <span>Are you sure?</span></h3>
            <p class="back-popup-message">If you go back now, your progress will be lost. Do you want to continue the test or submit?</p>
            <div class="back-popup-buttons">
                <button class="back-popup-btn back-popup-btn-secondary" id="backCancel">
                    <i class="bi bi-arrow-left"></i> Go Back
                </button>
                <button class="back-popup-btn back-popup-btn-primary" id="backContinue">
                    <i class="bi bi-play-fill"></i> Continue Test
                </button>
            </div>
            <div style="margin-top: 20px; font-size: 0.9rem; color: var(--text-muted);">
                <i class="bi bi-exclamation-circle"></i> Your answers are safe
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        <?php if (!$show_already_submitted): ?>
        // DOM Elements
        const questions = document.querySelectorAll('.question-card');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const timerDisplay = document.getElementById('timerDisplay');
        const remInput = document.getElementById('remaining_time');
        const currentQuestionNum = document.getElementById('currentQuestionNum');
        const totalQuestions = document.getElementById('totalQuestions');
        const quizProgress = document.getElementById('quizProgress');
        const progressPercentage = document.getElementById('progressPercentage');
        const questionDots = document.getElementById('questionDots');
        const quickNavFab = document.getElementById('quickNavFab');
        const quickNavPopup = document.getElementById('quickNavPopup');
        const quickNavDots = document.getElementById('quickNavDots');
        const closeQuickNav = document.getElementById('closeQuickNav');
        const confirmPopup = document.getElementById('confirmPopup');
        const cancelSubmit = document.getElementById('cancelSubmit');
        const confirmSubmit = document.getElementById('confirmSubmit');
        const returnCard = document.getElementById('returnCard');
        
        // Back Popup Elements
        const backPopup = document.getElementById('backPopup');
        const backCancel = document.getElementById('backCancel');
        const backContinue = document.getElementById('backContinue');

        // Quiz State
        let currentIndex = 0;
        let timeRemaining = parseInt(remInput.value, 10);
        const totalQuestionsCount = questions.length;
        const answeredQuestions = new Set();
        let timerInterval;
        let backButtonPressed = false;
        let quizSubmitted = false;

        // Store original question order for reference
        const originalQuestionIds = [];
        questions.forEach((q, index) => {
            originalQuestionIds[index] = q.dataset.originalId;
        });

        // Initialize Question Dots
        function initQuestionDots() {
            questionDots.innerHTML = '';
            quickNavDots.innerHTML = '';
            
            for (let i = 0; i < totalQuestionsCount; i++) {
                const dot = document.createElement('div');
                dot.className = 'question-dot';
                dot.textContent = i + 1;
                dot.dataset.index = i;
                
                dot.addEventListener('click', () => {
                    currentIndex = i;
                    showQuestion(currentIndex);
                    quickNavPopup.classList.remove('show');
                });
                
                questionDots.appendChild(dot.cloneNode(true));
                quickNavDots.appendChild(dot);
            }
        }

        // Show Question
        function showQuestion(index) {
            questions.forEach((q, i) => {
                q.style.display = i === index ? 'block' : 'none';
            });
            
            prevBtn.disabled = index === 0;
            nextBtn.disabled = index === totalQuestionsCount - 1;
            
            currentQuestionNum.textContent = index + 1;
            updateProgress();
        }

        // Update Progress
        function updateProgress() {
            const answered = answeredQuestions.size;
            const percentage = Math.round((answered / totalQuestionsCount) * 100);
            quizProgress.style.width = `${percentage}%`;
            progressPercentage.textContent = `${percentage}%`;
            
            document.querySelectorAll('.question-dot').forEach((dot, index) => {
                dot.classList.toggle('answered', answeredQuestions.has(index));
                dot.classList.toggle('current', index === currentIndex);
            });
        }

        // Timer Function
        function updateTimer() {
            if (quizSubmitted) return;
            
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                submitQuiz();
                return;
            }
            
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Color coding
            if (timeRemaining < 60) {
                timerDisplay.style.color = '#ef4444';
            } else if (timeRemaining < 300) {
                timerDisplay.style.color = '#f59e0b';
            } else {
                timerDisplay.style.color = '#ff5532';
            }
            
            remInput.value = timeRemaining;
            timeRemaining--;
        }

        // Start Timer
        function startTimer() {
            clearInterval(timerInterval);
            timerInterval = setInterval(updateTimer, 1000);
        }

        // Submit Quiz - Modified to show return card
        function submitQuiz() {
            if (quizSubmitted) return;
            quizSubmitted = true;
            
            clearInterval(timerInterval);
            
            // Hide all quiz elements
            document.querySelectorAll('.question-card').forEach(q => q.style.display = 'none');
            document.querySelector('.nav-buttons').style.display = 'none';
            document.querySelector('.dots-container').style.display = 'none';
            document.querySelector('.quick-nav-fab').style.display = 'none';
            
            // Show return card
            returnCard.style.display = 'block';
            
            // Submit form data
            document.getElementById('quizForm').submit();
        }

        // Track Answered Questions
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const questionCard = e.target.closest('.question-card');
                const questionIndex = Array.from(questions).indexOf(questionCard);
                answeredQuestions.add(questionIndex);
                updateProgress();
            });
        });

        // Event Listeners
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                showQuestion(currentIndex);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < totalQuestionsCount - 1) {
                currentIndex++;
                showQuestion(currentIndex);
            }
        });

        submitBtn.addEventListener('click', () => {
            confirmPopup.classList.add('show');
        });

        cancelSubmit.addEventListener('click', () => {
            confirmPopup.classList.remove('show');
        });

        confirmSubmit.addEventListener('click', () => {
            confirmPopup.classList.remove('show');
            submitQuiz();
        });

        // Quick Navigation
        quickNavFab.addEventListener('click', () => {
            quickNavPopup.classList.add('show');
        });

        closeQuickNav.addEventListener('click', () => {
            quickNavPopup.classList.remove('show');
        });

        // Close popups when clicking outside
        document.querySelectorAll('.popup-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.classList.remove('show');
                }
            });
        });

        // ===== BACK BUTTON DETECTION =====
        window.addEventListener('popstate', function(event) {
            // Prevent default back behavior
            event.preventDefault();
            
            if (!quizSubmitted && !backButtonPressed) {
                backButtonPressed = true;
                // Pause timer
                clearInterval(timerInterval);
                // Show back popup
                backPopup.classList.add('show');
            }
        });

        // Push a dummy state to enable popstate
        history.pushState({ page: 'quiz' }, 'Quiz', window.location.href);

        // Back popup buttons
        backCancel.addEventListener('click', function() {
            // User wants to go back - redirect to index.php
            window.location.href = '../student/index.php';
        });

        backContinue.addEventListener('click', function() {
            // User wants to continue - resume timer and close popup
            backPopup.classList.remove('show');
            startTimer();
            backButtonPressed = false;
        });

        // Close back popup if clicked outside (optional)
        backPopup.addEventListener('click', (e) => {
            if (e.target === backPopup) {
                backPopup.classList.remove('show');
                startTimer();
                backButtonPressed = false;
            }
        });

        // Keyboard Navigation
        document.addEventListener('keydown', (e) => {
            if (quizSubmitted) return;
            
            if (e.key === 'ArrowLeft' && currentIndex > 0 && !backPopup.classList.contains('show')) {
                currentIndex--;
                showQuestion(currentIndex);
            } else if (e.key === 'ArrowRight' && currentIndex < totalQuestionsCount - 1 && !backPopup.classList.contains('show')) {
                currentIndex++;
                showQuestion(currentIndex);
            }
        });

        // Initialize
        initQuestionDots();
        showQuestion(currentIndex);
        startTimer();
        updateProgress();

        // Prevent zoom on double tap
        document.addEventListener('touchstart', (e) => {
            if (e.touches.length > 1) {
                e.preventDefault();
            }
        }, { passive: false });

        // Smooth touch scrolling
        document.querySelector('.app-container').addEventListener('touchstart', () => {}, { passive: true });
        <?php endif; ?>
    </script>
</body>
</html>