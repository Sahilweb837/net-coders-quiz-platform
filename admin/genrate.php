<?php
//with filter functionality
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

include('../db.php');

// Fetch all quizzes
$quizzes = $conn->query("SELECT id, title FROM quizzes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .dashboard {
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav a {
            margin-right: 15px;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
        }

        .tab {
            padding: 13px 27px;
            cursor: pointer;
            background: #f4f4f4;
            margin-right: 10px;
            border-radius: 8px 8px 0 0;
            border: 1px solid #ddd;
        }

        .tab.active {
            background: white;
            border-bottom: none;
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #0056b3;
            color: #fff;
            text-transform: uppercase;
            font-weight: bold;
        }
        h2.resultdiv {
    padding-bottom: 20px;
}
.dataTables_wrapper .dataTables_filter input {

    padding: 12px !important;

}
table#DataTables_Table_0 {
    padding-top: 40px;
}

th.sorting.sorting_asc {
    background: #007BFF; 
}
    </style>
</head>
<body>
    <header>
        <img src="../assests/register_form_latest.png" alt="Solitaire Infosystems Logo" class="logo">
    </header>
    <div class="dashboard">
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
       <h3>Generate Temporary Quiz Link</h3> 
    <form method="POST" action="generate_temp_link.php">
       <label for="quiz_id">Select Quiz:</label> 
       <select name="quiz_id" id="quiz_id" required> 
         <?php
             $quizzes = $conn->query("SELECT id, title FROM quizzes");
            if ($quizzes->num_rows > 0) {
                 while ($quiz = $quizzes->fetch_assoc()) {
                    echo "<option value='{$quiz['id']}'>{$quiz['title']}</option>";
                 }
              }
         ?>
        </select> 
       <button type="submit">Generate Link</button> 
    </form>

        <h2 class="resultdiv">Student Results</h2>

        <!-- Tabs -->
        <div class="tabs">
            <?php
            $firstQuizId = null;
            if ($quizzes->num_rows > 0) {
                while ($quiz = $quizzes->fetch_assoc()) {
                    if (!$firstQuizId) $firstQuizId = $quiz['id'];
                    echo "<div class='tab' data-tab='quiz_{$quiz['id']}'>{$quiz['title']}</div>";
                }
            } else {
                echo "<p>No quizzes available.</p>";
            }
            ?>
        </div>

        <!-- Tab Content -->
        <?php
        $quizzes->data_seek(0); // Reset pointer for looping
        if ($quizzes->num_rows > 0) {
            while ($quiz = $quizzes->fetch_assoc()) {
                $quizId = $quiz['id'];
                $quizTitle = $quiz['title'];

                // Fetch unique courses for dropdown filter
                $courseQuery = "SELECT DISTINCT students.course 
                                FROM quiz_responses 
                                INNER JOIN students ON quiz_responses.student_id = students.id 
                                WHERE quiz_responses.quiz_id = $quizId";
                $courseResults = $conn->query($courseQuery);

                $courseOptions = "<option value=''>All</option>";
                if ($courseResults->num_rows > 0) {
                    while ($courseRow = $courseResults->fetch_assoc()) {
                        $course = htmlspecialchars($courseRow['course'], ENT_QUOTES, 'UTF-8');
                        $courseOptions .= "<option value='{$course}'>{$course}</option>";
                    }
                }

                // Tab Content
                echo "<div class='tab-content' id='quiz_{$quizId}'>
                      <table class='datatable' id='datatable-<?= $quizId ?>'></table>

                      </div>";
            }
        }
        ?>
    </div>
    <!-- Move jQuery before DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        // Tab functionality
        $('.tab').on('click', function () {
            const tabId = $(this).data('tab');
            const quizId = tabId.split('_')[1];

            // Highlight active tab
            $('.tab').removeClass('active');
            $(this).addClass('active');

            // Show the corresponding tab content
            $('.tab-content').removeClass('active');
            $('#' + tabId).addClass('active');

            // Initialize DataTable for the active quiz tab
            const table = $('#' + tabId + ' .datatable').DataTable({
                serverSide: true,
                processing: true,
                destroy: true, // Destroy any existing instance
                ajax: {
                    url: 'fetch_results.php',
                    data: function (d) {
                        d.quiz_id = quizId; // Pass quiz ID to the server
                        d.filter_course = $(`#filter-course-${quizId}`).val(); // Pass course filter value
                    }
                },
                columns: [
                    { title: "Name", data: 0 },
                    { title: "College", data: 1 },
                    { title: "Course", data: 2 },
                    { title: "Semester", data: 3 },
                    { title: "Branch", data: 4 },
                    { title: "Score", data: 5 }
                ]
            });

            // Handle course filter
            $(`#filter-course-${quizId}`).on('change', function () {
                table.ajax.reload(); // Reload data with the new filter
            });
        });

        // Automatically activate the first tab
        $('.tab').first().click();
    });
</script>

</body>
</html>
 