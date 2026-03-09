 <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "net_coders";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ✅ Check if is_published column exists in quizzes table
$columnCheck = $conn->query("SHOW COLUMNS FROM quizzes LIKE 'is_published'");
$hasPublishedColumn = $columnCheck->num_rows > 0;

// If column doesn't exist, add it
if (!$hasPublishedColumn) {
    $conn->query("ALTER TABLE quizzes ADD COLUMN is_published TINYINT(1) DEFAULT 0");
    $hasPublishedColumn = true;
}

// ✅ Fetch unique colleges for dropdown
$collegeResult = $conn->query("SELECT DISTINCT college FROM students ORDER BY college");

// ✅ Fetch quizzes for dropdown
$quizResult = $conn->query("SELECT DISTINCT title FROM quizzes ORDER BY title");

// ✅ Fetch quiz results + student info
$sql = "
    SELECT 
        s.id AS student_id,
        s.name AS student_name,
        s.email AS student_email,
        s.college,
        s.semester,
        q.id AS quiz_id,
        q.title AS quiz_title,
        COALESCE(q.is_published, 0) AS is_published,
        r.result AS score
    FROM quiz_results r
    JOIN students s ON r.student_id = s.id
    JOIN quizzes q ON r.quiz_id = q.id
    ORDER BY s.id DESC, q.id DESC
";
$result = $conn->query($sql);

if (!$result) {
    die("❌ Query failed: " . $conn->error);
}

