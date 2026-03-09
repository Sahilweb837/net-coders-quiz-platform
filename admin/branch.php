 <?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "net_coders"; // Fixed variable name (was $database)

$conn = new mysqli($servername, $username, $password, $dbname); // Fixed: using $dbname instead of $database

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create branches table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($createTable)) {
    die("Error creating table: " . $conn->error);
}

// Add Branch
if (isset($_POST['add_branch'])) {
    $branch_name = trim($_POST['branch_name']);
    if (!empty($branch_name)) {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO branches (name, status) VALUES (?, 1)");
        $stmt->bind_param("s", $branch_name);
        if ($stmt->execute()) {
            header('Location: branch.php?msg=added');
            exit();
        }
        $stmt->close();
    }
}

// Edit Branch
if (isset($_POST['edit_branch'])) {
    $id = (int)$_POST['id'];
    $branch_name = trim($_POST['branch_name']);
    if ($id > 0 && !empty($branch_name)) {
        $stmt = $conn->prepare("UPDATE branches SET name=? WHERE id=?");
        $stmt->bind_param("si", $branch_name, $id);
        if ($stmt->execute()) {
            header('Location: branch.php?msg=updated');
            exit();
        }
        $stmt->close();
    }
}

// Toggle Status
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];
    $current_status = (int)$_GET['current_status'];
    $new_status = $current_status == 1 ? 0 : 1;
    
    $stmt = $conn->prepare("UPDATE branches SET status=? WHERE id=?");
    $stmt->bind_param("ii", $new_status, $id);
    if ($stmt->execute()) {
        header('Location: branch.php?msg=toggled');
        exit();
    }
    $stmt->close();
}

// Fetch Branches
$result = $conn->query("SELECT * FROM branches ORDER BY id DESC");
if (!$result) {
    die("Error fetching branches: " . $conn->error);
}

// Get success message from URL
$message = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'added':
            $message = '<div class="alert alert-success">Branch added successfully!</div>';
            break;
        case 'updated':
            $message = '<div class="alert alert-success">Branch updated successfully!</div>';
            break;
        case 'toggled':
            $message = '<div class="alert alert-info">Branch status changed!</div>';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch Management - NetCoders</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }

        h2 {
            color: #555;
            margin: 30px 0 20px;
            font-size: 1.8em;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
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

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        input[type="text"] {
            flex: 1;
            min-width: 250px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        button[type="submit"] {
            background: #667eea;
            color: white;
        }

        button[type="submit"]:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .edit-btn {
            background: #ffc107;
            color: #333;
            margin-left: 10px;
        }

        .edit-btn:hover {
            background: #e0a800;
            transform: translateY(-2px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        th {
            background: #667eea;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-published {
            background: #d4edda;
            color: #155724;
        }

        .status-unpublished {
            background: #f8d7da;
            color: #721c24;
        }

        a.toggle {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        a.toggle.unpublish {
            background: #dc3545;
            color: white;
        }

        a.toggle:not(.unpublish) {
            background: #28a745;
            color: white;
        }

        a.toggle:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            background: white;
            cursor: pointer;
        }

        select:focus {
            outline: none;
            border-color: #667eea;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            position: relative;
            animation: slideUp 0.3s;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s;
        }

        .close:hover {
            color: #333;
        }

        .modal h2 {
            margin-bottom: 20px;
            color: #333;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 2em;
            }

            .form-group {
                flex-direction: column;
            }

            input[type="text"] {
                width: 100%;
            }

            button {
                width: 100%;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            td {
                min-width: 100px;
            }

            td:last-child {
                min-width: 150px;
            }

            .modal-content {
                width: 95%;
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5em;
            }

            .container {
                padding: 15px;
            }

            a.toggle {
                display: inline-block;
                margin-bottom: 5px;
            }

            .edit-btn {
                margin-left: 0;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏢 Branch Management - NetCoders</h1>
        
        <?php echo $message; ?>

        <!-- Add Branch Form -->
        <form action="" method="POST">
            <div class="form-group">
                <input type="text" name="branch_name" placeholder="Enter Branch Name" required>
                <button type="submit" name="add_branch">Add Branch</button>
            </div>
        </form>

        <!-- Branches Table -->
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
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $row['status'] == 1 ? 'status-published' : 'status-unpublished'; ?>">
                                    <?php echo $row['status'] == 1 ? 'Published' : 'Unpublished'; ?>
                                </span>
                            </td>
                            <td>
                                <a class="toggle <?php echo $row['status'] == 1 ? 'unpublish' : ''; ?>" 
                                   href="?toggle_status=<?php echo $row['id']; ?>&current_status=<?php echo $row['status']; ?>">
                                   <?php echo $row['status'] == 1 ? 'Unpublish' : 'Publish'; ?>
                                </a>
                                <button class="edit-btn" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>">Edit</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px;">
                            <p style="color: #999; font-size: 18px;">No branches found. Add your first branch above!</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Branch Dropdown Section -->
        <h2>📋 Available Branches</h2>
        <?php $branch_result = $conn->query("SELECT * FROM branches WHERE status=1 ORDER BY name"); ?>
        <select name="branch_dropdown">
            <option value="" disabled selected>Select a Branch</option>
            <?php if ($branch_result->num_rows > 0): ?>
                <?php while ($branch_row = $branch_result->fetch_assoc()): ?>
                    <option value="<?php echo $branch_row['id']; ?>">
                        <?php echo htmlspecialchars($branch_row['name']); ?>
                    </option>
                <?php endwhile; ?>
            <?php else: ?>
                <option value="" disabled>No active branches available</option>
            <?php endif; ?>
        </select>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Branch</h2>
            <form action="" method="POST">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-group">
                    <input type="text" name="branch_name" id="edit-name" required placeholder="Enter branch name">
                    <button type="submit" name="edit_branch">Save Changes</button>
                </div>
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

            // Open modal with edit data
            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    editId.value = button.getAttribute('data-id');
                    editName.value = button.getAttribute('data-name');
                    modal.style.display = 'flex';
                });
            });

            // Close modal functions
            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    modal.style.display = 'none';
                }
            });

            // Auto-hide alerts after 3 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 3000);
            });
        });
    </script>
</body>
</html>