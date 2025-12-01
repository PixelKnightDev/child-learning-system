<?php
/**
 * Signup - Role Selection Page
 * Users select their role before proceeding to signup form
 */

require_once 'config/config.php';
require_once 'includes/auth.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    switch ($_SESSION['role']) {
        case ROLE_ADMIN:
            header('Location: admin/dashboard.php');
            exit();
        case ROLE_TEACHER:
            header('Location: teacher/dashboard.php');
            exit();
        case ROLE_STUDENT:
            header('Location: student/dashboard.php');
            exit();
        case ROLE_PARENT:
            header('Location: parent/dashboard.php');
            exit();
    }
}

$page_title = 'Sign Up';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars(APP_NAME); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .signup-container {
            width: 100%;
            max-width: 900px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 50px;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .app-title {
            color: #667eea;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
            font-size: 32px;
        }
        .app-subtitle {
            color: #999;
            text-align: center;
            margin-bottom: 40px;
            font-size: 16px;
        }
        .role-card {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
        }
        .role-card:hover {
            border-color: #667eea;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
            transform: translateY(-5px);
        }
        .role-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }
        .role-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        .role-description {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .role-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .role-button:hover {
            transform: scale(1.05);
            color: white;
            text-decoration: none;
        }
        .login-link {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="app-title">Join Our Platform</div>
        <div class="app-subtitle">Select your role to get started</div>

        <div class="row">
            <!-- Student Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="role-card">
                    <span class="role-icon">👨‍🎓</span>
                    <div class="role-title">Student</div>
                    <div class="role-description">
                        Join your class and access learning materials, activities, and track your progress.
                    </div>
                    <a href="signup-student.php" class="role-button">Sign Up</a>
                </div>
            </div>

            <!-- Teacher Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="role-card">
                    <span class="role-icon">👨‍🏫</span>
                    <div class="role-title">Teacher</div>
                    <div class="role-description">
                        Create and manage classes, assign activities, and monitor student progress.
                    </div>
                    <a href="signup-teacher.php" class="role-button">Sign Up</a>
                </div>
            </div>

            <!-- Parent Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="role-card">
                    <span class="role-icon">👨‍👩‍👧</span>
                    <div class="role-title">Parent</div>
                    <div class="role-description">
                        Monitor your child's progress, view their activities, and communicate with teachers.
                    </div>
                    <a href="signup-parent.php" class="role-button">Sign Up</a>
                </div>
            </div>

            <!-- Admin Card (Disabled) -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="role-card" style="opacity: 0.6; cursor: not-allowed;">
                    <span class="role-icon">⚙️</span>
                    <div class="role-title">Admin</div>
                    <div class="role-description">
                        Manage system, users, and settings. Admin accounts are created by invitation only.
                    </div>
                    <button class="role-button" style="opacity: 0.5; cursor: not-allowed;" disabled>Invitation Only</button>
                </div>
            </div>
        </div>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
