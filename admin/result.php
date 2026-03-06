 <?php
// DB connection
 $servername = "localhost";
$username = "root";
$password = "";
$dbname = "net_coders";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Dropdown filters
$colleges = $conn->query("SELECT DISTINCT college FROM students ORDER BY college");
$courses = $conn->query("SELECT DISTINCT course FROM students ORDER BY course");
$semesters = $conn->query("SELECT DISTINCT semester FROM students ORDER BY semester");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Records</title>

    <!-- Bootstrap & DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }
        table.dataTable thead th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Student Records</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <label>Filter by College:</label>
            <select id="filterCollege" class="form-select">
                <option value="">All Colleges</option>
                <?php while ($row = $colleges->fetch_assoc()) { ?>
                    <option><?= htmlspecialchars($row['college']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Filter by Course:</label>
            <select id="filterCourse" class="form-select">
                <option value="">All Courses</option>
                <?php while ($row = $courses->fetch_assoc()) { ?>
                    <option><?= htmlspecialchars($row['course']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Filter by Semester:</label>
            <select id="filterSemester" class="form-select">
                <option value="">All Semesters</option>
                <?php while ($row = $semesters->fetch_assoc()) { ?>
                    <option><?= htmlspecialchars($row['semester']) ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <table id="studentsTable" class="table table-bordered table-striped" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>College</th>
                <th>Course</th>
                <th>Semester</th>
                <th>Branch</th>
                <th>Contact</th>
                <th>WhatsApp</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT s.*, b.name AS branch_name FROM students s 
                    LEFT JOIN branches b ON s.branch = b.id 
                    ORDER BY s.id DESC";
            $result = $conn->query($sql);
            $i = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$i}</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['college']) . "</td>
                    <td>" . htmlspecialchars($row['course']) . "</td>
                    <td>" . htmlspecialchars($row['semester']) . "</td>
                    <td>" . htmlspecialchars($row['branch_name']) . "</td>
                    <td>" . htmlspecialchars($row['contact']) . "</td>
                    <td>" . htmlspecialchars($row['whatsapp']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                </tr>";
                $i++;
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    const table = $('#studentsTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf',     'print', 'colvis'],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        responsive: false // ensures no hidden rows under arrows
    });

    $.fn.dataTable.ext.search.push((settings, data) => {
        const college = $('#filterCollege').val().toLowerCase();
        const course = $('#filterCourse').val().toLowerCase();
        const semester = $('#filterSemester').val().toLowerCase();

        const rowCollege = data[2].toLowerCase();
        const rowCourse = data[3].toLowerCase();
        const rowSemester = data[4].toLowerCase();

        return (!college || rowCollege === college) &&
               (!course || rowCourse === course) &&
               (!semester || rowSemester === semester);
    });

    $('#filterCollege, #filterCourse, #filterSemester').on('change', () => {
        table.draw();
    });
});
</script>
</body>
</html>
