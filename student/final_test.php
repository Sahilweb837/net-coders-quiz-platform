<?php
include('../db.php');
session_start();

// Check if the student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch only the "Psychometric Test" that the student hasn't attempted
$sql = "
    SELECT q.id, q.title, q.description, 
           CASE WHEN qr.id IS NOT NULL THEN 1 ELSE 0 END AS attempted
    FROM quizzes q
    LEFT JOIN quiz_responses qr ON q.id = qr.quiz_id AND qr.student_id = ?
    WHERE q.status = 'published' AND q.title = 'Psychometric Test'
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
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
        body {
            font-family: "Montserrat", sans-serif;
             color: #fff;
             background-image: url(Quizizz-test-1.png);
     background-repeat: no-repeat;
      background-size: cover;
             display: flex;
         }

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

        h1 {
            margin: 120px 0 20px;
            font-size: 36px;
            font-weight: bold;
            color: #fff;
            text-align: center;
        }

        .test-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            max-width: 1200px;
            padding: 20px;
        }

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

        .hidden {
            display: none;
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

    <h1>Select a Test</h1>

    <div class="test-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="test-card <?= $row['attempted'] ? 'hidden' : '' ?>">
                    <img src="https://img.icons8.com/ios-filled/100/000000/brain.png" alt="Quiz Icon">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <a href="final_quiz.php?quiz_id=<?= $row['id'] ?>">Start Test</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No Psychometric test available at the moment.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
