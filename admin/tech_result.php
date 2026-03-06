<?php
// Database connection
 $servername = "localhost";
$username = "root";
$password = "";
$dbname = "net_coders";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching data from the tech_results table with a LEFT JOIN to the Students table
$sql = "SELECT tr.id AS result_id, tr.quiz_id, tr.user_name, tr.score, tr.total_questions, tr.submission_date, 
               tr.student_id, s.id AS student_id, s.name AS student_name, s.email AS student_email, 
               s.contact AS student_contact, s.college, s.course
        FROM tech_results tr 
        LEFT JOIN Students s ON tr.student_id = s.id";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Results and Student Info</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            padding: 20px;
            text-align: center;
        }

        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .table-container h1 {
            color: #28a745;
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #28a745;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <h1>Tech Results and Student Information</h1>

        <!-- Tech Results and Students Table -->
        <table id="techResultsTable">
            <thead>
                <tr>
                    <th>Result ID</th>
                    <th>Quiz ID</th>
                    <th>User Name</th>
                    <th>Score</th>
                    <th>Total Questions</th>
                    <th>Submission Date</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Student Email</th>
                    <th>Student Phone</th>
                    <th>College</th>
                    <th>Course</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are any results
                if ($result->num_rows > 0) {
                    // Output each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['result_id']) . "</td>
                                <td>" . htmlspecialchars($row['quiz_id']) . "</td>
                                <td>" . htmlspecialchars($row['user_name']) . "</td>
                                <td>" . number_format($row['score'], 2) . "</td>
                                <td>" . htmlspecialchars($row['total_questions']) . "</td>
                                <td>" . htmlspecialchars($row['submission_date']) . "</td>
                                <td>" . htmlspecialchars($row['student_id']) . "</td>
                                <td>" . htmlspecialchars($row['student_name']) . "</td>
                                <td>" . htmlspecialchars($row['student_email']) . "</td>
                                <td>" . htmlspecialchars($row['student_contact']) . "</td>
                                <td>" . htmlspecialchars($row['college']) . "</td>
                                <td>" . htmlspecialchars($row['course']) . "</td>
                              </tr>";
                    }
                } else {
                    // If no results found
                    echo "<tr><td colspan='12'>No results found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            $('#techResultsTable').DataTable({
                "searching": true, // Enable searching
                "ordering": true,  // Enable sorting
                "order": [[0, 'asc']] // Default sorting on the first column (Result ID)
            });
        });
    </script>
</body>
</html>
