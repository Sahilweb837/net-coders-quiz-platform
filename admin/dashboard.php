 <!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NetCoders · #FF5533 theme</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #ff5533;        /* primary orange-red */
      --primary-dark: #e63e1f;
      --secondary: #ff8a5c;      /* softer complement */
      --success: #06d6a0;
      --warning: #ffd166;
      --danger: #ef476f;
      --dark: #2b2d42;
      --light: #f8f9fa;
      --gray: #8d99ae;
      --sidebar-width: 280px;
      --header-height: 70px;
      --transition: all 0.3s ease;
      --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      --card-radius: 16px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #0b0f1c;   /* deep base for particles */
      color: var(--dark);
      overflow-x: hidden;
      position: relative;
    }

    /* ---------- particle canvas (background) ---------- */
    #particle-canvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      pointer-events: none;
      background: radial-gradient(circle at 30% 30%, #1e1b2c, #0c0a17);
    }

    /* all content sits above canvas */
    .header, .sidebar, .content-wrapper, .notification-panel, .mobile-toggle {
      position: relative;
      z-index: 10;
    }

    /* Header Styles with new #ff5533 gradient */
    .header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: var(--header-height);
      background: linear-gradient(120deg, #ffcec4, #ffffffad);
      color: white;
      padding: 0 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
       transition: var(--transition);
    }

    .header .title {
      font-size: 24px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
      letter-spacing: -0.3px;
    }

    .header .title i {
      font-size: 30px;
      color: white;
      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    }

    .header .info {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .header .info .admin-profile {
      display: flex;
      align-items: center;
      gap: 10px;
      background: rgba(255, 255, 255, 0.2);
      padding: 8px 15px;
      border-radius: 50px;
      cursor: pointer;
      transition: var(--transition);
      border: 1px solid rgba(255,255,255,0.3);
    }

    .header .info .admin-profile:hover {
      background: rgba(255, 255, 255, 0.35);
    }

    .header .info .admin-profile img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid white;
    }

    .search-container {
      position: relative;
      width: 300px;
    }

    .search-box {
      width: 100%;
      padding: 12px 45px 12px 15px;
      border-radius: 50px;
      border: none;
      background: rgba(255, 255, 255, 0.2);
      color: white;
      font-size: 14px;
      transition: var(--transition);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255,255,255,0.2);
    }

    .search-box::placeholder {
      color: rgba(255, 255, 255, 0.8);
    }

    .search-box:focus {
      outline: none;
      background: rgba(255, 255, 255, 0.3);
      box-shadow: 0 0 0 3px rgba(255, 85, 51, 0.5);
    }

    .search-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: white;
      cursor: pointer;
    }

    .suggestions {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      width: 100%;
      background: white;
      box-shadow: var(--shadow);
      border-radius: 12px;
      z-index: 1000;
      max-height: 300px;
      overflow-y: auto;
      margin-top: 10px;
      padding: 10px 0;
    }

    .suggestions.open {
      display: block;
      animation: fadeIn 0.3s ease;
    }

    .suggestions div {
      padding: 12px 20px;
      cursor: pointer;
      font-size: 14px;
      color: var(--dark);
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .suggestions div i {
      color: var(--primary);
      font-size: 16px;
    }

    .suggestions div:hover {
      background-color: #fff0eb;
      color: var(--primary);
    }

    /* Sidebar with subtle blur / glassmorphism */
    .sidebar {
      position: fixed;
      top: var(--header-height);
      left: 0;
      width: var(--sidebar-width);
      height: calc(100vh - var(--header-height));
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      padding: 25px 0;
      box-shadow: var(--shadow);
      transition: var(--transition);
      z-index: 900;
      overflow-y: auto;
      border-right: 1px solid rgba(255, 85, 51, 0.2);
    }

    .sidebar.closed {
      transform: translateX(-100%);
    }

    .sidebar-header {
      padding: 0 25px 20px;
      border-bottom: 1px solid rgba(255, 85, 51, 0.2);
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .sidebar-header h3 {
      font-size: 18px;
      font-weight: 600;
      color: #ff5533;
    }

    .sidebar-header .toggle-sidebar {
      display: none;
      background: #f5f7fb;
      width: 30px;
      height: 30px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition);
    }

    .sidebar-header .toggle-sidebar:hover {
      background: var(--primary);
      color: white;
    }

    .sidebar-menu {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .menu-item {
      display: flex;
      align-items: center;
      padding: 14px 25px;
      color: var(--dark);
      text-decoration: none;
      transition: var(--transition);
      position: relative;
      margin: 5px 0;
      border-left: 4px solid transparent;
    }

    .menu-item i {
      margin-right: 15px;
      font-size: 18px;
      width: 24px;
      text-align: center;
      color: #ff5533;
    }

    .menu-item:hover {
      background: rgba(255, 85, 51, 0.08);
      color: #ff5533;
      border-left-color: #ff5533;
    }

    .menu-item.active {
      background: linear-gradient(to right, rgba(255, 85, 51, 0.15), transparent);
      color: #ff5533;
      border-left-color: #ff5533;
      font-weight: 500;
    }

    .menu-item.active::before {
      content: '';
      position: absolute;
      right: 0;
      top: 0;
      height: 100%;
      width: 3px;
      background: #ff5533;
      border-radius: 3px 0 0 3px;
    }

    .menu-badge {
      margin-left: auto;
      background: #ff5533;
      color: white;
      font-size: 12px;
      padding: 2px 8px;
      border-radius: 20px;
    }

    /* Content area with glass cards */
    .content-wrapper {
      margin-left: var(--sidebar-width);
      padding: 30px;
      padding-top: calc(var(--header-height) + 30px);
      transition: var(--transition);
      min-height: 100vh;
    }

    .content-wrapper.expanded {
      margin-left: 0;
    }

    .content-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      color: white;
      text-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }

    .content-header h1 {
      font-size: 28px;
      font-weight: 700;
      color: white;
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 10px;
      color: rgba(255,255,255,0.8);
      font-size: 14px;
    }

    .breadcrumb a {
      color: rgba(255,255,255,0.9);
      text-decoration: none;
      transition: var(--transition);
    }

    .breadcrumb a:hover {
      color: #ffaa8c;
    }

    .breadcrumb i {
      font-size: 12px;
    }

    /* Iframe Container with glass+shadow */
    .iframe-container {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: var(--card-radius);
      box-shadow: 0 20px 40px rgba(255, 85, 51, 0.2);
      overflow: hidden;
      height: calc(100vh - 180px);
      position: relative;
      border: 1px solid rgba(255,85,51,0.3);
    }

    #content-frame {
      width: 100%;
      height: 100%;
      border: none;
      transition: var(--transition);
    }

    .iframe-controls {
      position: absolute;
      top: 15px;
      right: 15px;
      display: flex;
      gap: 10px;
      z-index: 10;
    }

    .iframe-controls button {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: white;
      border: none;
      box-shadow: var(--shadow);
      color: #ff5533;
      cursor: pointer;
      transition: var(--transition);
    }

    .iframe-controls button:hover {
      background: #ff5533;
      color: white;
    }

    /* Dashboard Stats Cards (glass) */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: var(--card-radius);
      padding: 25px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      display: flex;
      align-items: center;
      transition: var(--transition);
      cursor: pointer;
      border: 1px solid rgba(255,85,51,0.35);
    }

    .stat-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 15px 30px rgba(255,85,51,0.4);
      background: rgba(255, 255, 255, 0.9);
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-right: 15px;
      background: rgba(255, 85, 51, 0.2);
      color: #ff5533;
    }

    .stat-details h3 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 5px;
      color: #1e1e2a;
    }

    .stat-details p {
      color: #4a4e69;
      font-size: 14px;
      margin: 0;
      font-weight: 500;
    }

    /* File Explorer with glass */
    .file-explorer {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: var(--card-radius);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      margin-bottom: 30px;
      overflow: hidden;
      border: 1px solid rgba(255,85,51,0.3);
    }

    .explorer-header {
      padding: 20px;
      border-bottom: 1px solid rgba(255,85,51,0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .explorer-header h3 {
      margin: 0;
      font-size: 18px;
      font-weight: 600;
      color: #ff5533;
    }

    .explorer-content {
      padding: 20px;
    }

    .file-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 15px;
    }

    .file-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 15px;
      border-radius: 12px;
      background: rgba(255, 245, 240, 0.7);
      backdrop-filter: blur(4px);
      transition: var(--transition);
      cursor: pointer;
      border: 1px solid rgba(255,85,51,0.2);
    }

    .file-item:hover {
      background: rgba(255, 235, 225, 0.9);
      transform: translateY(-3px);
      border-color: #ff5533;
    }

    .file-icon {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 10px;
      background: rgba(255, 85, 51, 0.2);
      color: #ff5533;
    }

    .file-name {
      font-size: 12px;
      text-align: center;
      font-weight: 500;
      color: #2b2d42;
    }

    /* Notification Panel with new theme */
    .notification-panel {
      position: fixed;
      top: var(--header-height);
      right: -350px;
      width: 350px;
      height: calc(100vh - var(--header-height));
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      box-shadow: -5px 0 20px rgba(255,85,51,0.25);
      z-index: 950;
      transition: var(--transition);
      padding: 20px;
      overflow-y: auto;
      border-left: 2px solid #ff5533;
    }

    .notification-panel.open {
      right: 0;
    }

    .notification-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #ffc8b8;
    }

    .notification-header h3 {
      font-size: 18px;
      font-weight: 600;
      color: #ff5533;
    }

    .notification-item {
      padding: 15px;
      border-radius: 12px;
      margin-bottom: 15px;
      background: rgba(255, 235, 230, 0.7);
      display: flex;
      align-items: flex-start;
      gap: 15px;
      border: 1px solid rgba(255,85,51,0.2);
    }

    .notification-item.unread {
      background: rgba(255, 225, 215, 0.9);
    }

    .notification-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
      background: rgba(255,85,51,0.2);
      color: #ff5533;
    }

    .notification-content h4 {
      font-size: 14px;
      margin-bottom: 5px;
      font-weight: 500;
      color: #2b2d42;
    }

    .notification-content p {
      font-size: 13px;
      color: #4a4e69;
      margin-bottom: 5px;
    }

    .notification-time {
      font-size: 12px;
      color: #ff7a5c;
    }

    /* Mobile Toggle */
    .mobile-toggle {
      display: none;
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: #ff5533;
      color: white;
      box-shadow: 0 4px 15px rgba(255,85,51,0.5);
      z-index: 800;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      cursor: pointer;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* responsive same as before */
    @media (max-width: 992px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .content-wrapper { margin-left: 0; }
      .search-container { width: 200px; }
      .mobile-toggle { display: flex; }
      .sidebar-header .toggle-sidebar { display: flex; }
    }

    @media (max-width: 768px) {
      .header .title span { display: none; }
      .search-container { width: 40px; overflow: hidden; }
      .search-container.expanded { width: 200px; }
      .search-box { padding-left: 40px; }
      .header .info .admin-profile span { display: none; }
      .stats-container { grid-template-columns: 1fr; }
      .notification-panel { width: 100%; right: -100%; }
    }

    @media (max-width: 576px) {
      .header { padding: 0 15px; }
      .content-wrapper { padding: 20px 15px; padding-top: calc(var(--header-height) + 20px); }
      .content-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    }

    /* small tweaks for white text on dark background (header/breadcrumb) */
    .content-header h1, .breadcrumb, .breadcrumb a, #current-datetime {
      color: white;
      text-shadow: 0 2px 3px rgba(0,0,0,0.3);
    }
    #current-datetime {
      background: rgba(0,0,0,0.2);
      padding: 5px 12px;
      border-radius: 30px;
      font-size: 13px;
      backdrop-filter: blur(4px);
    }
  </style>
