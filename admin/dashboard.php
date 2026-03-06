  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solitaire Infosys - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
      :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --secondary: #7209b7;
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
        background-color: #f5f7fb;
        color: var(--dark);
        overflow-x: hidden;
      }

      /* Header Styles */
      .header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: var(--header-height);
        background: linear-gradient(120deg, var(--primary), var(--secondary));
        color: white;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 1000;
        box-shadow: var(--shadow);
        transition: var(--transition);
      }

      .header .title {
        font-size: 22px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .header .title i {
        font-size: 26px;
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
        background: rgba(255, 255, 255, 0.15);
        padding: 8px 15px;
        border-radius: 50px;
        cursor: pointer;
        transition: var(--transition);
      }

      .header .info .admin-profile:hover {
        background: rgba(255, 255, 255, 0.25);
      }

      .header .info .admin-profile img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.5);
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
        backdrop-filter: blur(10px);
      }

      .search-box::placeholder {
        color: rgba(255, 255, 255, 0.7);
      }

      .search-box:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
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
        background-color: #f0f5ff;
        color: var(--primary);
      }

      /* Sidebar Styles */
      .sidebar {
        position: fixed;
        top: var(--header-height);
        left: 0;
        width: var(--sidebar-width);
        height: calc(100vh - var(--header-height));
        background: white;
        padding: 25px 0;
        box-shadow: var(--shadow);
        transition: var(--transition);
        z-index: 900;
        overflow-y: auto;
      }

      .sidebar.closed {
        transform: translateX(-100%);
      }

      .sidebar-header {
        padding: 0 25px 20px;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }

      .sidebar-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: var(--dark);
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
      }

      .menu-item:hover {
        background: #f8faff;
        color: var(--primary);
        border-left-color: var(--primary);
      }

      .menu-item.active {
        background: linear-gradient(to right, rgba(67, 97, 238, 0.1), transparent);
        color: var(--primary);
        border-left-color: var(--primary);
        font-weight: 500;
      }

      .menu-item.active::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: var(--primary);
        border-radius: 3px 0 0 3px;
      }

      .menu-badge {
        margin-left: auto;
        background: var(--primary);
        color: white;
        font-size: 12px;
        padding: 2px 8px;
        border-radius: 20px;
      }

      /* Content Area */
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
      }

      .content-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
      }

      .breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--gray);
        font-size: 14px;
      }

      .breadcrumb a {
        color: var(--gray);
        text-decoration: none;
        transition: var(--transition);
      }

      .breadcrumb a:hover {
        color: var(--primary);
      }

      .breadcrumb i {
        font-size: 12px;
      }

      /* Iframe Container */
      .iframe-container {
        background: white;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        height: calc(100vh - 180px);
        position: relative;
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
        color: var(--dark);
        cursor: pointer;
        transition: var(--transition);
      }

      .iframe-controls button:hover {
        background: var(--primary);
        color: white;
      }

      /* Dashboard Stats */
      .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
      }

      .stat-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 25px;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        transition: var(--transition);
        cursor: pointer;
      }

      .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
      }

      .stat-details h3 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
      }

      .stat-details p {
        color: var(--gray);
        font-size: 14px;
        margin: 0;
      }

      /* File Explorer */
      .file-explorer {
        background: white;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow);
        margin-bottom: 30px;
        overflow: hidden;
      }

      .explorer-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .explorer-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
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
        background: #f8faff;
        transition: var(--transition);
        cursor: pointer;
      }

      .file-item:hover {
        background: #e6eeff;
        transform: translateY(-3px);
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
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary);
      }

      .file-name {
        font-size: 12px;
        text-align: center;
        font-weight: 500;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      /* Notification Panel */
      .notification-panel {
        position: fixed;
        top: var(--header-height);
        right: -350px;
        width: 350px;
        height: calc(100vh - var(--header-height));
        background: white;
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        z-index: 950;
        transition: var(--transition);
        padding: 20px;
        overflow-y: auto;
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
        border-bottom: 1px solid #eee;
      }

      .notification-header h3 {
        font-size: 18px;
        font-weight: 600;
      }

      .notification-item {
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 15px;
        background: #f8faff;
        display: flex;
        align-items: flex-start;
        gap: 15px;
      }

      .notification-item.unread {
        background: #e6eeff;
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
      }

      .notification-content {
        flex: 1;
      }

      .notification-content h4 {
        font-size: 14px;
        margin-bottom: 5px;
        font-weight: 500;
      }

      .notification-content p {
        font-size: 13px;
        color: var(--gray);
        margin-bottom: 5px;
      }

      .notification-time {
        font-size: 12px;
        color: var(--gray);
      }

      /* Mobile Toggle Button */
      .mobile-toggle {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        z-index: 800;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        cursor: pointer;
      }

      /* Animations */
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }

      @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
      }

      /* Responsive Styles */
      @media (max-width: 992px) {
        .sidebar {
          transform: translateX(-100%);
        }
        
        .sidebar.open {
          transform: translateX(0);
        }
        
        .content-wrapper {
          margin-left: 0;
        }
        
        .search-container {
          width: 200px;
        }
        
        .mobile-toggle {
          display: flex;
        }
        
        .sidebar-header .toggle-sidebar {
          display: flex;
        }
      }

      @media (max-width: 768px) {
        .header .title span {
          display: none;
        }
        
        .search-container {
          width: 40px;
          overflow: hidden;
          transition: var(--transition);
        }
        
        .search-container.expanded {
          width: 200px;
        }
        
        .search-box {
          padding-left: 40px;
        }
        
        .header .info .admin-profile span {
          display: none;
        }
        
        .stats-container {
          grid-template-columns: 1fr;
        }
        
        .notification-panel {
          width: 100%;
          right: -100%;
        }
        
        .file-grid {
          grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        }
      }

      @media (max-width: 576px) {
        .header {
          padding: 0 15px;
        }
        
        .content-wrapper {
          padding: 20px 15px;
          padding-top: calc(var(--header-height) + 20px);
        }
        
        .content-header {
          flex-direction: column;
          align-items: flex-start;
          gap: 10px;
        }
        
        .iframe-controls {
          top: 10px;
          right: 10px;
        }
        
        .iframe-controls button {
          width: 32px;
          height: 32px;
        }
      }
    </style>
  </head>

  <body>

    <!-- Header Section -->
    <div class="header">
      <div class="title">
        <i class="fas fa-gem"></i>
        <span>Solitaire Infosys</span>
      </div>

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
          <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="Admin">
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
          <button class="btn btn-sm btn-outline-primary">Mark all as read</button>
        </div>
      </div>
      
      <div class="notification-list">
        <div class="notification-item unread">
          <div class="notification-icon" style="background: rgba(67, 97, 238, 0.1); color: var(--primary);">
            <i class="fas fa-user-plus"></i>
          </div>
          <div class="notification-content">
            <h4>New Student Registration</h4>
            <p>John Doe has registered for the Web Development course</p>
            <div class="notification-time">10 minutes ago</div>
          </div>
        </div>
        
        <div class="notification-item">
          <div class="notification-icon" style="background: rgba(6, 214, 160, 0.1); color: var(--success);">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="notification-content">
            <h4>Payment Received</h4>
            <p>Payment of $250 received from Jane Smith</p>
            <div class="notification-time">2 hours ago</div>
          </div>
        </div>
        
        <div class="notification-item">
          <div class="notification-icon" style="background: rgba(239, 71, 111, 0.1); color: var(--danger);">
            <i class="fas fa-exclamation-circle"></i>
          </div>
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
          <div class="stat-icon" style="background: rgba(67, 97, 238, 0.1); color: var(--primary);">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-details">
            <h3>1,248</h3>
            <p>Total Students</p>
          </div>
        </div>
        
        <div class="stat-card" data-file="courses.php">
          <div class="stat-icon" style="background: rgba(114, 9, 183, 0.1); color: var(--secondary);">
            <i class="fas fa-book"></i>
          </div>
          <div class="stat-details">
            <h3>24</h3>
            <p>Active Courses</p>
          </div>
        </div>
        
        <div class="stat-card" data-file="result.php">
          <div class="stat-icon" style="background: rgba(6, 214, 160, 0.1); color: var(--success);">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-details">
            <h3>92%</h3>
            <p>Completion Rate</p>
          </div>
        </div>
        
        <div class="stat-card" data-file="reports.php">
          <div class="stat-icon" style="background: rgba(255, 209, 102, 0.1); color: var(--warning);">
            <i class="fas fa-chart-line"></i>
          </div>
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
          <button class="btn btn-sm btn-outline-primary">View All</button>
        </div>
        <div class="explorer-content">
          <div class="file-grid">
            <div class="file-item" data-file="student.php">
              <div class="file-icon">
                <i class="fas fa-users"></i>
              </div>
              <div class="file-name">Students</div>
            </div>
            <div class="file-item" data-file="courses.php">
              <div class="file-icon">
                <i class="fas fa-book"></i>
              </div>
              <div class="file-name">Courses</div>
            </div>
            <div class="file-item" data-file="branch.php">
              <div class="file-icon">
                <i class="fas fa-code-branch"></i>
              </div>
              <div class="file-name">Branches</div>
            </div>
            <div class="file-item" data-file="college.php">
              <div class="file-icon">
                <i class="fas fa-university"></i>
              </div>
              <div class="file-name">Colleges</div>
            </div>
            <div class="file-item" data-file="result.php">
              <div class="file-icon">
                <i class="fas fa-chart-bar"></i>
              </div>
              <div class="file-name">Results</div>
            </div>
            <div class="file-item" data-file="reports.php">
              <div class="file-icon">
                <i class="fas fa-file-alt"></i>
              </div>
              <div class="file-name">Reports</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Iframe Container -->
      <div class="iframe-container">
        <div class="iframe-controls">
          <button id="refresh-frame" title="Refresh">
            <i class="fas fa-sync-alt"></i>
          </button>
          <button id="fullscreen-frame" title="Fullscreen">
            <i class="fas fa-expand"></i>
          </button>
        </div>
        <iframe id="content-frame" src="student.php"></iframe>
      </div>
    </div>

    <!-- Mobile Toggle Button -->
    <div class="mobile-toggle" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </div>

    <!-- JavaScript Section -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function () {
        // Update date and time
        function updateDateTime() {
          const now = new Date();
          const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
          };
          const dateTimeString = now.toLocaleString('en-US', options);
          $('#current-datetime').text(dateTimeString);
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Menu click event to load iframe content
        $('.menu-item, .stat-card, .file-item').on('click', function (e) {
          e.preventDefault();
          const file = $(this).data('file');
          let pageName = $(this).find('span').text() || $(this).find('.file-name').text();
          
          loadFileInIframe(file, pageName);

          // Highlight active link
          $('.menu-item').removeClass('active');
          $(`.menu-item[data-file="${file}"]`).addClass('active');
          
          // Close sidebar on mobile after selection
          if ($(window).width() < 992) {
            $('#sidebar').removeClass('open');
          }
        });

        // Function to load file in iframe
        function loadFileInIframe(file, pageName) {
          // Show loading state
          $('#content-frame').css('opacity', '0.7');
          
          // Set iframe source
          $('#content-frame').attr('src', file);
          
          // Update page title and breadcrumb
          $('.content-header h1').text(pageName);
          $('.breadcrumb span').text(pageName);
          
          // Update document title
          document.title = `Solitaire Infosys - ${pageName}`;
        }

        // Refresh iframe
        $('#refresh-frame').on('click', function() {
          const currentSrc = $('#content-frame').attr('src');
          $('#content-frame').attr('src', 'about:blank');
          setTimeout(function() {
            $('#content-frame').attr('src', currentSrc);
          }, 100);
        });

        // Toggle fullscreen for iframe
        $('#fullscreen-frame').on('click', function() {
          const iframe = $('#content-frame')[0];
          
          if (!document.fullscreenElement) {
            if (iframe.requestFullscreen) {
              iframe.requestFullscreen();
            } else if (iframe.webkitRequestFullscreen) {
              iframe.webkitRequestFullscreen();
            } else if (iframe.msRequestFullscreen) {
              iframe.msRequestFullscreen();
            }
          } else {
            if (document.exitFullscreen) {
              document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
              document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
              document.msExitFullscreen();
            }
          }
        });

        // Search functionality
        $('#search-box').on('input', function () {
          const query = $(this).val().toLowerCase();
          if (query.length > 0) {
            // Show suggestions dynamically by filtering menu items
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
            
            if (suggestions.length > 0) {
              suggestions.forEach(function (suggestion) {
                suggestionsBox.append(`
                  <div data-file="${suggestion.file}">
                    <i class="fas fa-${suggestion.icon}"></i>
                    ${suggestion.name}
                  </div>
                `);
              });
            } else {
              suggestionsBox.append('<div class="no-results">No results found</div>');
            }
            
            suggestionsBox.addClass('open');
          } else {
            $('#suggestions').removeClass('open');
          }
        });

        // Click on suggestion
        $(document).on('click', '.suggestions div', function () {
          if ($(this).hasClass('no-results')) return;
          
          const selectedText = $(this).text();
          const file = $(this).data('file');
          $('#search-box').val(selectedText);
          $('#suggestions').removeClass('open');

          // Load the selected content in iframe
          loadFileInIframe(file, selectedText.trim());

          // Highlight the selected menu item
          $('.menu-item').removeClass('active');
          $(`a[data-file="${file}"]`).addClass('active');
        });

        // Click outside to close suggestions
        $(document).on('click', function (e) {
          if (!$(e.target).closest('.search-container').length) {
            $('#suggestions').removeClass('open');
          }
        });

        // Admin profile dropdown
        $('#admin-profile').on('click', function () {
          // Toggle notification panel
          $('#notification-panel').toggleClass('open');
        });

        // Search icon click to expand on mobile
        $('#search-icon').on('click', function () {
          if ($(window).width() < 768) {
            $('#search-container').toggleClass('expanded');
            if ($('#search-container').hasClass('expanded')) {
              $('#search-box').focus();
            }
          }
        });
      });

      // Toggle Sidebar
      function toggleSidebar() {
        $('#sidebar').toggleClass('open');
      }

      // Close sidebar when clicking outside on mobile
      $(document).on('click', function (e) {
        if ($(window).width() < 992) {
          if (!$(e.target).closest('#sidebar').length && 
              !$(e.target).closest('.mobile-toggle').length &&
              $('#sidebar').hasClass('open')) {
            $('#sidebar').removeClass('open');
          }
        }
      });

      // Iframe load event
      $('#content-frame').on('load', function () {
        $(this).css('opacity', 1);
      });

      // Keyboard shortcuts
      $(document).keydown(function(e) {
        // Ctrl/Cmd + K for search
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 75) {
          e.preventDefault();
          $('#search-box').focus();
        }
        
        // Esc to close panels
        if (e.keyCode === 27) {
          $('#suggestions').removeClass('open');
          $('#notification-panel').removeClass('open');
          if ($(window).width() < 992) {
            $('#sidebar').removeClass('open');
          }
        }
      });

      // Responsive iframe height adjustment
      function adjustIframeHeight() {
        const headerHeight = $('.content-header').outerHeight() + parseInt($('.content-wrapper').css('padding-top'));
        const statsHeight = $('.stats-container').outerHeight() || 0;
        const explorerHeight = $('.file-explorer').outerHeight() || 0;
        const padding = 30;
        
        const iframeHeight = window.innerHeight - headerHeight - statsHeight - explorerHeight - padding;
        $('.iframe-container').css('height', iframeHeight + 'px');
      }

      $(window).resize(adjustIframeHeight);
      setTimeout(adjustIframeHeight, 100);
    </script>

  </body>

  </html>