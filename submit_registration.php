<?php
session_start();
include('db.php'); // Include database connection

// Check if the session is already active
if (isset($_SESSION['student_id'])) {
    // If session exists, redirect to the student index page
    $baseUrl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'];
    header("Location: ". $baseUrl."/cstm_quiz/student/index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $college = $_POST['college'];
    $course = $_POST['course'];
    $semester = $_POST['semester'];
    $branch = $_POST['branch'];
    $whatsapp = $_POST['whatsapp'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $session = $_POST['session'];

    // Prepare the SQL query to insert data into the students table
    $insert_sql = "INSERT INTO students (name, college, course, semester, branch, contact, email, whatsapp, session) 
                   VALUES ('$name', '$college', '$course', '$semester', '$branch', '$contact', '$email', '$whatsapp', '$session')";

    // Execute the query
    if (mysqli_query($conn, $insert_sql)) {
        // Registration successful, store student ID in session
        $_SESSION['student_id'] = mysqli_insert_id($conn); // Save student ID in session
        // Redirect to the student index page
        header("Location: student/index.php");
        exit();
    } else {
        // Registration failed, display error message
        echo "Error: " . mysqli_error($conn);
    }
}
?>

