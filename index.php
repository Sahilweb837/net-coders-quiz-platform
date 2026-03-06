    <?php
    // Include the database connection from the correct path
    include('db.php');

    session_start();

    // Check if user is already logged in
    if (isset($_SESSION['student_id'])) {
        echo "<script>location.replace('student/index.php');</script>";
        exit();
    }

    // Check if connection exists
    if (!isset($conn) || $conn->connect_error) {
        die("Database connection failed. Please check your database configuration.");
    }

    // Fetch college names from the database
    $colleges = [];
    $college_query = "SELECT id, college_name FROM updated_college WHERE published = 1";
    $college_result = $conn->query($college_query);
    if ($college_result && $college_result->num_rows > 0) {
        while ($row = $college_result->fetch_assoc()) {
            $colleges[] = $row;
        }
    }

    // Fetch active sessions from the database
    $active_sessions = [];
    $session_query = "SELECT id, session_name FROM current_session WHERE status = 1";
    $session_result = $conn->query($session_query);
    if ($session_result && $session_result->num_rows > 0) {
        while ($row = $session_result->fetch_assoc()) {
            $active_sessions[] = $row;
        }
    }

    // Fetch course names from the database
    $courses = [];
    $course_query = "SELECT id, course_name FROM new_courses WHERE published = 1";
    $course_result = $conn->query($course_query);
    if ($course_result && $course_result->num_rows > 0) {
        while ($row = $course_result->fetch_assoc()) {
            $courses[] = $row;
        }
    }

    // Fetch branch names from the database
    $branches = [];
    $branch_query = "SELECT id, name FROM branches WHERE status = 1";
    $branch_result = $conn->query($branch_query);
    if ($branch_result && $branch_result->num_rows > 0) {
        while ($row = $branch_result->fetch_assoc()) {
            $branches[] = $row;
        }
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect form data
        $name = $_POST['name'];
        $college = $_POST['college'];
        $course = $_POST['course'];
        $semester = $_POST['semester'];
        $branch = $_POST['branch'];
        $whatsapp = $_POST['whatsapp'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $session = $_POST['session'];

        // Check if the mobile number or email already exists in the database
        $check_sql = "SELECT * FROM students WHERE contact = ? OR email = ?";
        $stmt_check = $conn->prepare($check_sql);
        if ($stmt_check) {
            $stmt_check->bind_param("ss", $contact, $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                // If a record with the same mobile number or email exists, show an error
                $error_message = "The mobile number or email is already registered. Please use a different one.";
            } else {
                // Prepare SQL query
                $insert_sql = "INSERT INTO students (name, college, course, semester, branch, contact, email, whatsapp, session) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);

                if ($stmt) {
                    $stmt->bind_param("sssssssss", $name, $college, $course, $semester, $branch, $contact, $email, $whatsapp, $session);

                    if ($stmt->execute()) {
                        // Get the inserted student ID
                        $student_id = $stmt->insert_id;
                        
                        // Set session variables
                        $_SESSION['student_id'] = $student_id;
                        $_SESSION['student_name'] = $name;
                        $_SESSION['student_email'] = $email;
                        
                        // Registration successful - redirect to student page
                        echo "<script>location.replace('student/index.php');</script>";
                        exit();
                    } else {
                        $error_message = "Error: " . $conn->error;
                    }
                } else {
                    $error_message = "Database error: Unable to prepare insert statement.";
                }
            }
        } else {
            $error_message = "Database error: Unable to prepare check statement.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NetCoders - Student Registration</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- GSAP Library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
        <link rel='shortcut icon' href='https://netcoder.in/wp-content/uploads/2023/04/favicon.png'/>
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            :root {
                --orange: #ff5532;
                --orange-dark: #e03e1f;
                --orange-light: #ffeeea;
                --orange-glow: rgba(255, 85, 50, 0.3);
                --orange-glow-intense: rgba(255, 85, 50, 0.5);
                --dark-bg: #0b1120;
                --dark-card: #151f2f;
                --text-primary: #ffffff;
                --text-secondary: #e2e8f0;
                --text-muted: #94a3b8;
                --border-color: #1e2a3a;
                --success: #10b981;
                --success-bg: rgba(16, 185, 129, 0.1);
                --header-bg: #ffffff;
                --header-text: #1e293b;
                --glass-bg: rgba(21, 31, 47, 0.85);
                --glass-border: rgba(255, 85, 50, 0.2);
            }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: var(--dark-bg);
                color: var(--text-primary);
                min-height: 100vh;
                position: relative;
                overflow-x: hidden;
                margin: 0;
                padding: 0;
            }

            /* Simple Fast Animated Background */
            .bg-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -2;
                overflow: hidden;
            }

            /* Fast Gradient Animation */
            .bg-gradient {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: radial-gradient(circle at 30% 40%, rgba(255, 85, 50, 0.12) 0%, transparent 40%),
                            radial-gradient(circle at 70% 60%, rgba(255, 85, 50, 0.08) 0%, transparent 50%);
                animation: fastGradient 8s ease-in-out infinite alternate;
            }

            @keyframes fastGradient {
                0% { transform: scale(1); opacity: 0.6; }
                100% { transform: scale(1.1); opacity: 1; }
            }

            /* Simple Floating Boxes */
            .boxes-layer {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -1;
            }

            .box {
                position: absolute;
                background: rgba(255, 85, 50, 0.03);
                border: 1px solid rgba(255, 85, 50, 0.1);
                border-radius: 10px;
                animation: fastFloat 6s infinite ease-in-out;
            }

            .box-1 { width: 100px; height: 100px; top: 10%; left: 5%; animation-delay: 0s; }
            .box-2 { width: 150px; height: 150px; top: 5%; right: 8%; animation-delay: 1s; }
            .box-3 { width: 120px; height: 120px; top: 40%; left: 10%; animation-delay: 2s; }
            .box-4 { width: 180px; height: 180px; top: 45%; right: 5%; animation-delay: 0.5s; }
            .box-5 { width: 200px; height: 200px; bottom: 10%; left: 15%; animation-delay: 1.5s; }
            .box-6 { width: 130px; height: 130px; bottom: 15%; right: 10%; animation-delay: 2.5s; }
            .box-7 { width: 160px; height: 160px; top: 25%; left: 30%; animation-delay: 0.8s; }
            .box-8 { width: 140px; height: 140px; top: 60%; right: 25%; animation-delay: 1.8s; }

            @keyframes fastFloat {
                0% { transform: translateY(0) rotate(0deg); opacity: 0.3; }
                50% { transform: translateY(-15px) rotate(3deg); opacity: 0.5; }
                100% { transform: translateY(0) rotate(0deg); opacity: 0.3; }
            }

            /* Simple Particles */
            .particles-layer {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -1;
            }

            .particle {
                position: absolute;
                background: rgba(255, 85, 50, 0.2);
                border-radius: 50%;
                animation: fastParticle 8s infinite ease-in-out;
            }

            .particle-1 { width: 4px; height: 4px; top: 15%; left: 20%; animation-duration: 5s; animation-delay: 0s; }
            .particle-2 { width: 6px; height: 6px; top: 35%; left: 55%; animation-duration: 7s; animation-delay: 1s; }
            .particle-3 { width: 3px; height: 3px; top: 65%; left: 75%; animation-duration: 6s; animation-delay: 2s; }
            .particle-4 { width: 5px; height: 5px; top: 80%; left: 30%; animation-duration: 8s; animation-delay: 0.5s; }
            .particle-5 { width: 7px; height: 7px; top: 45%; left: 85%; animation-duration: 5.5s; animation-delay: 1.5s; }
            .particle-6 { width: 4px; height: 4px; top: 70%; left: 45%; animation-duration: 7.5s; animation-delay: 2.5s; }
            .particle-7 { width: 6px; height: 6px; top: 25%; left: 40%; animation-duration: 6.5s; animation-delay: 0.2s; }
            .particle-8 { width: 5px; height: 5px; top: 55%; left: 15%; animation-duration: 8.5s; animation-delay: 1.2s; }
            .particle-9 { width: 4px; height: 4px; top: 85%; left: 65%; animation-duration: 5.8s; animation-delay: 2.2s; }
            .particle-10 { width: 6px; height: 6px; top: 20%; left: 70%; animation-duration: 7.2s; animation-delay: 0.8s; }
            .particle-11 { width: 5px; height: 5px; top: 50%; left: 50%; animation-duration: 6.2s; animation-delay: 1.8s; }
            .particle-12 { width: 7px; height: 7px; top: 90%; left: 80%; animation-duration: 8.2s; animation-delay: 2.8s; }

            @keyframes fastParticle {
                0% { transform: translate(0, 0) scale(1); opacity: 0.2; }
                50% { transform: translate(-20px, 15px) scale(1.2); opacity: 0.5; }
                100% { transform: translate(0, 0) scale(1); opacity: 0.2; }
            }

            /* Fast Grid Animation */
            .grid-layer {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: 
                    linear-gradient(rgba(255, 85, 50, 0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 85, 50, 0.03) 1px, transparent 1px);
                background-size: 60px 60px;
                z-index: -1;
                animation: fastGrid 12s linear infinite;
            }

            @keyframes fastGrid {
                0% { transform: translateX(0) translateY(0); }
                50% { transform: translateX(10px) translateY(8px); }
                100% { transform: translateX(0) translateY(0); }
            }

            /* Loading Screen */
            .loading-screen {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, var(--dark-bg) 0%, #1a1f2e 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }

            .loading-logo-container {
                text-align: center;
                opacity: 0;
                transform: scale(0.5);
            }

            .loading-logo {
                max-width: 400px;
                width: 90%;
                height: auto;
                background-color:white;
                margin-bottom: 20px;
                filter: drop-shadow(0 0 30px var(--orange-glow));
            }

            .loading-text {
                color: white;
                font-size: 18px;
                letter-spacing: 8px;
                text-transform: uppercase;
                margin-top: 20px;
                opacity: 0;
                transform: translateY(20px);
            }

            .loading-bar-container {
                width: 300px;
                height: 4px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 4px;
                margin: 30px auto 0;
                overflow: hidden;
            }

            .loading-bar {
                width: 0%;
                height: 100%;
                background: linear-gradient(90deg, var(--orange), var(--orange-dark));
                border-radius: 4px;
            }

            /* Main Content - Initially Hidden */
            .main-content-wrapper {
                opacity: 0;
                visibility: hidden;
                position: relative;
                z-index: 1;
                padding: 20px;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Main Container */
            .registration-wrapper {
                max-width: 1400px;
                width: 100%;
                background: var(--glass-bg);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-radius: 32px;
                box-shadow: 0 25px 60px -15px rgba(255, 85, 50, 0.3);
                overflow: hidden;
                border: 1px solid var(--glass-border);
                margin: 0 auto;
                position: relative;
                z-index: 10;
            }

            /* Header with Logo */
            .header {
                background: white;
                padding: 35px 30px 25px;
                border-bottom: 3px solid var(--orange);
                text-align: center;
            }

            .logo-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 15px;
            }

            .logo {
                max-width: 280px;
                width: 100%;
                height: auto;
                transition: transform 0.3s ease;
                filter: drop-shadow(0 8px 16px var(--orange-glow));
            }

            .logo:hover {
                transform: scale(1.02);
            }

            .header h1 {
                color: #1e293b;
                font-size: 28px;
                font-weight: 700;
                margin: 10px 0 5px;
                letter-spacing: -0.02em;
            }

            .header .sub-heading {
                color: var(--orange);
                font-size: 18px;
                font-weight: 500;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }

            .header .sub-heading i {
                background: var(--orange);
                color: white;
                padding: 6px;
                border-radius: 50%;
                font-size: 12px;
            }

            /* Main Content Flex */
            .main-content {
                display: flex;
                background: transparent;
            }

            /* Form Section */
            .form-section {
                flex: 2.2;
                padding: 40px 45px;
                background: transparent;
            }

            .section-title {
                color: white;
                font-size: 20px;
                font-weight: 700;
                margin: 30px 0 20px;
                padding-bottom: 10px;
                border-bottom: 3px solid var(--orange);
                display: flex;
                align-items: center;
                gap: 12px;
                letter-spacing: -0.3px;
            }

            .section-title i {
                color: var(--orange);
                font-size: 24px;
                width: 36px;
                height: 36px;
                background: rgba(255, 85, 50, 0.15);
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 8px;
            }

            .form-group label {
                display: block;
                color: var(--text-secondary);
                font-size: 15px;
                font-weight: 600;
                margin-bottom: 8px;
            }

            .form-group label span {
                color: var(--orange);
                font-size: 16px;
            }

            .form-control {
                width: 100%;
                padding: 14px 18px;
                border: 2px solid var(--glass-border);
                border-radius: 16px;
                font-size: 16px;
                color: white;
                transition: all 0.2s ease;
                background: rgba(255, 255, 255, 0.05);
                font-weight: 450;
                backdrop-filter: blur(5px);
            }

            .form-control:focus {
                border-color: var(--orange);
                outline: none;
                background: rgba(255, 85, 50, 0.1);
                box-shadow: 0 0 0 4px var(--orange-glow);
                transform: translateY(-1px);
            }

            .form-control::placeholder {
                color: var(--text-muted);
                font-weight: 400;
            }

            select.form-control {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%23ff5532' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 18px center;
                background-size: 18px;
            }

            .input-group {
                display: flex;
                align-items: center;
                border: 2px solid var(--glass-border);
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.05);
                transition: all 0.2s ease;
            }

            .input-group:focus-within {
                border-color: var(--orange);
                box-shadow: 0 0 0 4px var(--orange-glow);
                background: rgba(255, 85, 50, 0.1);
            }

            .input-group-text {
                padding: 0 0 0 18px;
                color: var(--orange);
                background: transparent;
                border: none;
                font-size: 18px;
            }

            .input-group .form-control {
                border: none;
                background: transparent;
                padding-left: 8px;
                box-shadow: none;
            }

            /* Session field styling */
            .session-field {
                background: rgba(255, 85, 50, 0.1);
                padding: 14px 20px;
                border-radius: 16px;
                color: white;
                font-weight: 600;
                border: 2px solid var(--orange);
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .session-field i {
                color: var(--orange);
                font-size: 20px;
            }

            /* Right Panel - Features */
            .features-panel {
                flex: 1.2;
                background: linear-gradient(165deg, rgba(21, 31, 47, 0.95) 0%, rgba(11, 17, 32, 0.98) 100%);
                padding: 45px 35px;
                color: white;
                position: relative;
                overflow: hidden;
                backdrop-filter: blur(5px);
                border-left: 1px solid var(--glass-border);
            }

            .features-panel::before {
                content: '';
                position: absolute;
                top: -20%;
                right: -20%;
                width: 300px;
                height: 300px;
                background: radial-gradient(circle, rgba(255,85,50,0.15) 0%, transparent 70%);
                border-radius: 50%;
                pointer-events: none;
                animation: slowRotate 15s linear infinite;
            }

            @keyframes slowRotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .features-panel h3 {
                color: white;
                font-size: 28px;
                font-weight: 700;
                margin-bottom: 30px;
                padding-bottom: 18px;
                border-bottom: 4px solid var(--orange);
                display: flex;
                align-items: center;
                gap: 12px;
                letter-spacing: -0.5px;
                position: relative;
            }

            .features-panel h3 i {
                color: var(--orange);
                font-size: 30px;
            }

            .feature-grid {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .feature-item {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 18px 22px;
                background: rgba(255, 255, 255, 0.03);
                border-radius: 24px;
                backdrop-filter: blur(2px);
                border: 1px solid var(--glass-border);
                transition: all 0.2s ease;
                position: relative;
                z-index: 2;
            }

            .feature-item:hover {
                background: rgba(255, 85, 50, 0.15);
                transform: translateX(5px);
                border-color: var(--orange);
            }

            .feature-icon {
                width: 58px;
                height: 58px;
                background: var(--orange);
                border-radius: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 26px;
                box-shadow: 0 12px 20px -10px var(--orange-glow);
                transition: all 0.2s ease;
            }

            .feature-item:hover .feature-icon {
                background: white;
                color: var(--orange);
            }

            .feature-text h4 {
                color: white;
                font-size: 18px;
                font-weight: 700;
                margin-bottom: 5px;
            }

            .feature-text p {
                color: var(--text-secondary);
                font-size: 14px;
                margin: 0;
                line-height: 1.4;
            }

            .contact-card {
                margin-top: 40px;
                padding: 25px;
                background: rgba(255, 85, 50, 0.1);
                border-radius: 24px;
                border: 1px solid var(--orange);
                transition: all 0.2s ease;
            }

            .contact-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 25px -5px var(--orange-glow);
            }

            .contact-card h5 {
                color: var(--orange);
                font-size: 20px;
                margin-bottom: 15px;
            }

            .contact-card p {
                color: var(--text-secondary);
                font-size: 16px;
                margin: 8px 0;
            }

            .contact-card i {
                color: var(--orange);
                margin-right: 10px;
                width: 24px;
            }

            /* Register Button */
            .btn-register {
                background: linear-gradient(135deg, var(--orange), var(--orange-dark));
                color: white;
                border: none;
                padding: 18px 35px;
                font-size: 22px;
                font-weight: 700;
                border-radius: 40px;
                width: 100%;
                margin-top: 30px;
                cursor: pointer;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                letter-spacing: 0.5px;
                box-shadow: 0 15px 30px -8px var(--orange-glow);
                border: 1px solid rgba(255, 85, 50, 0.5);
                position: relative;
                z-index: 20;
            }

            .btn-register:hover {
                background: linear-gradient(135deg, var(--orange-dark), #cc3512);
                transform: scale(1.01) translateY(-2px);
                box-shadow: 0 20px 35px -8px var(--orange);
            }

            .btn-register i {
                font-size: 24px;
                transition: transform 0.2s ease;
            }

            .btn-register:hover i {
                transform: translateX(5px);
            }

            /* Alert */
            .alert {
                background: rgba(239, 68, 68, 0.1);
                border: 1px solid #ef4444;
                color: #fecaca;
                padding: 16px 20px;
                border-radius: 20px;
                margin-bottom: 25px;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 12px;
                animation: fastShake 0.3s ease-out;
            }

            @keyframes fastShake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }

            .alert i {
                font-size: 24px;
                color: #ef4444;
            }

            /* Terms */
            .terms-group {
                margin: 20px 0 0;
            }

            .terms-check {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .terms-check input[type="checkbox"] {
                width: 20px;
                height: 20px;
                accent-color: var(--orange);
                border-radius: 5px;
                cursor: pointer;
            }

            .terms-check label {
                color: var(--text-secondary);
                font-size: 16px;
            }

            .terms-check a {
                color: var(--orange);
                text-decoration: none;
                font-weight: 600;
            }

            .terms-check a:hover {
                color: white;
            }

            .error-message {
                color: #ef4444;
                font-size: 13px;
                margin-top: 6px;
                font-weight: 500;
            }

            hr {
                border: 1px solid var(--glass-border);
                margin: 25px 0 15px;
            }

            /* Mobile Optimizations */
            @media (max-width: 768px) {
                .box, .particle {
                    opacity: 0.1;
                }
                
                .grid-layer {
                    background-size: 40px 40px;
                }
            }

            /* Responsive */
            @media (max-width: 1000px) {
                .main-content {
                    flex-direction: column;
                }
                .form-row {
                    grid-template-columns: 1fr;
                }
                .logo {
                    max-width: 220px;
                }
                .features-panel {
                    border-left: none;
                    border-top: 1px solid var(--glass-border);
                }
            }

            @media (max-width: 480px) {
                .main-content-wrapper {
                    padding: 10px;
                }
                
                .registration-wrapper {
                    margin: 0;
                }
                
                .form-section {
                    padding: 20px;
                }
                
                .features-panel {
                    padding: 25px 20px;
                }
                
                .btn-register {
                    font-size: 18px;
                    padding: 15px 25px;
                }
                
                .header {
                    padding: 20px 15px;
                }
                
                .header h1 {
                    font-size: 22px;
                }
                
                .section-title {
                    font-size: 18px;
                    margin: 20px 0 15px;
                }
                
                .form-control {
                    padding: 12px 15px;
                    font-size: 14px;
                }
                
                .form-row {
                    gap: 15px;
                }
            }
        </style>
    </head>
    <body>
        <!-- Optimized Fast Animated Background -->
        <div class="bg-container">
            <div class="bg-gradient"></div>
            
            <div class="boxes-layer">
                <div class="box box-1"></div>
                <div class="box box-2"></div>
                <div class="box box-3"></div>
                <div class="box box-4"></div>
                <div class="box box-5"></div>
                <div class="box box-6"></div>
                <div class="box box-7"></div>
                <div class="box box-8"></div>
            </div>
            
            <div class="particles-layer">
                <div class="particle particle-1"></div>
                <div class="particle particle-2"></div>
                <div class="particle particle-3"></div>
                <div class="particle particle-4"></div>
                <div class="particle particle-5"></div>
                <div class="particle particle-6"></div>
                <div class="particle particle-7"></div>
                <div class="particle particle-8"></div>
                <div class="particle particle-9"></div>
                <div class="particle particle-10"></div>
                <div class="particle particle-11"></div>
                <div class="particle particle-12"></div>
            </div>
            
            <div class="grid-layer"></div>
        </div>

        <!-- Loading Screen with GSAP Animation -->
        <div class="loading-screen" id="loadingScreen">
            <div class="loading-logo-container" id="loadingLogo">
                <img src="./assests/logo.png" alt="NetCoders" class="loading-logo">
                <div class="loading-bar-container">
                    <div class="loading-bar" id="loadingBar"></div>
                </div>
                <div class="loading-text" id="loadingText">NETCODERS  TEST</div>
            </div>
        </div>

        <!-- Main Content Wrapper -->
        <div class="main-content-wrapper" id="mainContent">
            <div class="registration-wrapper">
                <!-- Header with Centered Logo -->
                <div class="header">
                    <div class="logo-container">
                <img src="./assests/logo.png" height="100px" alt="NetCoders" class="logo-img" >
                        <h1>Student Registration Portal</h1>
                        <div class="sub-heading">
                            <i class="fas fa-bolt"></i> 
                            <span>Fast-Track Your Tech Career</span>
                            <i class="fas fa-bolt"></i>
                        </div>
                    </div>
                </div>

                <!-- Main Content: Form + Attractive Features -->
                <div class="main-content">
                    <!-- Registration Form -->
                    <div class="form-section">
                        <?php if (isset($error_message)) { ?>
                            <div class="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo $error_message; ?>
                            </div>
                        <?php } ?>

                        <form id="registrationForm" method="POST" onsubmit="return validateForm()">
                            <!-- Personal Info -->
                            <div class="section-title">
                                <i class="fas fa-user-circle"></i> Personal Details
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Full Name <span>*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter your full name" required>
                                    <div class="error-message" id="name-error"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email Address <span>*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                                        <input type="email" class="form-control" name="email" id="email" placeholder="you@example.com" required>
                                    </div>
                                    <div class="error-message" id="email-error"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Phone Number <span>*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                        <input type="tel" class="form-control" name="contact" id="contact" placeholder="10-digit mobile number" required>
                                    </div>
                                    <div class="error-message" id="contact-error"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>WhatsApp Number <span>*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                        <input type="tel" class="form-control" name="whatsapp" id="whatsapp" placeholder="WhatsApp number" required>
                                    </div>
                                    <div class="error-message" id="whatsapp-error"></div>
                                </div>
                            </div>

                            <!-- Education -->
                            <div class="section-title">
                                <i class="fas fa-graduation-cap"></i> Education
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>College <span>*</span></label>
                                    <select class="form-control" name="college" id="college" required>
                                        <option value="" disabled selected>Select your college</option>
                                        <?php if (!empty($colleges)): ?>
                                            <?php foreach ($colleges as $college) { ?>
                                                <option value="<?php echo htmlspecialchars($college['college_name']); ?>">
                                                    <?php echo htmlspecialchars($college['college_name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="error-message" id="college-error"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Course <span>*</span></label>
                                    <select class="form-control" name="course" id="course" required>
                                        <option value="" disabled selected>Select your course</option>
                                        <?php if (!empty($courses)): ?>
                                            <?php foreach ($courses as $course) { ?>
                                                <option value="<?php echo htmlspecialchars($course['course_name']); ?>">
                                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="error-message" id="course-error"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Semester <span>*</span></label>
                                    <select class="form-control" name="semester" id="semester" required>
                                        <option value="" disabled selected>Select semester</option>
                                        <option value="1st">1st Semester</option>
                                        <option value="2nd">2nd Semester</option>
                                        <option value="3rd">3rd Semester</option>
                                        <option value="4th">4th Semester</option>
                                        <option value="5th">5th Semester</option>
                                        <option value="6th">6th Semester</option>
                                        <option value="7th">7th Semester</option>
                                        <option value="8th">8th Semester</option>
                                    </select>
                                    <div class="error-message" id="semester-error"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Branch <span>*</span></label>
                                    <select class="form-control" name="branch" id="branch" required>
                                        <option value="" disabled selected>Select your branch</option>
                                        <?php if (!empty($branches)): ?>
                                            <?php foreach ($branches as $branch) { ?>
                                                <option value="<?php echo htmlspecialchars($branch['name']); ?>">
                                                    <?php echo htmlspecialchars($branch['name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="error-message" id="branch-error"></div>
                                </div>
                            </div>

                            <!-- Session & Agreement -->
                            <div class="section-title">
                                <i class="fas fa-calendar-check"></i> Session & Terms
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Academic Session</label>
                                    <div class="session-field">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><?php echo isset($active_sessions[0]) ? htmlspecialchars($active_sessions[0]['session_name']) : 'Current Session'; ?></span>
                                        <input type="hidden" name="session" value="<?php echo isset($active_sessions[0]) ? htmlspecialchars($active_sessions[0]['session_name']) : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="terms-group">
                                    <div class="terms-check">
                                        <input type="checkbox" id="terms" required>
                                        <label for="terms">I accept the <a href="#">Terms & Conditions</a> <span>*</span></label>
                                    </div>
                                    <div class="error-message" id="terms-error"></div>
                                </div>
                            </div>

                            <hr>

                            <button type="submit" class="btn-register">
                                <i class="fas fa-bolt"></i> Register Now <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Right Panel: Attractive Features -->
                    <div class="features-panel">
                        <h3>
                            <i class="fas fa-star"></i> Why NetCoders?
                        </h3>

                        <div class="feature-grid">
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-laptop-code"></i></div>
                                <div class="feature-text">
                                    <h4>Live Project Training</h4>
                                    <p>Work on real-world projects</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-clock"></i></div>
                                <div class="feature-text">
                                    <h4>Flexible Batches</h4>
                                    <p>Weekend & Weekday options</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-certificate"></i></div>
                                <div class="feature-text">
                                    <h4>Govt. Approved Certification</h4>
                                    <p>Valid & recognized certificates</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                                <div class="feature-text">
                                    <h4>100% Placement Record</h4>
                                    <p>Till date: 1500+ placed</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-microchip"></i></div>
                                <div class="feature-text">
                                    <h4>Latest Tech Curriculum</h4>
                                    <p>AI, ML, Full Stack, Cloud</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                                <div class="feature-text">
                                    <h4>24/7 Mentor Support</h4>
                                    <p>Get doubts cleared anytime</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info Card -->
                        <div class="contact-card">
                            <h5><i class="fas fa-phone-alt"></i> Need Assistance?</h5>
                            <p><i class="fas fa-envelope"></i> admissions@netcoder.in</p>
                            <p><i class="fas fa-phone"></i> +91 98765 43210</p>
                            <p><i class="fas fa-map-pin"></i> 24, Tech Park, Bangalore</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // GSAP Loading Animation
            window.addEventListener('load', function() {
                // Create a timeline for loading animation
                const tl = gsap.timeline();
                
                // Initial animation of logo
                tl.to("#loadingLogo", {
                    opacity: 1,
                    scale: 1,
                    duration: 1,
                    ease: "power2.out"
                })
                .to("#loadingText", {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: "power2.out"
                }, "-=0.5")
                .to("#loadingBar", {
                    width: "100%",
                    duration: 2.5,
                    ease: "power2.inOut"
                })
                .to("#loadingScreen", {
                    duration: 0.8,
                    ease: "power2.inOut",
                    delay: 0.2,
                    onComplete: function() {
                        document.getElementById('loadingScreen').style.display = 'none';
                    }
                })
                .to("#mainContent", {
                    opacity: 1,
                    visibility: "visible",
                    duration: 1,
                    ease: "power2.out"
                })
                .from(".registration-wrapper", {
                    y: 30,
                    duration: 1,
                    ease: "power2.out"
                }, "-=0.5")
                .from(".header", {
                    y: -20,
                    duration: 0.8,
                    ease: "power2.out"
                }, "-=0.8")
                .from(".form-section", {
                    x: -20,
                    duration: 0.8,
                    ease: "power2.out"
                }, "-=0.6")
                .from(".features-panel", {
                    x: 20,
                    duration: 0.8,
                    ease: "power2.out"
                }, "-=0.8")
                .from(".form-group", {
                    y: 15,
                    stagger: 0.1,
                    duration: 0.5,
                    ease: "power2.out"
                }, "-=0.4")
                .from(".feature-item", {
                    y: 15,
                    stagger: 0.1,
                    duration: 0.5,
                    ease: "power2.out"
                }, "-=0.4")
                .from(".contact-card", {
                    y: 15,
                    duration: 0.5,
                    ease: "power2.out"
                }, "-=0.2")
                .from(".btn-register", {
                    scale: 0.95,
                    duration: 0.5,
                    ease: "backOut(1.5)"
                }, "-=0.2");
            });

            function validateForm() {
                let isValid = true;
                
                // Clear previous errors
                document.querySelectorAll('.error-message').forEach(e => e.innerHTML = '');

                // Name validation
                const name = document.getElementById('name');
                if (!/^[a-zA-Z\s]+$/.test(name.value)) {
                    document.getElementById('name-error').innerText = 'Only alphabets allowed';
                    name.style.borderColor = '#ef4444';
                    gsap.to(name, {
                        x: 5,
                        repeat: 3,
                        yoyo: true,
                        duration: 0.1,
                        ease: "power2.inOut"
                    });
                    isValid = false;
                } else {
                    name.style.borderColor = '';
                }

                // Email
                const email = document.getElementById('email');
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                    document.getElementById('email-error').innerText = 'Invalid email format';
                    email.style.borderColor = '#ef4444';
                    gsap.to(email, {
                        x: 5,
                        repeat: 3,
                        yoyo: true,
                        duration: 0.1,
                        ease: "power2.inOut"
                    });
                    isValid = false;
                } else {
                    email.style.borderColor = '';
                }

                // Phone
                const contact = document.getElementById('contact');
                if (!/^\d{10}$/.test(contact.value)) {
                    document.getElementById('contact-error').innerText = '10 digits required';
                    contact.style.borderColor = '#ef4444';
                    gsap.to(contact, {
                        x: 5,
                        repeat: 3,
                        yoyo: true,
                        duration: 0.1,
                        ease: "power2.inOut"
                    });
                    isValid = false;
                } else {
                    contact.style.borderColor = '';
                }

                // WhatsApp
                const whatsapp = document.getElementById('whatsapp');
                if (!/^\d{10}$/.test(whatsapp.value)) {
                    document.getElementById('whatsapp-error').innerText = '10 digits required';
                    whatsapp.style.borderColor = '#ef4444';
                    gsap.to(whatsapp, {
                        x: 5,
                        repeat: 3,
                        yoyo: true,
                        duration: 0.1,
                        ease: "power2.inOut"
                    });
                    isValid = false;
                } else {
                    whatsapp.style.borderColor = '';
                }

                // Select fields
                const college = document.getElementById('college');
                if (!college.value) {
                    document.getElementById('college-error').innerText = 'Select college';
                    college.style.borderColor = '#ef4444';
                    gsap.to(college, {
                        x: 5,
                        repeat: 3,
                        yoyo: true,
                        duration: 0.1,
                        ease: "power2.inOut"
                    });
                    isValid = false;
                } else {
                    college.style.borderColor = '';
                }

                const course = document.getElementById('course');
                if (!course.value) {
                    document.getElementById('course-error').innerText = 'Select course';
                    course.style.borderColor = '#ef4444';
                    gsap.to(course, {
                        x: 5,
                        repeat: 3,
                        yoyo: true,
                        duration: 0.1,
                        ease: "power2.inOut"
                    });
                    isValid = false;
                } else {
                    course.style.borderColor = '';
                }

                const semester = document.getElementById('semester');
                if (!semester.value) {
                    document.getElementById('semester-error').innerText = 'Select semester';
                    semester.style.borderColor = '#ef4444';
                    gsap.to(semester, {
                        x: 5,
                        repeat: 3,
                        yoyo: true,
                        duration: 0.1,
                        ease: "power2.inOut"
                    });
                    isValid = false;
                } else {
                    semester.style.borderColor = '';
                }

                const branch = document.getElementById('branch');
                if (!branch.value) {
                    document.getElementById('branch-error').innerText = 'Select branch';
                    branch.style.borderColor = '#ef4444';
                    gsap.to(branch, {
                        x: 5,
                        repeat: 3,
                        yoyo: true,
                        duration: 0.1,
                        ease: "power2.inOut"
                    });
                    isValid = false;
                } else {
                    branch.style.borderColor = '';
                }

                // Terms
                if (!document.getElementById('terms').checked) {
                    document.getElementById('terms-error').innerText = 'Please accept terms';
                    const terms = document.getElementById('terms');
                    gsap.to(terms, {
                        x: 5,
                        repeat: 3,
                        yoyo: true,
                        duration: 0.1,
                        ease: "power2.inOut"
                    });
                    isValid = false;
                }

                return isValid;
            }

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        gsap.to(window, {
                            duration: 1,
                            scrollTo: {
                                y: target,
                                offsetY: 50
                            },
                            ease: "power2.inOut"
                        });
                    }
                });
            });
        </script>
    </body>
    </html>