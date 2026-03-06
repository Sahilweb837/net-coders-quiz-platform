<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "net_coders";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Branch
if (isset($_POST['add_branch'])) {
    $branch_name = $_POST['branch_name'];
    $status = 1; // Active by default
    $sql = "INSERT INTO branches (name, status) VALUES ('$branch_name', $status)";
    $conn->query($sql);
    header('Location: branch.php');
}

// Edit Branch
if (isset($_POST['edit_branch'])) {
    $id = $_POST['id'];
    $branch_name = $_POST['branch_name'];
    $sql = "UPDATE branches SET name='$branch_name' WHERE id=$id";
    $conn->query($sql);
    header('Location: branch.php');
}

// Toggle Status
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $current_status = $_GET['current_status'];
    $new_status = $current_status == 1 ? 0 : 1; // Toggle between 1 and 0
    $sql = "UPDATE branches SET status=$new_status WHERE id=$id";
    $conn->query($sql);
    header('Location: branch.php');
}

// Fetch Branches
$result = $conn->query("SELECT * FROM branches");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch Management</title>
    <style>
      body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

table, th, td {
    border: 1px solid #ccc;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #007bff;
    color: white;
}

/* Make form inputs and buttons responsive */
form {
    margin-bottom: 20px;
}

input[type="text"], select {
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: calc(100% - 22px);
    box-sizing: border-box;
}

button {
    padding: 10px 20px;
    border: none;
    background-color: #007bff;
    color: white;
    border-radius: 5px;
    cursor: pointer;
 }

button:hover {
    background-color: #0056b3;
}

a.toggle {
    color: green;
    margin-right: 10px;
    text-decoration: none;
}

a.toggle.unpublish {
    color: red;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 5px;
    width: 50%;
    max-width: 600px;
}

.close {
    float: right;
    font-size: 20px;
    cursor: pointer;
    color: red;
}

.close:hover {
    color: darkred;
}

/* Responsive Design */
@media (max-width: 768px) {
    table, th, td {
        font-size: 14px;
    }

    button {
        width: auto;
    }

    .modal-content {
        width: 80%;
    }

    /* Stack form inputs vertically on smaller screens */
    input[type="text"], select {
        width: 100%;
    }

    /* Adjust the branch dropdown on mobile */
    select {
        width:  19%;
    }

    /* Adjust table layout on small screens */
    table {
        overflow-x: auto;
        display: block;
    }

    table th, table td {
        white-space: nowrap;
    }
}
.row{
     display:flex;
     align-items:center;
     margin-top: 0auto;
}
@media (max-width: 768px) {
    select {
        width: 20%;
    }
}
@media (max-width: 480px) {
    body {
        padding: 10px;
    }

    table th, table td {
        font-size: 12px;
    }

    .modal-content {
        width: 90%;
    }

    button {
     }
}

    </style>
</head>
<body>
    <h1>Branch Management</h1>

    <form action="" method="POST">
        <input type="text" name="branch_name" placeholder="Branch Name" required>
        <button type="submit" name="add_branch">Add Branch</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Branch Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['status'] == 1 ? 'Published' : 'Unpublished'; ?></td>
                    <td>
                        <a class="toggle <?php echo $row['status'] == 1 ? 'unpublish' : ''; ?>" 
                           href="?toggle_status=<?php echo $row['id']; ?>&current_status=<?php echo $row['status']; ?>">
                           <?php echo $row['status'] == 1 ? 'Unpublish' : 'Publish'; ?>
                        </a>
                        <button class="edit-btn" data-id="<?php echo $row['id']; ?>" data-name="<?php echo $row['name']; ?>">Edit</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Branch Dropdown</h2>
    <select name="branch_dropdown">
        <option value="" disabled selected>Select a Branch</option>
        <?php $branch_result = $conn->query("SELECT * FROM branches WHERE status=1"); ?>
        <?php while ($branch_row = $branch_result->fetch_assoc()): ?>
            <option value="<?php echo $branch_row['id']; ?>"><?php echo $branch_row['name']; ?></option>
        <?php endwhile; ?>
    </select>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form action="" method="POST">
                <input type="hidden" name="id" id="edit-id">
                <input type="text" name="branch_name" id="edit-name" required>
                <button type="submit" name="edit_branch">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editButtons = document.querySelectorAll('.edit-btn');
            const modal = document.getElementById('editModal');
            const closeModal = document.querySelector('.close');
            const editId = document.getElementById('edit-id');
            const editName = document.getElementById('edit-name');

            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    editId.value = button.getAttribute('data-id');
                    editName.value = button.getAttribute('data-name');
                    modal.style.display = 'flex';
                });
            });

            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
 