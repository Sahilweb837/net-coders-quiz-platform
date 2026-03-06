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
        q.is_published,
        r.result AS score
    FROM quiz_results r
    JOIN students s ON r.student_id = s.id
    JOIN quizzes q ON r.quiz_id = q.id
    ORDER BY s.id DESC, q.id DESC
";
$result = $conn->query($sql);

// ✅ Handle AJAX publish/unpublish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $ids = $_POST['ids'] ?? [];
    $action = $_POST['action'];

    if (!empty($ids)) {
        $ids_str = implode(",", array_map("intval", $ids));
        $status = ($action === "publish") ? 1 : 0;
        $update = "UPDATE quizzes q 
                   JOIN quiz_results r ON q.id = r.quiz_id 
                   SET q.is_published = $status 
                   WHERE r.student_id IN ($ids_str)";
        $conn->query($update);
    }

    echo json_encode(["success" => true]);
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
</head>
<body class="bg-light p-4">
<div class="container">
    <h2 class="mb-4">📊 Student Quiz Results</h2>

    <!-- ✅ Top Buttons -->
    <div class="mb-3">
        <button type="button" id="btnPublish" class="btn btn-success">Publish Selected</button>
        <button type="button" id="btnUnpublish" class="btn btn-danger">Unpublish Selected</button>
    </div>

    <!-- ✅ Filters -->
    <div class="row mb-3">
        <div class="col-md-3">
            <select id="filterCollege" class="form-control">
                <option value="">All Colleges</option>
                <?php while ($c = $collegeResult->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($c['college']) ?>"><?= htmlspecialchars($c['college']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" id="filterSemester" class="form-control" placeholder="Filter by Semester">
        </div>
        <div class="col-md-3">
            <select id="filterTest" class="form-control">
                <option value="">All Quizzes</option>
                <?php while ($q = $quizResult->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($q['title']) ?>"><?= htmlspecialchars($q['title']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>

    <!-- ✅ Results Table -->
    <div class="card shadow p-3">
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
                    <tr>
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
                                <span class="badge bg-success">Published</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Unpublished</span>
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

<!-- ✅ Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">✅ Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Action completed successfully!
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
    var table = $('#resultsTable').DataTable();

    // ✅ Filters
    $('#filterCollege').on('change', function() {
        table.column(4).search(this.value).draw();
    });
    $('#filterSemester').on('keyup', function() {
        table.column(5).search(this.value).draw();
    });
    $('#filterTest').on('change', function() {
        table.column(6).search(this.value).draw();
    });

    // ✅ Select All Checkbox
    $('#selectAll').on('click', function() {
        $('.studentCheckbox').prop('checked', this.checked);
    });

    // ✅ AJAX Action
    function updateStatus(action) {
        var ids = $('.studentCheckbox:checked').map(function() {
            return this.value;
        }).get();

        if (ids.length === 0) {
            alert("⚠️ Please select at least one student.");
            return;
        }

        $.post("student.php", { action: action, ids: ids }, function(response) {
            var res = JSON.parse(response);
            if (res.success) {
                $('#successModal').modal('show');
                setTimeout(() => location.reload(), 1000);
            }
        });
    }

    $('#btnPublish').click(function() { updateStatus("publish"); });
    $('#btnUnpublish').click(function() { updateStatus("unpublish"); });
});
</script>
</body>
</html>
