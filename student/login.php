<?php
session_start();

// Include the database connection
include('db.php'); // Assuming your database connection is in a file named 'db.php'

// Fetch college names from the database
$colleges = [];
$college_query = "SELECT id, college_name FROM updated_college WHERE published = 1";
$college_result = $conn->query($college_query);
if ($college_result->num_rows > 0) {
    while ($row = $college_result->fetch_assoc()) {
        $colleges[] = $row;
    }
}

// Fetch active sessions from the database
$active_sessions = [];
$session_query = "SELECT id, session_name FROM current_session WHERE status = 1"; // Get only active sessions
$session_result = $conn->query($session_query);

if ($session_result->num_rows > 0) {
    while ($row = $session_result->fetch_assoc()) {
        $active_sessions[] = $row;
    }
}

// Fetch course names from the database
$courses = [];
$course_query = "SELECT id, course_name FROM new_courses WHERE published = 1";
$course_result = $conn->query($course_query);
if ($course_result->num_rows > 0) {
    while ($row = $course_result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $college = $_POST['college'];
    $course = $_POST['course'];
    $semester = $_POST['semester'];
    $branch = $_POST['branch']; // Get the selected branch
    $whatsapp = $_POST['whatsapp'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $session = $_POST['session'];

    // Check if the mobile number or email already exists in the database
    $check_sql = "SELECT * FROM students WHERE contact = ? OR email = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ss", $contact, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // If a record with the same mobile number or email exists, show an error
        $error_message = "The mobile number or email is already registered. Please use a different one.";
    } else {
        // Prepare SQL query to insert data into the 'students' table
        $insert_sql = "INSERT INTO students (name, college, course, semester, branch, contact, email, whatsapp, session) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);

        // Bind parameters to the query
        $stmt->bind_param("sssssssss", $name, $college, $course, $semester, $branch, $contact, $email, $whatsapp, $session);

        // Execute the query
        if ($stmt->execute()) {
            // If the query is successful, redirect to the student index page
            $_SESSION['student_id'] = $stmt->insert_id; // Store student ID in session
            header("Location: student/index.php"); // Redirect to the index page
            exit();
        } else {
            // If there is an error, show the error message
            echo "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solitare Tests</title>
    <link href="https://solitaireinfosystems.com/wp-content/themes/slinfy/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://solitaireinfosystems.com/wp-content/themes/slinfy/assets/css/style.css" rel="stylesheet">
    <link href="https://solitaireinfosystems.com/wp-content/themes/slinfy/assets/css/responsive.css" rel="stylesheet">
    <link rel='shortcut icon' href='https://solitaireinfosystems.com/wp-content/uploads/2018/02/fav-01-16.png'/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?_t=<?php echo time(); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #e9f5ff, #cce7ff);
            font-family: Arial, sans-serif;
        }
        .registration-container {
            margin: 20px auto;
            max-width: 1100px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 20px;
        }
        .registration-form input, .registration-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .registration-form input.error, .registration-form select.error {
            border-color: red;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: -8px;
            margin-bottom: 10px;
        }
        .registration-form button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .registration-form button:hover {
            background-color: #0056b3;
        }
        .logoenquery {
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <!-- Header with Logo -->
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                <img class="logoenquery" src="https://solitaireinfosystems.com/wp-content/uploads/2023/09/register_form_latest.png" alt="Logo">
            </div>
        </div>
        <!-- Content Section -->
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center">
                <div class="registration-content">
                    <!-- Left Section (Image) -->
                    <div class="registration-image">
                        <img class="imgforms" src="./assests/bannermain.jpg" alt="Logo">
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center">
                <div class="registration-form">
                    <form id="quizForm" action="index.php" method="POST" onsubmit="return validateForm()">
                        <div class="user-info">
                            <!-- Error Message Display -->
                            <?php if (isset($error_message)) { ?>
                                <div class="error-message"><?php echo $error_message; ?></div>
                            <?php } ?>

                            <!-- Name and College Fields -->
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <input type="text" id="name" name="name" placeholder="Name" required>
                                        <div class="error-message" id="name-error"></div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <select id="college" name="college" required>
                                            <option value="" disabled selected>Select Your College</option>
                                            <?php foreach ($colleges as $college) { ?>
                                                <option value="<?php echo htmlspecialchars($college['college_name']); ?>">
                                                    <?php echo htmlspecialchars($college['college_name']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <div class="error-message" id="college-error"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Email, Semester, Branch Fields -->
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <input type="email" id="email" name="email" placeholder="Email ID" required>
                                        <div class="error-message" id="email-error"></div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <select id="semester" name="semester" required>
                                            <option value="" disabled selected>Select a Semester</option>
                                            <option value="1st">1st Semester</option>
                                            <option value="2nd">2nd Semester</option>
                                            <option value="3rd">3rd Semester</option>
                                            <option value="4th">4th Semester</option>
                                            <option value="5th">5th Semester</option>
                                            <option value="6th">6th Semester</option>
                                            <option value="7th">7th Semester</option>
                                            <option value="8th">8th Semester</option>
                                        </select>
                                        <div class="error-message" id="semester-error"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Branch, Course, Contact, and WhatsApp Fields -->
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <select name="branch" id="branch" required>
                                            <option value="" disabled selected>Select a Branch</option>
                                            <?php 
                                            $branch_result = $conn->query("SELECT * FROM branches WHERE status=1");
                                            while ($branch_row = $branch_result->fetch_assoc()) { ?>
                                                <option value="<?php echo $branch_row['id']; ?>">
                                                    <?php echo $branch_row['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <div class="error-message" id="branch-error"></div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <input type="tel" id="contact" name="contact" placeholder="Phone No" required>
                                        <div class="error-message" id="contact-error"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <input type="tel" id="whatsapp" name="whatsapp" placeholder="WhatsApp No" required>
                                        <div class="error-message" id="whatsapp-error"></div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <input type="text" id="session" name="session" placeholder="Session" value="<?php echo isset($active_sessions[0]) ? htmlspecialchars($active_sessions[0]['session_name']) : ''; ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Selection -->
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <select id="course" name="course" required>
                                            <option value="" disabled selected>Select a Course</option>
                                            <?php foreach ($courses as $course) { ?>
                                                <option value="<?php echo htmlspecialchars($course['course_name']); ?>">
                                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <div class="error-message" id="course-error"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-center">
                                <button type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            let isValid = true;

            // Name validation: Only alphabets allowed
            const name = document.getElementById("name");
            const nameError = document.getElementById("name-error");
            const nameRegex = /^[a-zA-Z ]+$/;
            if (!nameRegex.test(name.value)) {
                nameError.textContent = "Name should contain only alphabets.";
                name.classList.add("error");
                isValid = false;
            } else {
                nameError.textContent = "";
                name.classList.remove("error");
            }

            // Mobile number validation: 10 digits only
            const contact = document.getElementById("contact");
            const contactError = document.getElementById("contact-error");
            const whatsapp = document.getElementById("whatsapp");
            const whatsappError = document.getElementById("whatsapp-error");
            const mobileRegex = /^[0-9]{10}$/;
            if (!mobileRegex.test(contact.value)) {
                contactError.textContent = "Mobile number must be exactly 10 digits.";
                contact.classList.add("error");
                isValid = false;
            } else {
                contactError.textContent = "";
                contact.classList.remove("error");
            }
            if (!mobileRegex.test(whatsapp.value)) {
                whatsappError.textContent = "WhatsApp number must be exactly 10 digits.";
                whatsapp.classList.add("error");
                isValid = false;
            } else {
                whatsappError.textContent = "";
                whatsapp.classList.remove("error");
            }

            // Email validation: Standard format
            const email = document.getElementById("email");
            const emailError = document.getElementById("email-error");
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email.value)) {
                emailError.textContent = "Please enter a valid email address.";
                email.classList.add("error");
                isValid = false;
            } else {
                emailError.textContent = "";
                email.classList.remove("error");
            }

            // Ensure the 'branch' field is selected
            const branch = document.getElementById("branch");
            const branchError = document.getElementById("branch-error");
            if (branch.value === "") {
                branchError.textContent = "Please select a branch.";
                branch.classList.add("error");
                isValid = false;
            } else {
                branchError.textContent = "";
                branch.classList.remove("error");
            }

            return isValid;
        }
    </script>
</body>
</html>
