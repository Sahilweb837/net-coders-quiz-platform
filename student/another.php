<?php
session_start();

$servername = "localhost";
$username = "campusedge_quiz";
$password = "MLOno(DK?WKa!+pR";
$dbname = "campusedge_quiz";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user has already selected a test
$testSelected = isset($_SESSION['selected_test']);

// Get only published results
$query = "SELECT 
            qr.id,
            s.name AS student_name,
            q.title AS quiz_title,
            qr.result
          FROM quiz_responses qr
          JOIN students s ON qr.student_id = s.id
          JOIN quizzes q ON qr.quiz_id = q.id
          WHERE qr.published = 1
          ORDER BY qr.id DESC";

$results = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Technical Assessment Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3a0ca3;
      --accent-color: #4895ef;
      --light-color: #f8f9fa;
      --dark-color: #212529;
      --success-color: #4cc9f0;
      --warning-color: #f8961e;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #e2e8f0 100%);
      min-height: 100vh;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .main-container {
      max-width: 800px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
      padding: 2.5rem;
      margin: 3rem auto;
      position: relative;
      overflow: hidden;
    }
    
    .main-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    }
    
    h3 {
      color: var(--primary-color);
      font-weight: 700;
      margin-bottom: 1.5rem;
      text-align: center;
      position: relative;
      padding-bottom: 0.5rem;
    }
    
    h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: var(--accent-color);
      border-radius: 2px;
    }
    
    .terms-box {
      background: #f8f9fa;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      border-left: 4px solid var(--accent-color);
    }
    
    .terms-title {
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 0.75rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .terms-text {
      font-size: 0.9rem;
      color: #495057;
      line-height: 1.6;
    }
    
    .test-btn-container {
      text-align: center;
      margin: 2rem 0;
      position: relative;
    }
    
    .test-btn {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      border: none;
      border-radius: 12px;
      padding: 1rem 2rem;
      font-weight: 600;
      font-size: 1.1rem;
      letter-spacing: 0.5px;
      color: white;
      box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      overflow: hidden;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 220px;
    }
    
    .test-btn:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 12px 25px rgba(67, 97, 238, 0.4);
    }
    
    .test-btn:active {
      transform: translateY(1px);
    }
    
    .test-btn::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(
        to bottom right,
        rgba(255, 255, 255, 0.3) 0%,
        rgba(255, 255, 255, 0) 60%
      );
      transform: rotate(30deg);
      transition: all 0.3s ease;
    }
    
    .test-btn:hover::after {
      left: 100%;
      top: 100%;
    }
    
    .btn-icon {
      margin-right: 10px;
      font-size: 1.2rem;
    }
    
    .result-item {
      background: white;
      border-radius: 12px;
      padding: 1.25rem 1.5rem;
      margin-bottom: 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      border-left: 4px solid var(--accent-color);
      transition: all 0.3s ease;
    }
    
    .result-item:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .student-name {
      font-weight: 600;
      color: var(--dark-color);
      margin-bottom: 0.25rem;
    }
    
    .quiz-title {
      font-size: 0.85rem;
      color: #6c757d;
      margin-bottom: 0.5rem;
    }
    
    .result-percent {
      font-weight: 700;
      color: var(--primary-color);
    }
    
    .no-results {
      text-align: center;
      padding: 2rem;
      background: #f8f9fa;
      border-radius: 12px;
    }
    
    .loading-spinner {
      width: 50px;
      height: 50px;
      border: 5px solid rgba(67, 97, 238, 0.1);
      border-radius: 50%;
      border-top-color: var(--primary-color);
      animation: spin 1s linear infinite;
      margin: 0 auto 1.5rem;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    
    .pulse {
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    
    .test-status {
      padding: 1.5rem;
      background-color: rgba(248, 249, 250, 0.8);
      border-radius: 12px;
      text-align: center;
      margin-bottom: 1.5rem;
      border-left: 4px solid var(--warning-color);
    }
    
    .status-icon {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: var(--warning-color);
    }
    
    .progress-container {
      height: 6px;
      background: #e9ecef;
      border-radius: 3px;
      margin-top: 1.5rem;
      overflow: hidden;
    }
    
    .progress-bar {
      height: 100%;
      background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
      border-radius: 3px;
      width: 0%;
      transition: width 1s ease;
    }
    
    .btn-loading .btn-text {
      visibility: hidden;
      opacity: 0;
    }
    
    .btn-loading::after {
      content: "";
      position: absolute;
      width: 20px;
      height: 20px;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      margin: auto;
      border: 3px solid transparent;
      border-top-color: #ffffff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
  </style>
</head>
<body>
  <div class="main-container animate__animated animate__fadeIn">
    <h3>Technical Assessment Portal</h3>
    
    <div class="terms-box animate__animated animate__fadeIn">
      <h5 class="terms-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
          <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
          <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
        </svg>
        Assessment Terms & Conditions
      </h5>
      <div class="terms-text">
        <p>By proceeding with this technical assessment, you agree to the following:</p>
        <ul>
          <li>This test must be completed independently without assistance</li>
          <li>All answers will be recorded and analyzed</li>
          <li>The time taken to complete the assessment may be factored into evaluation</li>
          <li>Results will be shared with the hiring team</li>
        </ul>
      </div>
    </div>
    
    <?php if ($testSelected): ?>
      <div class="test-status animate__animated animate__fadeIn">
        <div class="status-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
          </svg>
        </div>
        <h5>Test in Progress</h5>
        <p>You have already selected a test. Please complete your current assessment before starting a new one.</p>
        <div class="progress-container">
          <div class="progress-bar" id="progressBar"></div>
        </div>
      </div>
    <?php elseif ($results->num_rows > 0): ?>
      <?php $row = $results->fetch_assoc(); ?>
      <div class="result-item animate__animated animate__fadeInUp">
        <h5 class="student-name">Candidate: <?= htmlspecialchars($row['student_name']) ?></h5>
        <p class="quiz-title">Assessment: <?= htmlspecialchars($row['quiz_title']) ?></p>
        <p>Score: <span class="result-percent"><?= htmlspecialchars($row['result']) ?>%</span></p>
      </div>
      
      <div class="test-btn-container">
        <a href="tech.php?id=<?= $row['id'] ?>" class="test-btn animate__animated animate__pulse animate__infinite" id="startTestBtn">
          <span class="btn-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
              <path d="M6 12v-2h3v2H6z"/>
              <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M6 4v-.5a.5.5 0 0 1 1 0V4h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 2v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 2v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
            </svg>
          </span>
          <span class="btn-text">BEGIN TECHNICAL TEST</span>
        </a>
      </div>
    <?php else: ?>
      <div class="no-results animate__animated animate__fadeIn">
        <div class="loading-spinner"></div>
        <h5>Assessment Not Available</h5>
        <p class="text-muted">Your technical assessment has been loading  wait .</p>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Animate progress bar
    if (document.getElementById('progressBar')) {
      let progress = 0;
      const progressBar = document.getElementById('progressBar');
      const interval = setInterval(() => {
        progress += Math.random() * 10;
        if (progress >= 100) {
          progress = 100;
          clearInterval(interval);
        }
        progressBar.style.width = progress + '%';
      }, 500);
    }

    // Add loading state to button
    document.getElementById('startTestBtn')?.addEventListener('click', function(e) {
      this.classList.add('btn-loading');
      
      // Simulate loading delay (remove in production)
      setTimeout(() => {
        this.classList.remove('btn-loading');
      }, 3000);
    });
  </script>
</body>
</html>