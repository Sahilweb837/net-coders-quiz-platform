<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'solitaireinfo_quiz_db';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch registered students with their latest quiz score
$sql_students = "SELECT s.*, 
                    (SELECT CONCAT(score, '/', total_questions) 
                     FROM tech_results 
                     WHERE student_id = s.id 
                     ORDER BY created_at DESC LIMIT 1) AS latest_score 
                 FROM students s";
$result_students = $conn->query($sql_students);

echo "<div class='container mt-5'>";
echo "<h3 class='text-center'>Registered Students</h3>";
echo "<table id='studentsTable' class='table table-bordered table-striped mt-3'>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Semester</th>
                <th>Course</th>
                <th>College</th>
                <th>Contact</th>
                <th>Whatsapp</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>";

if ($result_students) {
    while ($row = $result_students->fetch_assoc()) {
        $mobile = isset($row['contact']) ? $row['contact'] : 'N/A';
        $whatsapp = isset($row['whatsapp']) ? $row['whatsapp'] : 'N/A';
        $latest_score = isset($row['latest_score']) ? $row['latest_score'] : 'Not Attempted';
        $remarks = ($latest_score !== 'Not Attempted') ? "Score: $latest_score" : "Not Attempted";

        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['semester']}</td>
                <td>{$row['course']}</td>
                <td>{$row['college']}</td>
                <td>$mobile</td>
                <td>$whatsapp</td>
                <td>$remarks</td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>No students found.</td></tr>";
}

echo "</tbody></table>";
echo "</div>";

$conn->close();
?>

<!-- DataTables JS and CSS Integration -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

<script>
$(document).ready(function() {
    $('#studentsTable').DataTable({
        "dom": 'Bfrtip',
        "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
        "searching": true,
        "ordering": true,
        "paging": true,
        "lengthChange": true
    });
});
</script>
