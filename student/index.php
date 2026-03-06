 <?php
include('../db.php');
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch student name
$student_sql = "SELECT name FROM students WHERE id = ?";
$student_stmt = $conn->prepare($student_sql);
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student_name = "Student";
if ($student_result->num_rows > 0) {
    $student_data = $student_result->fetch_assoc();
    $student_name = $student_data['name'];
}

// Fetch completed quiz IDs for this student
$completed_sql = "SELECT quiz_id FROM quiz_results WHERE student_id = ?";
$completed_stmt = $conn->prepare($completed_sql);
$completed_stmt->bind_param("i", $student_id);
$completed_stmt->execute();
$completed_result = $completed_stmt->get_result();
$completed_quizzes = [];
while ($row = $completed_result->fetch_assoc()) {
    $completed_quizzes[] = $row['quiz_id'];
}

// ---- SHOW ONLY ONE CRITICAL TEST (first published quiz) ----
$critical_quiz_sql = "SELECT q.id, q.title, q.description FROM quizzes q 
                      WHERE q.status = 'published' 
                      ORDER BY q.id ASC LIMIT 1";
$critical_quiz_stmt = $conn->prepare($critical_quiz_sql);
$critical_quiz_stmt->execute();
$critical_quiz_result = $critical_quiz_stmt->get_result();
$critical_quiz = $critical_quiz_result->fetch_assoc();

// For progress calculation
$total_quizzes = 1; 
$completed_count = 0;
if ($critical_quiz && in_array($critical_quiz['id'], $completed_quizzes)) {
    $completed_count = 1;
}

