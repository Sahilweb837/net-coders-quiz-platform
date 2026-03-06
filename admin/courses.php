<?php
session_start();
 $servername = "localhost";
$username = "campusedge_quiz";
$password = "MLOno(DK?WKa!+pR";
$dbname = "campusedge_quiz";


// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Create (Add new course)
    if ($action == 'add') {
        $course_name = $conn->real_escape_string($_POST['course_name']);
        
        if (!empty($course_name)) {
            // Check if the course name already exists in the database
            $result = $conn->query("SELECT id FROM new_courses WHERE course_name = '$course_name'");
            
            if ($result->num_rows > 0) {
                // If the course name exists, return an error message
                echo json_encode(['status' => 'error', 'message' => 'Course name already exists.']);
            } else {
                // If the course name does not exist, proceed to insert
                $conn->query("INSERT INTO new_courses (course_name) VALUES ('$course_name')");
                $id = $conn->insert_id; // Get the last inserted ID
                echo json_encode(['status' => 'success', 'message' => 'New course added successfully!', 'id' => $id]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Course name cannot be empty.']);
        }
        exit;
    }

    // Update (Edit existing course)
    if ($action == 'edit') {
        $id = intval($_POST['id']);
        $course_name = $conn->real_escape_string($_POST['course_name']);
        if (!empty($course_name)) {
            $conn->query("UPDATE new_courses SET course_name='$course_name' WHERE id=$id");
            echo json_encode(['status' => 'success', 'message' => 'Course updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Course name cannot be empty.']);
        }
        exit;
    }

    // Delete record
    if ($action == 'delete') {
        $id = intval($_POST['id']);

        // Check if the course is published
        $result = $conn->query("SELECT published FROM new_courses WHERE id=$id");
        $course = $result->fetch_assoc();

        if ($course && $course['published'] == 1) {
            // If the course is published, do not allow deletion
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete published course.']);
        } else {
            // If the course is not published, proceed with deletion
            $conn->query("DELETE FROM new_courses WHERE id=$id");
            echo json_encode(['status' => 'success', 'message' => 'Course deleted successfully!']);
        }
        exit;
    }

    // Toggle published status
    if ($action == 'toggle_published') {
        $id = intval($_POST['id']);
        $currentStatus = intval($_POST['current_status']);

        // Toggle the published status (0 to 1 or 1 to 0)
        $newStatus = $currentStatus == 1 ? 0 : 1;

        // Update the course published status
        $conn->query("UPDATE new_courses SET published = $newStatus WHERE id = $id");

        echo json_encode(['status' => 'success', 'message' => 'Course status updated successfully!']);
        exit;
    }
}

// Fetch records for initial page load
$result = $conn->query("SELECT * FROM new_courses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Course Management</title>
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }
        .btn-custom {
            margin-bottom: 20px;
        }
        .modal-content {
            border-radius: 8px;
        }
        #message {
            display: none;
            margin-top: 20px;
            padding: 10px;
            text-align: center;
        }
        #message.success {
            background-color: #28a745;
            color: white;
        }
        #message.error {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <h2>Admin Dashboard - Manage Courses</h2>
    <div id="message" class="message"></div>
    <button class="btn btn-success btn-custom" data-toggle="modal" data-target="#addEditModal" onclick="openAddModal()">+ Add New Course</button>
    <table id="recordsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course</th>
                <th>Published</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr data-id="<?php echo $row['id']; ?>">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                    <td><?php echo $row['published'] == 1 ? 'Yes' : 'No'; ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo $row['course_name']; ?>')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteRecord(<?php echo $row['id']; ?>)">Delete</button>
                        <button class="btn btn-warning btn-sm" onclick="togglePublished(<?php echo $row['id']; ?>, <?php echo $row['published']; ?>)">
                            <?php echo $row['published'] == 1 ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal for Add/Edit Course -->
<div class="modal fade" id="addEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Course</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addEditForm">
                    <input type="hidden" id="recordId">
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" id="course_name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#recordsTable').DataTable();
});

function openAddModal() {
    $('#modalTitle').text('Add New Course');
    $('#recordId').val('');
    $('#course_name').val('');
    $('#addEditModal').modal('show');
}

function openEditModal(id, course_name) {
    $('#modalTitle').text('Edit Course');
    $('#recordId').val(id);
    $('#course_name').val(course_name);
    $('#addEditModal').modal('show');
}

$('#addEditForm').submit(function (e) {
    e.preventDefault();
    let action = $('#recordId').val() ? 'edit' : 'add';
    $.post('', { action: action, id: $('#recordId').val(), course_name: $('#course_name').val() }, function (response) {
        let data = JSON.parse(response);
        if (data.status === 'success') {
            if (action === 'add') {
                let newRow = `<tr data-id="${data.id}">
                    <td>${data.id}</td>
                    <td>${$('#course_name').val()}</td>
                    <td>No</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="openEditModal(${data.id}, '${$('#course_name').val()}')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteRecord(${data.id})">Delete</button>
                        <button class="btn btn-warning btn-sm" onclick="togglePublished(${data.id}, 0)">Activate</button>
                    </td>
                </tr>`;
                $('#recordsTable tbody').append(newRow);
            } else {
                let row = $('tr[data-id="' + $('#recordId').val() + '"]');
                row.find('td').eq(1).text($('#course_name').val());
            }
            $('#message').removeClass('error').addClass('success').text(data.message).show();
            setTimeout(() => { $('#message').fadeOut(); }, 2000);
            $('#addEditModal').modal('hide');
        }
    }).fail(() => {
        $('#message').removeClass('success').addClass('error').text('Something went wrong!').show();
        setTimeout(() => $('#message').fadeOut(), 2000);
    });
});

function deleteRecord(id) {
    if (confirm('Are you sure?')) {
        $.post('', { action: 'delete', id: id }, function (response) {
            let data = JSON.parse(response);
            if (data.status === 'success') {
                $('tr[data-id="' + id + '"]').remove();
                $('#message').removeClass('error').addClass('success').text(data.message).show();
                setTimeout(() => { $('#message').fadeOut(); }, 2000);
            } else {
                $('#message').removeClass('success').addClass('error').text(data.message).show();
                setTimeout(() => $('#message').fadeOut(), 2000);
            }
        }).fail(() => {
            $('#message').removeClass('success').addClass('error').text('Something went wrong!').show();
            setTimeout(() => $('#message').fadeOut(), 2000);
        });
    }
    // 
}

function togglePublished(id, currentStatus) {
    $.post('', { action: 'toggle_published', id: id, current_status: currentStatus }, function (response) {
        let data = JSON.parse(response);
        if (data.status === 'success') {
            location.reload();  // Refresh the page to see the changes
        } else {
            alert(data.message);  // Show error message
        }
    });
}
</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
 
  