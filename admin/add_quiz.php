<?php
// Start the session
session_start();

// Include your database connection file
include('../db.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data safely (using trim to remove extra spaces and htmlspecialchars for security)
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $group_id = (int)$_POST['group_id']; // Cast to integer to ensure it's a number

    // Validate inputs
    if (empty($title) || empty($description) || empty($group_id)) {
        die('All fields are required.');
    }

    // SQL Query for inserting the quiz into the database
    $sql = "INSERT INTO quizzes (title, description, group_id) VALUES (?, ?, ?)";

    // Prepare the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the SQL query
        $stmt->bind_param("ssi", $title, $description, $group_id);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to add_question_to_quiz.php after successful insertion
            header("Location: add_question_to_quiz.php?quiz_id=" . $stmt->insert_id); // Pass quiz_id to the next page
            exit(); // Stop further execution
        } else {
            // Handle failure
            echo "Error: " . $stmt->error;
        }

         $stmt->close();
    } else {
        // Handle preparation error
        die('MySQL prepare error: ' . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar (Optional, if you have it) -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #003366;">
    <div class="container">
        <a class="navbar-brand" href="#">Solitaire Infosystems</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Form for Adding Quiz -->
<div class="container mt-5">
    <h2 class="text-center">Add New Quiz</h2>

    <form action="add_quiz.php" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Quiz Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Quiz Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="group_id" class="form-label">Group ID</label>
            <input type="number" class="form-control" id="group_id" name="group_id" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Quiz</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 add that quiz that have multiple quiz  like that pyhton java  networking and seo
  after that  they add th question in the difreent difrent quizes add that code  