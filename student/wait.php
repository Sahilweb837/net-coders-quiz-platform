<?php
session_start();
include('../db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$query = "SELECT selected, test_completed, is_published FROM Students WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing the SQL query: " . $conn->error);
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($is_selected, $test_completed, $is_published);
$stmt->fetch();
$stmt->close();

if ($is_published) {
    if ($is_selected) {
        $message = $test_completed 
            ? "You have successfully completed the critical and aptitude tests. The next test is loading." 
            : "You have been selected for the final test. Click the button below to start.";
        $show_button = !$test_completed;
        $test_status = $test_completed ? 'completed' : 'loading';
    } else {
        $message = " ";
        $show_button = false;
        $test_status = 'not-selected';
    }
} else {
    $message = "Waiting for the test to be published...";
    $show_button = false;
    $test_status = 'not-published';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Test Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            margin: 0;
        }
        #loader {
            display: flex;
            justify-content: center;
            align-items: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }
        .loader-circle {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff; /* Blue color */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .status-icon {
            font-size: 2rem;
        }
        .text-success {
            color: #28a745 !important;
        }
        .text-danger {
            color: #dc3545 !important;
        }
        #content {
            display: none;
            text-align: center;
        }
        .btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1 class="text-success mt-2">Critical and Aptitude Tests Completed</h1>

<!-- Loader -->
<div id="loader">
    <div class="loader-circle"></div>
</div>

<!-- Main Content -->
<div id="content" class="container text-center">
    <h1 class="mb-3">Final Test Status</h1>
    <p class="mt-3"><?php echo $message; ?></p>

    <!-- Display Status -->
    <div class="my-4">
        <?php if ($test_status === 'completed'): ?>
            <span class="status-icon text-success">&#10004;</span> <!-- Green Check -->
        <?php elseif ($test_status === 'loading'): ?>
            <div class="loader-circle"></div>
            <p class="mt-2">Preparing your next test...</p>
        <?php elseif ($test_status === 'not-selected'): ?>
            <span class="status-icon text-danger">&#10008;</span> <!-- Red Cross -->
            <p class="text-danger">You are not selected for the final test.</p>
        <?php elseif ($test_status === 'not-published'): ?>
            <p class="text-info">Waiting for the test to be published...
             <center>
                
             </center>

            </p>
        <?php endif; ?>
    </div>

    <!-- Show Start Test Button -->
    <?php if ($show_button): ?>
        <form action="final.php" method="POST">
            <button type="submit" class="btn btn-primary">Start Final Test</button>
        </form>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const loader = document.getElementById('loader');
        const content = document.getElementById('content');

        // Simulate loading time for content display
        setTimeout(() => {
            loader.style.display = 'none'; // Hide loader
            content.style.display = 'block'; // Show the main content
        }, 2000); // Adjust time as needed
    });
     
</script>

</body>
</html>
 