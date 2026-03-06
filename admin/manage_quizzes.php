<?php
session_start();
include('../db.php');

// Handle Publish/Unpublish action
if (isset($_GET['publish_id'])) {
    $quiz_id = $_GET['publish_id'];
    $sql = "SELECT status FROM quizzes WHERE id = $quiz_id";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        // Toggle status
        $new_status = ($row['status'] == 'published') ? 'unpublished' : 'published';
        $update_sql = "UPDATE quizzes SET status = '$new_status' WHERE id = $quiz_id";
        $conn->query($update_sql);
        header("Location: manage_tests.php?status=success");
        exit();
    } else {
        echo "Quiz not found.";
        exit();
    }
}

// Handle quiz creation for Psychometric Test
if (isset($_POST['create_quiz'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = 'unpublished'; // Default status

    // Insert the new quiz into the database
    $insert_sql = "INSERT INTO quizzes (title, description, status) VALUES ('$title', '$description', '$status')";
    if ($conn->query($insert_sql)) {
        $new_quiz_id = $conn->insert_id;
        header("Location: manage_tests.php?status=created");
        exit();
    } else {
        echo "Error creating the quiz: " . $conn->error;
    }
}

// Handle quiz update for Psychometric Test
if (isset($_POST['update_quiz'])) {
    $quiz_id = $_POST['quiz_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Update the quiz in the database
    $update_sql = "UPDATE quizzes SET title = '$title', description = '$description' WHERE id = $quiz_id";
    if ($conn->query($update_sql)) {
        header("Location: manage_tests.php?status=updated");
        exit();
    } else {
        echo "Error updating the quiz: " . $conn->error;
    }
}

// Handle quiz deletion
if (isset($_POST['delete_quiz'])) {
    $quiz_id = $_POST['quiz_id'];
    $delete_sql = "DELETE FROM quizzes WHERE id = $quiz_id";
    if ($conn->query($delete_sql)) {
        header("Location: manage_tests.php?status=deleted");
        exit();
    } else {
        echo "Error deleting the quiz: " . $conn->error;
    }
}

// Get all quizzes
$sql = "SELECT * FROM quizzes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        .logo {
            width: 150px;
            margin-bottom: 20px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            border-radius: 12px;
            padding: 10px 20px;
            text-align: center;
            font-size: 16px;
            box-shadow: 0px 5px 15px rgba(0, 123, 255, 0.2);
        }
        .status-published {
            color: #28a745;
        }
        .status-unpublished {
            color: #dc3545;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .table {
            border-radius: 10px;
            background-color: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            color:black;
        }
    </style>
</head>
<body>

<header class="text-center">
    <img src="../assests/register_form_latest.png" alt="Solitaire Infosystems Logo" class="logo">
</header>

<div class="container">
    <!-- Success Message -->
    <?php if (isset($_GET['status'])) { ?>
        <div class="alert alert-success">
            <strong>Success!</strong> The action has been completed successfully.
        </div>
    <?php } ?>

    <!-- Quiz Data Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['title'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td>
                            <?php if ($row['status'] == 'published') { ?>
                                <span class="status-published">Published</span>
                            <?php } else { ?>
                                <span class="status-unpublished">Unpublished</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editQuizModal" onclick="editQuiz(<?= $row['id'] ?>, '<?= $row['title'] ?>', '<?= $row['description'] ?>')">Edit</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteQuizModal" onclick="deleteQuiz(<?= $row['id'] ?>)">Delete</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#publishUnpublishModal" onclick="setPublishUnpublish(<?= $row['id'] ?>, '<?= $row['status'] ?>')"><?= $row['status'] == 'published' ? 'Unpublish' : 'Publish' ?></a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Quiz Modal -->
<div class="modal fade" id="editQuizModal" tabindex="-1" aria-labelledby="editQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editQuizModalLabel">Edit Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="quiz_id" id="editQuizId">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="title" id="editTitle" placeholder="Title" required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="description" id="editDescription" placeholder="Description" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_quiz" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Quiz Modal -->
<div class="modal fade" id="deleteQuizModal" tabindex="-1" aria-labelledby="deleteQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteQuizModalLabel">Delete Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="quiz_id" id="deleteQuizId">
                    <p>Are you sure you want to delete this quiz?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="delete_quiz" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Publish/Unpublish Modal -->
<div class="modal fade" id="publishUnpublishModal" tabindex="-1" aria-labelledby="publishUnpublishModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="publishUnpublishModalLabel">Publish/Unpublish Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="publish_id" id="publishQuizId">
                    <p>Are you sure you want to <span id="publishAction"></span> this quiz?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
    // Function to populate Edit Modal
    function editQuiz(id, title, description) {
        document.getElementById('editQuizId').value = id;
        document.getElementById('editTitle').value = title;
        document.getElementById('editDescription').value = description;
    }

    // Function to populate Delete Modal
    function deleteQuiz(id) {
        document.getElementById('deleteQuizId').value = id;
    }

    // Function to populate Publish/Unpublish Modal
    function setPublishUnpublish(id, status) {
        document.getElementById('publishQuizId').value = id;
        document.getElementById('publishAction').textContent = (status == 'published') ? 'Unpublish' : 'Publish';
    }
</script>

</body>
</html>
  