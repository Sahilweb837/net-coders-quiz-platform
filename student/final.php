<?php
include('../db.php');
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch both "Psychometric Test" and "Technical Test" that the student hasn't attempted
$sql = "
    SELECT q.id, q.title, q.description, 
           CASE WHEN qr.id IS NOT NULL THEN 1 ELSE 0 END AS attempted,
           q.status AS quiz_status
    FROM quizzes q
    LEFT JOIN quiz_responses qr ON q.id = qr.quiz_id AND qr.student_id = ? 
    WHERE q.status = 'published' AND (q.title = 'Psychometric Test' OR q.title = 'Technical Test')
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the Psychometric Test has been completed
$psychometric_attempted = false;
$sql_check_psychometric = "SELECT id FROM quiz_responses WHERE student_id = ? AND quiz_id IN (SELECT id FROM quizzes WHERE title = 'Psychometric Test')";
$stmt_psychometric = $conn->prepare($sql_check_psychometric);
$stmt_psychometric->bind_param("i", $student_id);
$stmt_psychometric->execute();
$psychometric_result = $stmt_psychometric->get_result();
if ($psychometric_result->num_rows > 0) {
    $psychometric_attempted = true;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Quizzes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Global styles */
        body {
            font-family: "Montserrat", sans-serif;
            color: #fff;
            background-image: url(https://png.pngtree.com/thumb_back/fh260/background/20201206/pngtree-abstract-dark-blue-gaming-background-design-image_505148.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        /* Navbar Styles */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #1a237e;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        nav img {
            height: 50px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
        }

        /* Page Heading */
        h1 {
            margin: 120px 0 20px;
            font-size: 36px;
            font-weight: bold;
            color: #fff;
            text-align: center;
        }

        /* Test Container (Grid for responsive layout) */
        .test-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            max-width: 1200px;
            padding: 20px;
        }

        /* Individual Test Cards */
        .test-card {
            background: #fff;
            color: #333;
            width: 260px;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .test-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.3);
        }

        .test-card img {
            width: 60px;
            height: 60px;
            margin-bottom: 15px;
        }

        .test-card h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1a237e;
        }

        .test-card p {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .test-card a {
            display: inline-block;
            padding: 8px 12px;
            background-color: #1a237e;
            color: #fff;
            font-weight: 600;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .test-card a:hover {
            background-color: #0d47a1;
        }

        /* Lock Styling */
        .locked {
            background-color: #ccc;
            color: #666;
            cursor: not-allowed;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        .locked:hover {
            background-color: #aaa;
        }

        .test-card.locked {
            opacity: 0.5;
            pointer-events: none;
        }

        .test-card.locked img {
            filter: grayscale(100%);
        }
    </style>
</head>

<body>
    <nav>
        <img src="https://solitaireinfosystems.com/wp-content/uploads/2023/07/slinfi-logo.png" alt="Logo">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    
    <center>
        <h1 class="centered-heading">Select a Test</h1>
    </center>
    
    <div class="test-container">
        <!-- Loop through all the tests (Psychometric and Technical) -->
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    $is_locked = ($row['quiz_status'] == 'locked') && !$row['attempted']; 
                    $is_attempted = ($row['attempted'] == 1);
                ?>
                
                <div class="test-card <?= $is_locked ? 'locked' : '' ?>">
                    <img src="https://img.icons8.com/ios-filled/100/000000/brain.png" alt="Quiz Icon">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <?php if ($row['title'] == 'Technical Test'): ?>
                        <?php if ($psychometric_attempted): ?>
                            <a href="tech.php?quiz_id=<?= $row['id'] ?>">Start Technical Test</a>
                        <?php else: ?>
                            <a href="#" class="locked">Unlock after Psychometric Test</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($is_locked): ?>
                            <a href="#" class="locked">Test Locked</a>
                        <?php else: ?>
                            <a href="final_quiz.php?quiz_id=<?= $row['id'] ?>">Start Test</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

        <!-- New Technical Test card (locked initially) -->
        <div class="test-card <?= $psychometric_attempted ? '' : 'locked' ?>">
            <img src="https://img.icons8.com/ios-filled/100/000000/brain.png" alt="Technical Test Icon">
            <h3>Technical Test</h3>
            <p>Test your technical knowledge and skills here.</p>
            <?php if ($psychometric_attempted): ?>
                <a href="tech.php">Start Technical Test</a>
            <?php else: ?>
                <a href="#" class="locked">Unlock after Psychometric Test</a>
            <?php endif; ?>
        </div>

    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