// ✅ Handle AJAX publish/unpublish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $ids = $_POST['ids'] ?? [];
    $action = $_POST['action'];
    $response = ["success" => false, "message" => ""];

    if (!empty($ids)) {
        $ids_str = implode(",", array_map("intval", $ids));
        $status = ($action === "publish") ? 1 : 0;
        
        // First get all quiz IDs for these students
        $quizIdsQuery = "SELECT DISTINCT r.quiz_id 
                         FROM quiz_results r 
                         WHERE r.student_id IN ($ids_str)";
        $quizIdsResult = $conn->query($quizIdsQuery);
        
        if ($quizIdsResult && $quizIdsResult->num_rows > 0) {
            $quizIds = [];
            while ($row = $quizIdsResult->fetch_assoc()) {
                $quizIds[] = $row['quiz_id'];
            }
            $quizIds_str = implode(",", $quizIds);
            
            // Update the quizzes table
            $update = "UPDATE quizzes SET is_published = $status WHERE id IN ($quizIds_str)";
            if ($conn->query($update)) {
                $response = ["success" => true, "message" => "Status updated successfully"];
            } else {
                $response = ["success" => false, "message" => "Failed to update: " . $conn->error];
            }
        } else {
            $response = ["success" => false, "message" => "No quizzes found for selected students"];
        }
    } else {
        $response = ["success" => false, "message" => "No students selected"];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Quiz Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .badge-published {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge-unpublished {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .action-buttons {
            margin-bottom: 20px;
        }
        .filter-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light p-4">
<div class="container-fluid">
    <h2 class="mb-4">📊 Student Quiz Results</h2>

    <!-- ✅ Top Buttons -->
    <div class="action-buttons">
        <button type="button" id="btnPublish" class="btn btn-success me-2">
            <i class="bi bi-check-circle"></i> Publish Selected
        </button>
        <button type="button" id="btnUnpublish" class="btn btn-danger">
            <i class="bi bi-x-circle"></i> Unpublish Selected
        </button>
    </div>

    <!-- ✅ Filters -->
    <div class="filter-row row">
        <div class="col-md-3">
            <label class="form-label">College</label>
            <select id="filterCollege" class="form-select">
                <option value="">All Colleges</option>
                <?php 
                if ($collegeResult) {
                    while ($c = $collegeResult->fetch_assoc()): 
                ?>
                    <option value="<?= htmlspecialchars($c['college']) ?>"><?= htmlspecialchars($c['college']) ?></option>
                <?php 
                    endwhile;
                } 
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Semester</label>
            <input type="text" id="filterSemester" class="form-control" placeholder="Filter by Semester">
        </div>
        <div class="col-md-3">
            <label class="form-label">Quiz</label>
            <select id="filterTest" class="form-select">
                <option value="">All Quizzes</option>
                <?php 
                if ($quizResult) {
                    while ($q = $quizResult->fetch_assoc()): 
                ?>
                    <option value="<?= htmlspecialchars($q['title']) ?>"><?= htmlspecialchars($q['title']) ?></option>
                <?php 
                    endwhile;
                } 
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select id="filterStatus" class="form-select">
                <option value="">All Status</option>
                <option value="Published">Published</option>
                <option value="Unpublished">Unpublished</option>
            </select>
        </div>
    </div>

    <!-- ✅ Results Table -->
    <div class="card shadow">
        <div class="card-body">
            <table id="resultsTable" class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>College</th>
                        <th>Semester</th>
                        <th>Quiz</th>
                        <th>Score</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-student-id="<?= $row['student_id'] ?>" data-quiz-id="<?= $row['quiz_id'] ?>">
                            <td><input type="checkbox" class="studentCheckbox" value="<?= $row['student_id'] ?>"></td>
                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td><?= htmlspecialchars($row['student_email']) ?></td>
                            <td><?= htmlspecialchars($row['college']) ?></td>
                            <td><?= htmlspecialchars($row['semester']) ?></td>
                            <td><?= htmlspecialchars($row['quiz_title']) ?></td>
                            <td><?= htmlspecialchars($row['score']) ?></td>
                            <td class="status">
                                <?php if ($row['is_published'] == 1): ?>
                                    <span class="badge-published">Published</span>
                                <?php else: ?>
                                    <span class="badge-unpublished">Unpublished</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center">No results found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ✅ Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">✅ Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="successMessage">Action completed successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">❌ Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage">An error occurred!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#resultsTable').DataTable({
        pageLength: 25,
        order: [[1, 'desc']],
        columnDefs: [
            { orderable: false, targets: 0 }
        ]
    });

    // ✅ Custom filter for status
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var statusFilter = $('#filterStatus').val();
            if (statusFilter === '') return true;
            
            var status = data[8]; // Status column index
            if (statusFilter === 'Published' && status.includes('Published')) return true;
            if (statusFilter === 'Unpublished' && status.includes('Unpublished')) return true;
            
            return false;
        }
    );

    // ✅ Apply filters
    $('#filterCollege').on('change', function() {
        table.column(4).search(this.value).draw();
    });
    
    $('#filterSemester').on('keyup', function() {
        table.column(5).search(this.value).draw();
    });
    
    $('#filterTest').on('change', function() {
        table.column(6).search(this.value).draw();
    });
    
    $('#filterStatus').on('change', function() {
        table.draw();
    });

    // ✅ Select All Checkbox
    $('#selectAll').on('click', function() {
        $('.studentCheckbox').prop('checked', this.checked);
    });

    // ✅ Update checkboxes when table rows change (pagination)
    table.on('draw', function() {
        $('#selectAll').prop('checked', false);
    });

    // ✅ AJAX Action for Publish/Unpublish
    function updateStatus(action) {
        var ids = $('.studentCheckbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (ids.length === 0) {
            alert("⚠️ Please select at least one student.");
            return;
        }

        // Show loading state
        var btn = action === 'publish' ? $('#btnPublish') : $('#btnUnpublish');
        var originalText = btn.html();
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
        btn.prop('disabled', true);

        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: {
                action: action,
                ids: ids
            },
            dataType: 'json',
            success: function(response) {
                btn.html(originalText);
                btn.prop('disabled', false);
                
                if (response.success) {
                    $('#successMessage').text(response.message || 'Status updated successfully!');
                    $('#successModal').modal('show');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    $('#errorMessage').text(response.message || 'Failed to update status');
                    $('#errorModal').modal('show');
                }
            },
            error: function(xhr, status, error) {
                btn.html(originalText);
                btn.prop('disabled', false);
                $('#errorMessage').text('AJAX Error: ' + error);
                $('#errorModal').modal('show');
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }

    // ✅ Bind buttons
    $('#btnPublish').click(function() { 
        if (confirm('Are you sure you want to publish selected items?')) {
            updateStatus("publish"); 
        }
    });
    
    $('#btnUnpublish').click(function() { 
        if (confirm('Are you sure you want to unpublish selected items?')) {
            updateStatus("unpublish"); 
        }
    });

    // ✅ Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl/Cmd + A for select all
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 65) {
            e.preventDefault();
            $('#selectAll').click();
        }
        
        // Escape to clear selections
        if (e.keyCode === 27) {
            $('.studentCheckbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
        }
    });

    // ✅ Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</body>
</html>