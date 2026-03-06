<?php
include('../db.php');

$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
$orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$filterCourse = isset($_GET['filter_course']) ? $_GET['filter_course'] : '';

// Map DataTables column index to database column names
$columns = ['students.name', 'students.college', 'students.course', 'students.semester', 'students.branch', 'quiz_responses.result'];
$orderColumn = $columns[$orderColumnIndex];

// Build the query
$where = "quiz_responses.quiz_id = $quiz_id";
if (!empty($filterCourse)) {
    $where .= " AND students.course = '" . $conn->real_escape_string($filterCourse) . "'";
}
if (!empty($searchValue)) {
    $searchValue = $conn->real_escape_string($searchValue);
    $where .= " AND (students.name LIKE '%$searchValue%' 
                      OR students.college LIKE '%$searchValue%' 
                      OR students.course LIKE '%$searchValue%' 
                      OR students.branch LIKE '%$searchValue%')";
}

// Count total records
$totalQuery = "SELECT COUNT(*) AS total FROM quiz_responses 
               INNER JOIN students ON quiz_responses.student_id = students.id 
               WHERE $where";
$totalResult = $conn->query($totalQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];

// Fetch filtered data
$dataQuery = "SELECT students.name, students.college, students.course, students.semester, students.branch, quiz_responses.result 
              FROM quiz_responses 
              INNER JOIN students ON quiz_responses.student_id = students.id 
              WHERE $where 
              ORDER BY $orderColumn $orderDir 
              LIMIT $start, $length";
$dataResult = $conn->query($dataQuery);

$data = [];
while ($row = $dataResult->fetch_assoc()) {
    $data[] = [
        'name' => $row['name'],
        'college' => $row['college'],
        'course' => $row['course'],
        'semester' => $row['semester'],
        'branch' => $row['branch'],
        'result' => $row['result']
    ];
}

// Return response as JSON
$response = [
    "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
];

echo json_encode($response);
?>