</head>

<body>

  <!-- particle canvas background -->
  <canvas id="particle-canvas"></canvas>

  <!-- Header Section with new logo (fa-code) + #ff553353 gradient -->
  <div class="header">
                 <img src="../assests/logo1.png" height="100px" alt="NetCoders"  >


    <!-- Search Section -->
    <div class="search-container" id="search-container">
      <input type="text" class="search-box" id="search-box" placeholder="Search...">
      <div class="search-icon" id="search-icon">
        <i class="fas fa-search"></i>
      </div>
      <div class="suggestions" id="suggestions"></div>
    </div>

    <div class="info">
      <div id="current-datetime"></div>
      <div class="admin-profile" id="admin-profile">
                        <img src="../assests/logo1.png" alt="NetCoders" class="loading-logo">

         <span id="admin-name">Admin</span>
        <i class="fas fa-chevron-down"></i>
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h3>Navigation</h3>
      <div class="toggle-sidebar" onclick="toggleSidebar()">
        <i class="fas fa-times"></i>
      </div>
    </div>
    
    <div class="sidebar-menu">
      <a href="#" class="menu-item active" data-file="student.php">
        <i class="fas fa-chart-bar"></i>
        <span>Dashboard</span>
      </a>
      <a href="#" class="menu-item" data-file="branch.php">
        <i class="fas fa-code-branch"></i>
        <span>Branches</span>
        <span class="menu-badge">3</span>
      </a>
      <a href="#" class="menu-item" data-file="college.php">
        <i class="fas fa-university"></i>
        <span>Colleges</span>
      </a>
      <a href="#" class="menu-item" data-file="courses.php">
        <i class="fas fa-book"></i>
        <span>Courses</span>
        <span class="menu-badge">12</span>
      </a>
      <a href="#" class="menu-item" data-file="result.php">
        <i class="fas fa-users"></i>
        <span>Students</span>
      </a>
      <a href="#" class="menu-item" data-file="settings.php">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
      <a href="#" class="menu-item" data-file="logout.php">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <!-- Notification Panel -->
  <div class="notification-panel" id="notification-panel">
    <div class="notification-header">
      <h3>Notifications</h3>
      <div class="notification-actions">
        <button class="btn btn-sm" style="background:#ff5533; color:white; border:none;">Mark all as read</button>
      </div>
    </div>
    
    <div class="notification-list">
      <div class="notification-item unread">
        <div class="notification-icon"><i class="fas fa-user-plus"></i></div>
        <div class="notification-content">
          <h4>New Student Registration</h4>
          <p>John Doe has registered for the Web Development course</p>
          <div class="notification-time">10 minutes ago</div>
        </div>
      </div>
      
      <div class="notification-item">
        <div class="notification-icon"><i class="fas fa-check-circle"></i></div>
        <div class="notification-content">
          <h4>Payment Received</h4>
          <p>Payment of $250 received from Jane Smith</p>
          <div class="notification-time">2 hours ago</div>
        </div>
      </div>
      
      <div class="notification-item">
        <div class="notification-icon"><i class="fas fa-exclamation-circle"></i></div>
        <div class="notification-content">
          <h4>Course Almost Full</h4>
          <p>The Data Science course has only 3 seats remaining</p>
          <div class="notification-time">5 hours ago</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Content Wrapper -->
  <div class="content-wrapper" id="content-wrapper">
    <div class="content-header">
      <h1>Dashboard</h1>
      <div class="breadcrumb">
        <a href="#">Home</a>
        <i class="fas fa-chevron-right"></i>
        <span>Dashboard</span>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-container">
      <div class="stat-card" data-file="student.php">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-details">
          <h3>1,248</h3>
          <p>Total Students</p>
        </div>
      </div>
      
      <div class="stat-card" data-file="courses.php">
        <div class="stat-icon"><i class="fas fa-book"></i></div>
        <div class="stat-details">
          <h3>24</h3>
          <p>Active Courses</p>
        </div>
      </div>
      
      <div class="stat-card" data-file="result.php">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-details">
          <h3>92%</h3>
          <p>Completion Rate</p>
        </div>
      </div>
      
      <div class="stat-card" data-file="reports.php">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-details">
          <h3>$15.8K</h3>
          <p>Total Revenue</p>
        </div>
      </div>
    </div>

    <!-- File Explorer -->
    <div class="file-explorer">
      <div class="explorer-header">
        <h3>Quick Access Files</h3>
        <button class="btn btn-sm" style="background:#ff5533; color:white;">View All</button>
      </div>
      <div class="explorer-content">
        <div class="file-grid">
          <div class="file-item" data-file="student.php"><div class="file-icon"><i class="fas fa-users"></i></div><div class="file-name">Students</div></div>
          <div class="file-item" data-file="courses.php"><div class="file-icon"><i class="fas fa-book"></i></div><div class="file-name">Courses</div></div>
          <div class="file-item" data-file="branch.php"><div class="file-icon"><i class="fas fa-code-branch"></i></div><div class="file-name">Branches</div></div>
          <div class="file-item" data-file="college.php"><div class="file-icon"><i class="fas fa-university"></i></div><div class="file-name">Colleges</div></div>
          <div class="file-item" data-file="result.php"><div class="file-icon"><i class="fas fa-chart-bar"></i></div><div class="file-name">Results</div></div>
          <div class="file-item" data-file="reports.php"><div class="file-icon"><i class="fas fa-file-alt"></i></div><div class="file-name">Reports</div></div>
        </div>
      </div>
    </div>

    <!-- Iframe Container -->
    <div class="iframe-container">
      <div class="iframe-controls">
        <button id="refresh-frame" title="Refresh"><i class="fas fa-sync-alt"></i></button>
        <button id="fullscreen-frame" title="Fullscreen"><i class="fas fa-expand"></i></button>
      </div>
      <iframe id="content-frame" src="student.php"></iframe>
    </div>
  </div>

  <!-- Mobile Toggle Button -->
  <div class="mobile-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </div>

  <!-- JavaScript (jQuery + particles + same logic) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Particle animation background (box + bg) – properly working
    (function() {
      const canvas = document.getElementById('particle-canvas');
      const ctx = canvas.getContext('2d');
      let width, height;
      let particles = [];

      const PARTICLE_COUNT = 70;

      function initParticles() {
        particles = [];
        for (let i = 0; i < PARTICLE_COUNT; i++) {
          particles.push({
            x: Math.random(),
            y: Math.random(),
            radius: Math.random() * 3 + 1.2,
            speedX: (Math.random() - 0.5) * 0.2,
            speedY: (Math.random() - 0.5) * 0.2,
            alpha: Math.random() * 0.5 + 0.2,
          });
        }
      }

      function resizeCanvas() {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
      }

      function drawParticles() {
        if (!ctx) return;
        ctx.clearRect(0, 0, width, height);

        for (let p of particles) {
          p.x += p.speedX * 0.015;
          p.y += p.speedY * 0.015;
          if (p.x < 0) p.x = 1;
          if (p.x > 1) p.x = 0;
          if (p.y < 0) p.y = 1;
          if (p.y > 1) p.y = 0;

          const xPos = p.x * width;
          const yPos = p.y * height;

          // glow
          ctx.beginPath();
          ctx.arc(xPos, yPos, p.radius * 1.8, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(255, 130, 100, ${p.alpha * 0.2})`; // #ff5533 tint
          ctx.fill();

          // core
          ctx.beginPath();
          ctx.arc(xPos, yPos, p.radius * 0.9, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(255, 85, 51, ${p.alpha})`;
          ctx.fill();

          // highlight
          ctx.beginPath();
          ctx.arc(xPos-1, yPos-1, p.radius*0.3, 0, Math.PI*2);
          ctx.fillStyle = `rgba(255, 255, 255, ${p.alpha*0.8})`;
          ctx.fill();
        }

        // faint connecting lines
        for (let i = 0; i < particles.length; i += 3) {
          for (let j = i + 1; j < particles.length; j += 2) {
            const p1 = particles[i];
            const p2 = particles[j];
            const dx = Math.abs(p1.x - p2.x) * width;
            const dy = Math.abs(p1.y - p2.y) * height;
            const dist = Math.sqrt(dx*dx + dy*dy);
            if (dist < 120) {
              ctx.beginPath();
              ctx.strokeStyle = `rgba(255, 120, 80, ${0.12 * (1 - dist/180)})`;
              ctx.lineWidth = 1;
              ctx.moveTo(p1.x*width, p1.y*height);
              ctx.lineTo(p2.x*width, p2.y*height);
              ctx.stroke();
            }
          }
        }
        requestAnimationFrame(drawParticles);
      }

      initParticles();
      resizeCanvas();
      drawParticles();
      window.addEventListener('resize', resizeCanvas);
    })();

    // original dashboard js (with minor theme adjustments)
    $(document).ready(function () {
      function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        $('#current-datetime').text(now.toLocaleString('en-US', options));
      }
      setInterval(updateDateTime, 1000);
      updateDateTime();

      $('.menu-item, .stat-card, .file-item').on('click', function (e) {
        e.preventDefault();
        const file = $(this).data('file');
        let pageName = $(this).find('span').text() || $(this).find('.file-name').text();
        loadFileInIframe(file, pageName);
        $('.menu-item').removeClass('active');
        $(`.menu-item[data-file="${file}"]`).addClass('active');
        if ($(window).width() < 992) $('#sidebar').removeClass('open');
      });

      function loadFileInIframe(file, pageName) {
        $('#content-frame').css('opacity', '0.7');
        $('#content-frame').attr('src', file);
        $('.content-header h1').text(pageName);
        $('.breadcrumb span').text(pageName);
        document.title = `NetCoders - ${pageName}`;
      }

      $('#refresh-frame').on('click', function() {
        const currentSrc = $('#content-frame').attr('src');
        $('#content-frame').attr('src', 'about:blank');
        setTimeout(() => $('#content-frame').attr('src', currentSrc), 100);
      });

      $('#fullscreen-frame').on('click', function() {
        const iframe = $('#content-frame')[0];
        if (!document.fullscreenElement) {
          if (iframe.requestFullscreen) iframe.requestFullscreen();
          else if (iframe.webkitRequestFullscreen) iframe.webkitRequestFullscreen();
          else if (iframe.msRequestFullscreen) iframe.msRequestFullscreen();
        } else {
          if (document.exitFullscreen) document.exitFullscreen();
          else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
          else if (document.msExitFullscreen) document.msExitFullscreen();
        }
      });

      $('#search-box').on('input', function () {
        const query = $(this).val().toLowerCase();
        if (query.length > 0) {
          const menuItems = [
            { name: 'Dashboard', file: 'student.php', icon: 'chart-bar' },
            { name: 'Branches', file: 'branch.php', icon: 'code-branch' },
            { name: 'Colleges', file: 'college.php', icon: 'university' },
            { name: 'Courses', file: 'course.php', icon: 'book' },
            { name: 'Students', file: 'result.php', icon: 'users' },
            { name: 'Settings', file: 'settings.php', icon: 'cog' },
            { name: 'Logout', file: 'logout.php', icon: 'sign-out-alt' }
          ];
          const suggestions = menuItems.filter(item => item.name.toLowerCase().includes(query));
          const suggestionsBox = $('#suggestions');
          suggestionsBox.empty();
          if (suggestions.length) {
            suggestions.forEach(s => suggestionsBox.append(`<div data-file="${s.file}"><i class="fas fa-${s.icon}"></i> ${s.name}</div>`));
          } else {
            suggestionsBox.append('<div class="no-results">No results found</div>');
          }
          suggestionsBox.addClass('open');
        } else $('#suggestions').removeClass('open');
      });

      $(document).on('click', '.suggestions div', function () {
        if ($(this).hasClass('no-results')) return;
        const selectedText = $(this).text();
        const file = $(this).data('file');
        $('#search-box').val(selectedText);
        $('#suggestions').removeClass('open');
        loadFileInIframe(file, selectedText.trim());
        $('.menu-item').removeClass('active');
        $(`a[data-file="${file}"]`).addClass('active');
      });

      $(document).on('click', function (e) {
        if (!$(e.target).closest('.search-container').length) $('#suggestions').removeClass('open');
      });

      $('#admin-profile').on('click', function () { $('#notification-panel').toggleClass('open'); });

      $('#search-icon').on('click', function () {
        if ($(window).width() < 768) {
          $('#search-container').toggleClass('expanded');
          if ($('#search-container').hasClass('expanded')) $('#search-box').focus();
        }
      });
    });

    function toggleSidebar() { $('#sidebar').toggleClass('open'); }

    $(document).on('click', function (e) {
      if ($(window).width() < 992 && !$(e.target).closest('#sidebar').length && !$(e.target).closest('.mobile-toggle').length && $('#sidebar').hasClass('open')) {
        $('#sidebar').removeClass('open');
      }
    });

    $('#content-frame').on('load', function () { $(this).css('opacity', 1); });

    function adjustIframeHeight() {
      const headerH = $('.content-header').outerHeight() + parseInt($('.content-wrapper').css('padding-top'));
      const statsH = $('.stats-container').outerHeight() || 0;
      const explorerH = $('.file-explorer').outerHeight() || 0;
      const padding = 30;
      const iframeH = window.innerHeight - headerH - statsH - explorerH - padding;
      $('.iframe-container').css('height', iframeH + 'px');
    }
    $(window).resize(adjustIframeHeight);
    setTimeout(adjustIframeHeight, 100);
  </script>

</body>

</html>