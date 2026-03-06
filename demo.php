<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard with Sidebar</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
    }

    .sidebar {
      width: 200px;
      background-color: #333;
      color: #fff;
      height: 100vh;
      padding-top: 20px;
      position: fixed;
    }

    .sidebar a {
      display: block;
      color: #fff;
      text-decoration: none;
      padding: 10px 20px;
      margin: 5px 0;
    }

    .sidebar a:hover {
      background-color: #575757;
    }

    .content {
      margin-left: 200px;
      padding: 20px;
      width: 100%;
    }

    iframe {
      width: 100%;
      height: calc(100vh - 40px);
      border: none;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <a href="#" class="menu-item" data-file="admin/dashboard.php">Dashboard</a>
     <a href="#" class="menu-item" data-file="other.php">Add-Branch</a>
    <a href="#" class="menu-item" data-file="other.php">Add-College</a>

    <a href="#" class="menu-item" data-file="courses..php">Add-Courses</a>

 
    <a href="#" class="menu-item" data-file="setting.php">Setting</a>

    <a href="#" class="menu-item" data-file="admin.php/logout.php">logout</a>

  </div>

  <div class="content">
    <iframe id="content-frame" src=""></iframe>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.menu-item').on('click', function(e) {
        e.preventDefault();
        const file = $(this).data('file');
        $('#content-frame').attr('src', file);
      });
    });
  </script>
</body>
</html>
