 <?php
include('../db.php');
session_start();

// Check if the student is logged in - FIXED: Redirect to main index.php (registration) if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: /index.php"); // back to registration
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student name
$student_sql = "SELECT name FROM students WHERE id = ?";
$student_stmt = $conn->prepare($student_sql);
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student_name = "Student";
if ($student_result->num_rows > 0) {
    $student_data = $student_result->fetch_assoc();
    $student_name = $student_data['name'];
}

// Fetch only 2 initial tests
$quizzes_sql = "SELECT q.id, q.title, q.description FROM quizzes q WHERE q.status = 'published' ORDER BY q.title LIMIT 2";
$quizzes_stmt = $conn->prepare($quizzes_sql);
$quizzes_stmt->execute();
$quizzes_result = $quizzes_stmt->get_result();

// Fetch completed quiz IDs for this student
$completed_sql = "SELECT quiz_id FROM quiz_responses WHERE student_id = ?";
$completed_stmt = $conn->prepare($completed_sql);
$completed_stmt->bind_param("i", $student_id);
$completed_stmt->execute();
$completed_result = $completed_stmt->get_result();
$completed_quizzes = [];
while ($row = $completed_result->fetch_assoc()) {
    $completed_quizzes[] = $row['quiz_id'];
}

// Check if both initial tests are completed
$all_completed = false;
if ($quizzes_result->num_rows == 2) {
    $quizzes_result->data_seek(0); // Reset pointer
    $quiz1 = $quizzes_result->fetch_assoc();
    $quiz2 = $quizzes_result->fetch_assoc();
    $all_completed = in_array($quiz1['id'], $completed_quizzes) && in_array($quiz2['id'], $completed_quizzes);
    
    // Reset pointer again for display
    $quizzes_result->data_seek(0);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Quizzes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding-top: 80px;
            color: var(--light-color);
            overflow-x: hidden;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: var(--dark-color);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }
        
        nav img {
            height: 40px;
        }
        
        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        
        .nav-links a {
            color: var(--light-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-links a:hover {
            color: var(--accent-color);
        }
        
        .welcome-message {
            color: var(--light-color);
            font-weight: 500;
            margin-right: 15px;
        }
        
        h1 {
            margin: 20px 0 30px;
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .test-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            width: 100%;
            padding: 20px;
        }
        
        .test-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .test-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }
        
        .test-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--accent-color);
        }
        
        .test-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: white;
            text-align: center;
        }
        
        .test-card p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 25px;
            text-align: center;
            line-height: 1.5;
        }
        
        .btn-start {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
            color: white;
        }
        
        .completed-badge {
            background-color: rgba(76, 201, 240, 0.2);
            color: var(--success-color);
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .loading-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            display: none;
        }
        
        .loading-spinner {
            width: 70px;
            height: 70px;
            border: 8px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: var(--accent-color);
            animation: spin 1.5s linear infinite;
            margin-bottom: 25px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .loading-text {
            color: white;
            font-size: 1.5rem;
            margin-top: 15px;
            text-align: center;
        }
        
        .progress-container {
            width: 60%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            height: 10px;
            margin-top: 30px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            transition: width 0.4s ease;
        }
        
        .particle {
            position: absolute;
            background: rgba(67, 97, 238, 0.6);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        
        .more-tests-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 9998;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            display: none;
        }
        
        .more-tests-content {
            text-align: center;
            max-width: 500px;
            padding: 30px;
        }
        
        .more-tests-content h2 {
            color: var(--accent-color);
            margin-bottom: 20px;
            font-size: 2rem;
        }
        
        .more-tests-content p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .test-container {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .test-card {
                padding: 20px;
            }
            
            .nav-links {
                gap: 15px;
            }
            
            .welcome-message {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Particles Background -->
    <div id="particles-js"></div>
    
    <nav>
        <img src="https://solitaireinfosystems.com/wp-content/uploads/2023/07/slinfi-logo.png" alt="Logo">
        <div class="nav-links">
            <span class="welcome-message">Welcome, <?= htmlspecialchars($student_name) ?></span>
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1>Available Tests</h1>
        
        <div class="test-container">
            <?php if ($quizzes_result->num_rows > 0): ?>
                <?php while ($quiz = $quizzes_result->fetch_assoc()): ?>
                    <?php $is_completed = in_array($quiz['id'], $completed_quizzes); ?>
                    
                    <div class="test-card <?= $is_completed ? 'completed' : '' ?>">
                        <i class="fas fa-brain test-icon"></i>
                        <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                        <p><?= htmlspecialchars($quiz['description']) ?></p>
                        
                        <?php if ($is_completed): ?>
                            <span class="completed-badge"><i class="fas fa-check-circle"></i> Completed</span>
                        <?php else: ?>
                            <a href="take_quiz.php?quiz_id=<?= $quiz['id'] ?>" class="btn-start" onclick="showLoading()">
                                <i class="fas fa-play"></i> Start Test
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                
                <?php if ($all_completed): ?>
                    <div class="test-card" id="moreTestsCard" onclick="loadMoreTests()">
                        <i class="fas fa-sync-alt test-icon"></i>
                        <h3>More Tests Available</h3>
                        <p>Click here to load additional tests</p>
                        <button class="btn-start">
                            <i class="fas fa-download"></i> Load Tests
                        </button>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info w-100 text-center">
                    No tests are available at the moment. Please check back later.
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Loading Animation for Test Start -->
    <div class="loading-container" id="loadingContainer">
        <div class="loading-spinner"></div>
        <div class="loading-text">Preparing your test environment...</div>
        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>
    </div>
    
    <!-- Loading Animation for More Tests -->
    <div class="more-tests-loading" id="moreTestsLoading">
        <div class="more-tests-content">
            <h2><i class="fas fa-cog fa-spin"></i> Loading More Tests</h2>
            <p>We're preparing additional assessments for you. Please wait while we load the next set of tests.</p>
            <div class="loading-spinner"></div>
            <div class="progress-container">
                <div class="progress-bar" id="moreTestsProgress"></div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <script>
        // Initialize particles.js
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS("particles-js", {
                "particles": {
                    "number": {
                        "value": 80,
                        "density": {
                            "enable": true,
                            "value_area": 800
                        }
                    },
                    "color": {
                        "value": "#4361ee"
                    },
                    "shape": {
                        "type": "circle",
                        "stroke": {
                            "width": 0,
                            "color": "#000000"
                        },
                        "polygon": {
                            "nb_sides": 5
                        }
                    },
                    "opacity": {
                        "value": 0.5,
                        "random": false,
                        "anim": {
                            "enable": false,
                            "speed": 1,
                            "opacity_min": 0.1,
                            "sync": false
                        }
                    },
                    "size": {
                        "value": 3,
                        "random": true,
                        "anim": {
                            "enable": false,
                            "speed": 40,
                            "size_min": 0.1,
                            "sync": false
                        }
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": "#3a56e8",
                        "opacity": 0.4,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 2,
                        "direction": "none",
                        "random": false,
                        "straight": false,
                        "out_mode": "out",
                        "bounce": false,
                        "attract": {
                            "enable": false,
                            "rotateX": 600,
                            "rotateY": 1200
                        }
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {
                        "onhover": {
                            "enable": true,
                            "mode": "grab"
                        },
                        "onclick": {
                            "enable": true,
                            "mode": "push"
                        },
                        "resize": true
                    },
                    "modes": {
                        "grab": {
                            "distance": 140,
                            "line_linked": {
                                "opacity": 1
                            }
                        },
                        "bubble": {
                            "distance": 400,
                            "size": 40,
                            "duration": 2,
                            "opacity": 8,
                            "speed": 3
                        },
                        "repulse": {
                            "distance": 200,
                            "duration": 0.4
                        },
                        "push": {
                            "particles_nb": 4
                        },
                        "remove": {
                            "particles_nb": 2
                        }
                    }
                },
                "retina_detect": true
            });
        });
        
        function showLoading() {
            const loadingContainer = document.getElementById('loadingContainer');
            const progressBar = document.getElementById('progressBar');
            
            loadingContainer.style.display = 'flex';
            
            let width = 0;
            const interval = setInterval(() => {
                if (width >= 100) {
                    clearInterval(interval);
                } else {
                    width += 5;
                    progressBar.style.width = width + '%';
                }
            }, 50);
        }
        
        function loadMoreTests() {
            const loadingContainer = document.getElementById('moreTestsLoading');
            const progressBar = document.getElementById('moreTestsProgress');
            
            loadingContainer.style.display = 'flex';
            
            let width = 0;
            const interval = setInterval(() => {
                if (width >= 100) {
                    clearInterval(interval);
                    // Simulate loading completion
                    setTimeout(() => {
                        loadingContainer.innerHTML = `
                            <div class="more-tests-content">
                                <h2><i class="fas fa-check-circle" style="color:#4cc9f0"></i> Tests Ready</h2>
                                <p>New tests have been loaded successfully. You will be redirected shortly.</p>
                                <div class="progress-container">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                            </div>
                        `;
                        // Redirect to a new page or reload with more tests
                        setTimeout(() => {
                            window.location.href = 'another.php';
                        }, 1500);
                    }, 1000);
                } else {
                    width += 2;
                    progressBar.style.width = width + '%';
                }
            }, 50);
        }
        
        // Hide loading if user comes back
        window.addEventListener('pageshow', function() {
            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('moreTestsLoading').style.display = 'none';
        });
    </script>
</body>

</html>