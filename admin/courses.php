 <?php
session_start();

// ✅ LOCAL DATABASE CONNECTION (Fixed for XAMPP)
$servername = "localhost";
$username = "root";        // Changed from 'campusedge_quiz' to 'root' for local XAMPP
$password = "";            // Empty password for XAMPP default
$dbname = "net_coders";    // Using net_coders database

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create new_courses table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS new_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(255) NOT NULL,
    published TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_course (course_name)
)";

if (!$conn->query($createTable)) {
    die("Error creating table: " . $conn->error);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Create (Add new course)
    if ($action == 'add') {
        $course_name = $conn->real_escape_string(trim($_POST['course_name']));
        
        if (!empty($course_name)) {
            // Check if the course name already exists in the database
            $stmt = $conn->prepare("SELECT id FROM new_courses WHERE course_name = ?");
            $stmt->bind_param("s", $course_name);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Course name already exists.']);
            } else {
                // Insert new course
                $stmt = $conn->prepare("INSERT INTO new_courses (course_name, published) VALUES (?, 0)");
                $stmt->bind_param("s", $course_name);
                if ($stmt->execute()) {
                    $id = $conn->insert_id;
                    echo json_encode(['status' => 'success', 'message' => 'New course added successfully!', 'id' => $id]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to add course.']);
                }
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Course name cannot be empty.']);
        }
        exit;
    }

    // Update (Edit existing course)
    if ($action == 'edit') {
        $id = intval($_POST['id']);
        $course_name = $conn->real_escape_string(trim($_POST['course_name']));
        
        if (!empty($course_name)) {
            // Check if name exists for other records
            $stmt = $conn->prepare("SELECT id FROM new_courses WHERE course_name = ? AND id != ?");
            $stmt->bind_param("si", $course_name, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Course name already exists.']);
            } else {
                $stmt = $conn->prepare("UPDATE new_courses SET course_name = ? WHERE id = ?");
                $stmt->bind_param("si", $course_name, $id);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Course updated successfully!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update course.']);
                }
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Course name cannot be empty.']);
        }
        exit;
    }

    // Delete record
    if ($action == 'delete') {
        $id = intval($_POST['id']);

        // Check if the course is published
        $stmt = $conn->prepare("SELECT published FROM new_courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();

        if ($course && $course['published'] == 1) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete published course.']);
        } else {
            $stmt = $conn->prepare("DELETE FROM new_courses WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Course deleted successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete course.']);
            }
        }
        $stmt->close();
        exit;
    }

    // Toggle published status
    if ($action == 'toggle_published') {
        $id = intval($_POST['id']);
        $currentStatus = intval($_POST['current_status']);
        $newStatus = $currentStatus == 1 ? 0 : 1;

        $stmt = $conn->prepare("UPDATE new_courses SET published = ? WHERE id = ?");
        $stmt->bind_param("ii", $newStatus, $id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Course status updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update status.']);
        }
        $stmt->close();
        exit;
    }
}

// Fetch records for initial page load
$result = $conn->query("SELECT * FROM new_courses ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetCoders - Course Management</title>
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-weight: 600;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .btn-custom {
            margin-bottom: 20px;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .modal-header .close {
            color: white;
            opacity: 0.8;
        }
        .modal-header .close:hover {
            opacity: 1;
        }
        #message {
            display: none;
            margin-top: 20px;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            font-weight: 500;
            animation: slideIn 0.5s ease;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        #message.success {
            background-color: #28a745;
            color: white;
        }
        #message.error {
            background-color: #dc3545;
            color: white;
        }
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .table thead th {
            background: #667eea;
            color: white;
            font-weight: 600;
            border: none;
        }
        .btn-sm {
            margin: 2px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .btn-sm:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        @media (max-width: 768px) {
            .admin-container {
                padding: 20px;
            }
            .btn-custom {
                width: 100%;
            }
            .table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

<div class="admin-container">
    <h2><i class="fas fa-graduation-cap"></i> NetCoders - Course Management</h2>
    <div id="message" class="message"></div>
    
    <button class="btn btn-success btn-custom" data-toggle="modal" data-target="#addEditModal" onclick="openAddModal()">
        <i class="fas fa-plus-circle"></i> Add New Course
    </button>
    
    <table id="recordsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr data-id="<?php echo $row['id']; ?>">
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $row['published'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $row['published'] == 1 ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['course_name'], ENT_QUOTES); ?>')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteRecord(<?php echo $row['id']; ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="togglePublished(<?php echo $row['id']; ?>, <?php echo $row['published']; ?>)">
                                <i class="fas <?php echo $row['published'] == 1 ? 'fa-times-circle' : 'fa-check-circle'; ?>"></i>
                                <?php echo $row['published'] == 1 ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No courses found. Click "Add New Course" to get started!</td>
                </tr>
            <?php endif; ?>
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
                        <label for="course_name">Course Name</label>
                        <input type="text" id="course_name" class="form-control" placeholder="Enter course name" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Save Course</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Initialize DataTable
    $('#recordsTable').DataTable({
        pageLength: 10,
        order: [[0, 'desc']],
        language: {
            emptyTable: "No courses available"
        }
    });

    // Handle form submission
    $('#addEditForm').submit(function (e) {
        e.preventDefault();
        let action = $('#recordId').val() ? 'edit' : 'add';
        let courseName = $('#course_name').val().trim();
        
        if (!courseName) {
            showMessage('Course name cannot be empty', 'error');
            return;
        }

        $.post('', { 
            action: action, 
            id: $('#recordId').val(), 
            course_name: courseName 
        }, function (response) {
            let data = JSON.parse(response);
            showMessage(data.message, data.status);
            
            if (data.status === 'success') {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
            $('#addEditModal').modal('hide');
        }).fail(function(xhr, status, error) {
            showMessage('Something went wrong: ' + error, 'error');
        });
    });
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

function deleteRecord(id) {
    if (confirm('⚠️ Are you sure you want to delete this course?')) {
        $.post('', { action: 'delete', id: id }, function (response) {
            let data = JSON.parse(response);
            showMessage(data.message, data.status);
            if (data.status === 'success') {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }).fail(() => {
            showMessage('Something went wrong!', 'error');
        });
    }
}

function togglePublished(id, currentStatus) {
    let action = currentStatus == 1 ? 'deactivate' : 'activate';
    if (confirm(`Are you sure you want to ${action} this course?`)) {
        $.post('', { 
            action: 'toggle_published', 
            id: id, 
            current_status: currentStatus 
        }, function (response) {
            let data = JSON.parse(response);
            showMessage(data.message, data.status);
            if (data.status === 'success') {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }).fail(() => {
            showMessage('Something went wrong!', 'error');
        });
    }
}

function showMessage(message, type) {
    let msgBox = $('#message');
    msgBox.removeClass('success error').addClass(type).text(message).show();
    setTimeout(() => {
        msgBox.fadeOut();
    }, 3000);
}
</script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php 
if (isset($conn)) {
    $conn->close(); 
}
?>