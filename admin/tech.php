<?php
session_start();

// Database connection
$servername = "localhost";
$username = "campusedge_quiz";
$password = "MLOno(DK?WKa!+pR";
$dbname = "campusedge_quiz";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize session variables
if (!isset($_SESSION['quiz_started'])) {
    $_SESSION['quiz_started'] = false;
    $_SESSION['quiz_id'] = null;
    $_SESSION['current_question_index'] = 0;
    $_SESSION['questions'] = [];
}

// Display available quizzes (cards)
$quizzes = [
    ['id' => 'P', 'name' => 'PHP Quiz'],
    ['id' => 'A', 'name' => 'Android Quiz'],
    ['id' => 'Python', 'name' => 'Python Quiz'],
    ['id' => 'C&C++_Placement_Test', 'name' => 'C & C++ Quiz'],
    ['id' => 'Java_Quiz', 'name' => 'Java Quiz'],
    ['id' => 'Networking', 'name' => 'Networking Quiz']
];

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Application</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .quiz-container {
            max-width: 800px;
            margin: 50px auto;
        }
        .quiz-card {
            margin: 15px 0;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>';

if (!$_SESSION['quiz_started']) {
    // Show quiz options first (quiz cards)
    echo "<div class='quiz-container'>";
    echo "<h1 class='text-center'>Available Quizzes</h1>";
    echo "<div class='row'>";

    foreach ($quizzes as $quiz) {
        echo "<div class='col-md-4'>";
        echo "<div class='card quiz-card'>";
        echo "<div class='card-body text-center'>";
        echo "<h5 class='card-title'>{$quiz['name']}</h5>";
        echo "<form method='POST' action=''>";
        echo "<input type='hidden' name='quiz_id' value='{$quiz['id']}'>";
        echo "<button type='submit' name='start_quiz' class='btn btn-primary'>Start Test</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    echo "</div>"; // Close row
    echo "</div>"; // Close quiz-container
} else {
    // If the quiz is started, fetch and display the questions for that quiz
    if (isset($_POST['start_quiz']) && !$_SESSION['quiz_started']) {
        $quiz_id = $_POST['quiz_id'];
        $_SESSION['quiz_id'] = $quiz_id;
        $_SESSION['quiz_started'] = true;
        $_SESSION['current_question_index'] = 0;

        // Fetch the quiz questions from the database
        $sql = "SELECT * FROM tech_questions WHERE quiz_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $quiz_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $_SESSION['questions'] = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $_SESSION['questions'][] = $row;
            }
        }
        // Reload the page to show the questions after starting the quiz
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    // Display the current question if quiz has started
    $current_question_index = $_SESSION['current_question_index'];
    $questions = $_SESSION['questions'];

    if (!empty($questions) && isset($questions[$current_question_index])) {
        $current_question = $questions[$current_question_index];

        echo "<div class='quiz-container'>";
        echo "<h1 class='text-center'>Question " . ($current_question_index + 1) . "</h1>";
        echo "<form method='POST' action=''>";

        echo "<div class='card'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>{$current_question['question']}</h5>";

        // Display options (A, B, C, D)
        foreach (['A', 'B', 'C', 'D'] as $option) {
            if (!empty($current_question["option_$option"])) {
                echo "<div class='form-check'>";
                echo "<input class='form-check-input' type='radio' name='selected_answer' value='$option' required>";
                echo "<label class='form-check-label'>{$current_question["option_$option"]}</label>";
                echo "</div>";
            }
        }

        echo "</div>";
        echo "</div>";

        // Navigation buttons (Next/Previous)
        echo "<div class='d-flex justify-content-between'>";
        if ($current_question_index > 0) {
            echo "<button type='submit' name='previous_question' class='btn btn-secondary'>Previous</button>";
        }
        if ($current_question_index < count($questions) - 1) {
            echo "<button type='submit' name='next_question' class='btn btn-primary'>Next</button>";
        } else {
            echo "<button type='submit' name='submit_quiz' class='btn btn-success'>Submit Quiz</button>";
        }
        echo "</div>";

        echo "</form>";
        echo "</div>"; // Close quiz-container
    } else {
        echo "<div class='quiz-container'>";
        echo "<h3 class='text-center'>No questions found for this quiz.</h3>";
        echo "</div>";
    }
}

echo '</body>
</html>';

$conn->close();
?>
