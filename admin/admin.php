<?php
// Database connection
include('../db.php');

// Create functionality: Add new student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $course = $_POST['course'];

    $sql = "INSERT INTO students (course) VALUES ('$course')";
    if ($conn->query($sql) === TRUE) {
        $message = "Student added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Read functionality: Fetch all students
$sql = "SELECT * FROM students";
$result = $conn->query($sql);

// Update functionality: Edit a student's data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['id'];
    $course = $_POST['course'];

    $sql = "UPDATE students SET course='$course' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "Student updated successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Delete functionality: Delete a student
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM students WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header('Location: admin.php');
        exit;
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Manage Students</h2>

    <!-- Display success or error message -->
    <?php if (isset($message)) { ?>
        <div class="alert alert-info">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- Add new student form -->
    <h3>Add New Student</h3>
    <form method="POST" action="admin.php" id="addStudentForm">
        <input type="hidden" name="action" value="add">
        <div class="mb-3">
            <label for="course" class="form-label">Course</label>
            <input type="text" class="form-control" id="course" name="course" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Student</button>
    </form>

    <!-- Students Table -->
    <h3 class="mt-5">All Students</h3>
    <table class="table table-bordered" id="studentsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr id="student-<?php echo $row['id']; ?>">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['course']; ?></td>
                    <td>
                        <!-- Edit Button (opens modal) -->
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>

                        <!-- Delete Button -->
                        <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Student</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" id="editStudentForm<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <div class="mb-3">
                                        <label for="course" class="form-label">Course</label>
                                        <input type="text" class="form-control" id="course" name="course" value="<?php echo $row['course']; ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        // Handle Add Student Form submission via AJAX
        $('#addStudentForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'admin.php',
                data: $(this).serialize(),
                success: function(response) {
                    // Reload the page to show the new student added
                    location.reload();
                }
            });
        });

        // Handle Edit Student Form submission via AJAX
        $('[id^="editStudentForm"]').submit(function(e) {
            e.preventDefault();
            var formId = $(this).attr('id');
            var studentId = formId.replace('editStudentForm', '');

            $.ajax({
                type: 'POST',
                url: 'admin.php',
                data: $(this).serialize(),
                success: function(response) {
                    // Update the student row without reloading
                    var updatedCourse = $('input[name="course"]', '#' + formId).val();

                    // Update the row in the table dynamically
                    $('#student-' + studentId + ' td:nth-child(2)').text(updatedCourse);

                    // Close the modal
                    $('#editModal' + studentId).modal('hide');
                }
            });
        });
    });
</script>

</body>
</html>
 