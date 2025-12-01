<?php
/**
 * Teacher Signup Page
 */

require_once 'config/config.php';
require_once 'config/db.php';
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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $school_name = trim($_POST['school_name'] ?? '');
    $subject = trim($_POST['subject'] ?? '');

    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All required fields must be filled.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $check_stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error = 'Email address is already registered.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = ROLE_TEACHER;

            // Insert user
            $user_stmt = $mysqli->prepare(
                "INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, ?)"
            );
            $user_stmt->bind_param("ssss", $email, $hashed_password, $full_name, $role);
            
            if ($user_stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php' style='color: #155724; font-weight: 600;'>login</a> as a teacher.";
                $_POST = [];
            } else {
                $error = 'Error creating account. Please try again.';
            }
            $user_stmt->close();
        }
        
        $check_stmt->close();
    }
}

$page_title = 'Teacher Sign Up';
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
            max-width: 500px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
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
            font-size: 28px;
        }
        .app-subtitle {
            color: #999;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .btn-signup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-signup:hover {
            transform: translateY(-2px);
            color: white;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 12px;
            transition: border-color 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: none;
        }
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease-out;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="app-title">👨‍🏫 Teacher Registration</div>
        <div class="app-subtitle">Create your teacher account</div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="full_name" 
                    name="full_name" 
                    value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                    required
                    placeholder="Enter your full name"
                >
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    required
                    placeholder="Enter your email"
                >
            </div>

            <div class="mb-3">
                <label for="school_name" class="form-label">School/Institution Name</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="school_name" 
                    name="school_name" 
                    value="<?php echo htmlspecialchars($_POST['school_name'] ?? ''); ?>"
                    placeholder="(Optional) Enter your school name"
                >
            </div>

            <div class="mb-3">
                <label for="subject" class="form-label">Subject/Specialization</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="subject" 
                    name="subject" 
                    value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>"
                    placeholder="(Optional) e.g., Mathematics, English"
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Enter password (min 6 characters)"
                >
                <small class="text-muted d-block mt-1">Minimum 6 characters</small>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="confirm_password" 
                    name="confirm_password" 
                    required
                    placeholder="Confirm password"
                >
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-signup btn-primary w-100">Create Account</button>
            </div>
        </form>

        <div class="back-link">
            <a href="signup.php">← Back to role selection</a> | 
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