// Get first name only
$first_name = explode(' ', $student_name)[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>NETCODERS - Critical Test</title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            --orange-light: #ffeeea;
            --orange-glow: rgba(255, 85, 50, 0.25);
            --dark-bg: #0b1120;
            --dark-card: #151f2f;
            --text-primary: #ffffff;
            --text-secondary: #e2e8f0;
            --text-muted: #94a3b8;
            --border-color: #1e2a3a;
            --success: #10b981;
            --success-bg: rgba(16, 185, 129, 0.1);
            --header-bg: #ffffff;
            --header-text: #1e293b;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            position: relative;
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

        /* Box 11 - New */
        .box-11 {
            width: 220px;
            height: 220px;
            top: 60%;
            left: 70%;
            transform: rotate(-45deg);
            animation-delay: 1.2s;
            background: rgba(255, 85, 50, 0.02);
            border-width: 2px;
        }

        /* Box 12 - New */
        .box-12 {
            width: 90px;
            height: 90px;
            top: 80%;
            left: 85%;
            transform: rotate(75deg);
            animation-delay: 3.2s;
            background: rgba(255, 85, 50, 0.04);
            border-width: 2px;
        }

        /* Box 13 - New */
        .box-13 {
            width: 180px;
            height: 180px;
            top: 10%;
            left: 60%;
            transform: rotate(-5deg);
            animation-delay: 2.8s;
            background: rgba(255, 85, 50, 0.03);
            border-width: 2px;
        }

        /* Box 14 - New */
        .box-14 {
            width: 130px;
            height: 130px;
            top: 45%;
            left: 20%;
            transform: rotate(85deg);
            animation-delay: 4.2s;
            background: rgba(255, 85, 50, 0.02);
            border-width: 3px;
        }

        /* Box 15 - New */
        .box-15 {
            width: 280px;
            height: 280px;
            top: 65%;
            left: 25%;
            transform: rotate(15deg);
            animation-delay: 0.8s;
            background: rgba(255, 85, 50, 0.01);
            border-width: 1px;
            border-color: rgba(255, 85, 50, 0.06);
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

        /* Generate 50 particles with different sizes and positions */
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
        .particle-31 { width: 5px; height: 5px; top: 22%; left: 33%; animation-duration: 14s; animation-delay: 3.1s; }
        .particle-32 { width: 7px; height: 7px; top: 67%; left: 47%; animation-duration: 18s; animation-delay: 1.9s; }
        .particle-33 { width: 4px; height: 4px; top: 38%; left: 72%; animation-duration: 12s; animation-delay: 4.7s; }
        .particle-34 { width: 6px; height: 6px; top: 81%; left: 92%; animation-duration: 16s; animation-delay: 2.1s; }
        .particle-35 { width: 8px; height: 8px; top: 14%; left: 12%; animation-duration: 19s; animation-delay: 3.9s; }
        .particle-36 { width: 5px; height: 5px; top: 48%; left: 63%; animation-duration: 13s; animation-delay: 0.9s; }
        .particle-37 { width: 7px; height: 7px; top: 73%; left: 18%; animation-duration: 17s; animation-delay: 2.7s; }
        .particle-38 { width: 4px; height: 4px; top: 27%; left: 83%; animation-duration: 15s; animation-delay: 4.3s; }
        .particle-39 { width: 6px; height: 6px; top: 91%; left: 40%; animation-duration: 11s; animation-delay: 1.1s; }
        .particle-40 { width: 9px; height: 9px; top: 54%; left: 54%; animation-duration: 20s; animation-delay: 3.7s; }
        .particle-41 { width: 5px; height: 5px; top: 33%; left: 9%; animation-duration: 14s; animation-delay: 2.3s; }
        .particle-42 { width: 7px; height: 7px; top: 77%; left: 77%; animation-duration: 18s; animation-delay: 4.9s; }
        .particle-43 { width: 4px; height: 4px; top: 44%; left: 96%; animation-duration: 12s; animation-delay: 0.7s; }
        .particle-44 { width: 6px; height: 6px; top: 62%; left: 29%; animation-duration: 16s; animation-delay: 3.5s; }
        .particle-45 { width: 8px; height: 8px; top: 19%; left: 50%; animation-duration: 19s; animation-delay: 1.3s; }
        .particle-46 { width: 5px; height: 5px; top: 86%; left: 68%; animation-duration: 13s; animation-delay: 4.1s; }
        .particle-47 { width: 7px; height: 7px; top: 41%; left: 37%; animation-duration: 17s; animation-delay: 2.5s; }
        .particle-48 { width: 4px; height: 4px; top: 59%; left: 14%; animation-duration: 15s; animation-delay: 0.3s; }
        .particle-49 { width: 6px; height: 6px; top: 24%; left: 88%; animation-duration: 11s; animation-delay: 3.3s; }
        .particle-50 { width: 9px; height: 9px; top: 96%; left: 22%; animation-duration: 20s; animation-delay: 4.5s; }

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
            .box {
                opacity: 0.1;
            }
            
            .particle {
                opacity: 0.1;
            }
            
            .grid-layer {
                background-size: 30px 30px;
            }
        }

        /* ===== WHITE HEADER WITH LOGO ===== */
        .white-header {
            background: var(--header-bg);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: 0 8px 20px rgba(255, 85, 50, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--orange);
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        .brand-text {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--header-text);
            letter-spacing: -0.5px;
        }

        .brand-text span {
            color: var(--orange);
            background: var(--orange-light);
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 1rem;
            margin-left: 8px;
            font-weight: 700;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8fafc;
            padding: 6px 16px 6px 12px;
            border-radius: 60px;
            border: 1px solid #e2e8f0;
        }

        .header-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            border-radius: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
            text-transform: uppercase;
        }

        .header-user-info {
            display: flex;
            flex-direction: column;
        }

        .header-user-name {
            font-weight: 700;
            color: #1e293b;
            line-height: 1.3;
            font-size: 0.95rem;
        }

        .header-user-badge {
            font-size: 0.7rem;
            color: var(--orange);
            font-weight: 600;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: 1px solid #e2e8f0;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            color: var(--orange);
            font-size: 1.3rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .mobile-menu-btn:hover {
            background: var(--orange-light);
            border-color: var(--orange);
        }

        /* Desktop Navigation */
        .desktop-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            padding: 8px 16px;
            border-radius: 40px;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .nav-link i {
            color: var(--orange);
            font-size: 1rem;
        }

        .nav-link:hover {
            background: var(--orange-light);
            color: var(--orange);
        }

        .nav-link.logout {
            background: #fee2e2;
            color: #dc2626;
        }

        .nav-link.logout i {
            color: #dc2626;
        }

        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 24px;
            position: relative;
            z-index: 10;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, rgba(255, 85, 50, 0.05) 0%, transparent 100%);
            border: 1px solid rgba(255, 85, 50, 0.15);
            border-radius: 28px;
            padding: 28px 32px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
            backdrop-filter: blur(10px);
        }

        .welcome-text h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
        }

        .welcome-text h1 span {
            color: var(--orange);
            position: relative;
        }

        .welcome-text h1 span::after {
            content: '';
            position: absolute;
            bottom: 6px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(255, 85, 50, 0.25);
            border-radius: 10px;
            z-index: -1;
        }

        .welcome-text p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .welcome-stats {
            display: flex;
            gap: 32px;
                padding: 18px 28px;
             border: 1px solid rgba(255, 85, 50, 0.15);
            backdrop-filter: blur(8px);
        }

        .stat-block {
            text-align: center;
            min-width: 70px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--orange);
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Progress Card */
        .progress-card {
            background: transparent;
            border-radius: 24px;
            padding: 24px 28px;
            margin-bottom: 35px;
            border: 1px solid rgba(255, 85, 50, 0.15);
            display: flex;
            align-items: center;
            gap: 28px;
            flex-wrap: wrap;
            backdrop-filter: blur(10px);
        }

        .progress-info {
            flex: 1;
            min-width: 180px;
        }

        .progress-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-percentage {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--orange);
            line-height: 1;
            margin-bottom: 5px;
        }

        .progress-detail {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .progress-bar-container {
            flex: 2;
            height: 12px;
            background: #1e2a3a;
            border-radius: 20px;
            overflow: hidden;
            min-width: 200px;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--orange), #ff8866);
            width: <?= ($completed_count / max($total_quizzes, 1)) * 100 ?>%;
            border-radius: 20px;
            box-shadow: 0 0 10px var(--orange);
            transition: width 0.5s ease;
        }

        /* Critical Test Section */
        .critical-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .critical-title i {
            font-size: 2rem;
            color: var(--orange);
            background: rgba(255,85,50,0.1);
            padding: 12px;
            border-radius: 18px;
        }

        .quiz-grid {
            display: grid;
            grid-template-columns: minmax(300px, 650px);
            gap: 20px;
            justify-content: center;
            margin-bottom: 40px;
        }

        .quiz-card {
             border-radius: 36px;
            padding: 36px 32px;
            border: 2px solid var(--orange);
            transition: all 0.3s ease;
            box-shadow: 0 25px 40px -15px rgba(255, 85, 50, 0.3);
            text-align: center;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .quiz-card::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -30%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,85,50,0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .quiz-card:hover {
            transform: translateY(-6px);
            border-color: var(--orange-dark);
            box-shadow: 0 30px 50px -15px var(--orange);
        }

        .quiz-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 85, 50, 0.15);
            border-radius: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .quiz-icon i {
            font-size: 3.8rem;
            color: var(--orange);
        }

        .quiz-title {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 16px;
            color: white;
        }

        .quiz-description {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 28px;
            line-height: 1.6;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .quiz-meta {
            display: flex;
            gap: 16px;
            margin-bottom: 28px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .quiz-meta span {
            background: rgba(255, 85, 50, 0.1);
            padding: 8px 20px;
            border-radius: 60px;
            font-size: 1rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,85,50,0.2);
        }

        .quiz-meta i {
            color: var(--orange);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 60px;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 24px;
        }

        .badge-completed {
            background: var(--success-bg);
            color: var(--success);
            border: 2px solid rgba(16, 185, 129, 0.3);
        }

        .badge-pending {
            background: rgba(255, 85, 50, 0.15);
            color: var(--orange);
            border: 2px solid rgba(255, 85, 50, 0.3);
        }

        .quiz-button {
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            padding: 18px 28px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.2s;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-start {
            background: var(--orange);
            color: white;
            box-shadow: 0 10px 20px -5px var(--orange);
        }

        .btn-start:hover {
            background: var(--orange-dark);
            transform: scale(1.02);
            box-shadow: 0 15px 25px -5px var(--orange-dark);
        }

        .btn-view {
            background: var(--success-bg);
            color: var(--success);
            border: 2px solid rgba(16, 185, 129, 0.3);
        }

        .btn-view:hover {
            background: rgba(16, 185, 129, 0.2);
        }

        /* Mobile Menu (slide-out) */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100%;
            background: white;
            z-index: 1000;
            padding: 80px 24px 24px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }

        .mobile-menu.show {
            transform: translateX(0);
        }

        .close-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background: var(--orange-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--orange);
            font-size: 1.2rem;
            cursor: pointer;
        }

        .mobile-menu-items {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mobile-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 18px;
            background: #f8fafc;
            border-radius: 16px;
            color: #1e293b;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .mobile-item i {
            width: 24px;
            color: var(--orange);
            font-size: 1.1rem;
        }

        .mobile-item.logout {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .mobile-item.logout i {
            color: #dc2626;
        }

        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .menu-overlay.show {
            display: block;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(11, 17, 32, 0.95);
            backdrop-filter: blur(10px);
            z-index: 2000;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .loading-content {
            text-align: center;
            max-width: 350px;
            padding: 30px;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 85, 50, 0.2);
            border-top-color: var(--orange);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 25px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .desktop-nav {
                display: none;
            }
            .mobile-menu-btn {
                display: flex;
            }
            .header-user {
                display: none;
            }
            .white-header {
                padding: 12px 20px;
            }
            .brand-text {
                font-size: 1.3rem;
            }
            .brand-text span {
                font-size: 0.8rem;
                padding: 2px 8px;
            }
            .logo-img {
                height: 40px;
            }
        }

        @media (max-width: 768px) {
            .welcome-banner {
                flex-direction: column;
                text-align: center;
                padding: 24px;
            }
            .welcome-stats {
                width: 100%;
                justify-content: space-around;
                padding: 14px 18px;
            }
            .progress-card {
                flex-direction: column;
                text-align: center;
                gap: 18px;
                padding: 22px;
            }
            .progress-info {
                width: 100%;
            }
            .quiz-card {
                padding: 28px 20px;
            }
            .quiz-title {
                font-size: 1.9rem;
            }
            .quiz-icon {
                width: 80px;
                height: 80px;
            }
            .quiz-icon i {
                font-size: 3rem;
            }
        }

        @media (max-width: 480px) {
            .white-header {
                padding: 10px 16px;
            }
            .logo-img {
                height: 35px;
            }
            .brand-text {
                font-size: 1.1rem;
            }
            .brand-text span {
                display: none;
            }
            .welcome-stats {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
                padding: 16px;
            }
            .stat-block {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .stat-value {
                font-size: 1.5rem;
            }
            .quiz-title {
                font-size: 1.7rem;
            }
            .quiz-description {
                font-size: 1rem;
            }
            .quiz-meta span {
                padding: 6px 14px;
                font-size: 0.9rem;
            }
            .status-badge {
                padding: 10px 20px;
                font-size: 1rem;
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
            <div class="box box-11"></div>
            <div class="box box-12"></div>
            <div class="box box-13"></div>
            <div class="box box-14"></div>
            <div class="box box-15"></div>
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
            <div class="particle particle-31"></div>
            <div class="particle particle-32"></div>
            <div class="particle particle-33"></div>
            <div class="particle particle-34"></div>
            <div class="particle particle-35"></div>
            <div class="particle particle-36"></div>
            <div class="particle particle-37"></div>
            <div class="particle particle-38"></div>
            <div class="particle particle-39"></div>
            <div class="particle particle-40"></div>
            <div class="particle particle-41"></div>
            <div class="particle particle-42"></div>
            <div class="particle particle-43"></div>
            <div class="particle particle-44"></div>
            <div class="particle particle-45"></div>
            <div class="particle particle-46"></div>
            <div class="particle particle-47"></div>
            <div class="particle particle-48"></div>
            <div class="particle particle-49"></div>
            <div class="particle particle-50"></div>
        </div>
        
        <div class="grid-layer"></div>
    </div>

    <!-- WHITE HEADER WITH LOGO -->
    <header class="white-header">
        <div class="logo-area">
            <img src="../assests/logo.png" alt="NetCoders" class="logo-img" onerror="this.src='https://via.placeholder.com/120x48?text=NETCODERS'">
            <div class="brand-text">
             </div>
        </div>

        <!-- Desktop Navigation -->
        <div class="desktop-nav">
             <a href="logout.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- Header User Info & Mobile Button -->
        <div class="header-actions">
            <div class="header-user">
                <div class="header-avatar">
                    <?= strtoupper(substr($first_name, 0, 1)) ?>
                </div>
                <div class="header-user-info">
                    <span class="header-user-name"><?= htmlspecialchars($first_name) ?></span>
                    <span class="header-user-badge"><i class="fas fa-bolt" style="font-size: 0.6rem;"></i> Critical test</span>
                </div>
            </div>
            <div class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="menu-overlay" id="menuOverlay" onclick="toggleMobileMenu()"></div>
    
    <!-- Mobile Menu Panel -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="close-menu" onclick="toggleMobileMenu()">
            <i class="fas fa-times"></i>
        </div>
        <div class="mobile-menu-items">
            <a href="index.php" class="mobile-item">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="profile.php" class="mobile-item">
                <i class="fas fa-user"></i> My Profile
            </a>
            <a href="results.php" class="mobile-item">
                <i class="fas fa-chart-bar"></i> My Results
            </a>
            <a href="settings.php" class="mobile-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="logout.php" class="mobile-item logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Welcome Banner with Student Name -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>👋 Welcome, <span><?= htmlspecialchars($first_name) ?>!</span></h1>
                <p>Your critical assessment is ready. Complete it to unlock your progress.</p>
            </div>
            <div class="welcome-stats">
                <div class="stat-block">
                    <div class="stat-value"><?= $completed_count ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-block">
                    <div class="stat-value"><?= $total_quizzes - $completed_count ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-block">
                    <div class="stat-value"><?= round(($completed_count / max($total_quizzes, 1)) * 100) ?>%</div>
                    <div class="stat-label">Progress</div>
                </div>
            </div>
        </div>

        <!-- Progress Card -->
        <div class="progress-card">
            <div class="progress-info">
                <div class="progress-label">
                    <i class="fas fa-bolt" style="color: var(--orange);"></i>
                    Critical Test Progress
                </div>
                <div class="progress-percentage">
                    <?= round(($completed_count / max($total_quizzes, 1)) * 100) ?>%
                </div>
                <div class="progress-detail">
                    <?= $completed_count ?> of <?= $total_quizzes ?> completed
                </div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill"></div>
            </div>
        </div>

        <!-- Critical Test Section - ONLY ONE TEST -->
         
        <div>
            <div class="critical-title">
                <i class="fas fa-exclamation-triangle"></i> Required   Assessment
            </div>

            <div class="quiz-grid">
                <?php if ($critical_quiz): 
                    $is_completed = in_array($critical_quiz['id'], $completed_quizzes);
                ?>
                    <div class="quiz-card">
                        <div class="quiz-icon">
                            <i class="fas fa-<?= $is_completed ? 'check-circle' : 'bolt' ?>"></i>
                        </div>
                        
                        <h3 class="quiz-title"><?= htmlspecialchars($critical_quiz['title']) ?></h3>
                        <p class="quiz-description"><?= htmlspecialchars($critical_quiz['description']) ?></p>
                        
                        <div class="quiz-meta">
                            <span><i class="far fa-clock"></i> 20 mins</span>
                            <span><i class="fas fa-question-circle"></i> 20 Questions</span>
                            <span><i class="fas fa-exclamation-circle"></i> Critical</span>
                        </div>

                        <?php if ($is_completed): ?>
                            <div class="status-badge badge-completed">
                                <i class="fas fa-check-circle"></i> Completed - View Results
                            </div>
                            <a href="view_result.php?quiz_id=<?= $critical_quiz['id'] ?>" class="quiz-button btn-view">
                                <i class="fas fa-chart-bar"></i> View Results
                            </a>
                        <?php else: ?>
                            <div class="status-badge badge-pending">
                                <i class="fas fa-hourglass-half"></i> Ready to Start
                            </div>
                            <a href="take_quiz.php?quiz_id=<?= $critical_quiz['id'] ?>" class="quiz-button btn-start" onclick="showLoading()">
                                <i class="fas fa-play"></i> Start Critical Test
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px; background: var(--dark-card); border-radius: 36px;">
                        <i class="fas fa-exclamation-circle" style="font-size: 4rem; color: var(--orange); margin-bottom: 20px;"></i>
                        <h3 style="font-size: 2rem; margin-bottom: 10px;">No Critical Test</h3>
                        <p style="color: var(--text-secondary);">Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="quizLoading">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h3 style="color: var(--orange); margin-bottom: 8px;">Starting Critical Test</h3>
            <p style="color: var(--text-secondary);">Get ready, <?= htmlspecialchars($first_name) ?>!</p>
            <div style="width: 100%; height: 6px; background: var(--border-color); border-radius: 10px; margin-top: 20px;">
                <div class="loading-progress-bar" id="quizProgress" style="height: 100%; width: 0; background: var(--orange); border-radius: 10px;"></div>
            </div>
        </div>
    </div>

    <script>
        // Toggle mobile menu
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('menuOverlay');
            menu.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Close mobile menu when clicking outside (overlay click)
        document.getElementById('menuOverlay').addEventListener('click', function() {
            document.getElementById('mobileMenu').classList.remove('show');
            this.classList.remove('show');
        });

        // Show loading when starting quiz
        function showLoading() {
            const overlay = document.getElementById('quizLoading');
            const progressBar = document.getElementById('quizProgress');
            
            overlay.style.display = 'flex';
            
            let width = 0;
            const interval = setInterval(() => {
                if (width >= 100) {
                    clearInterval(interval);
                } else {
                    width += 5;
                    progressBar.style.width = width + '%';
                }
            }, 50);
        }

        // Hide loading when navigating back
        window.addEventListener('pageshow', function() {
            document.getElementById('quizLoading').style.display = 'none';
        });
    </script>
</body>
</html>