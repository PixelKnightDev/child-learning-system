<?php
/**
 * Registration Page
 * Allows new users to register as students with class selection
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

// Get all available classes
$classes_result = $mysqli->query("SELECT id, class_name, grade, section FROM classes ORDER BY grade, class_name");
$classes = $classes_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $class_id = intval($_POST['class_id'] ?? 0);

    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif ($class_id <= 0) {
        $error = 'You must select a class to join.';
    } else {
        // Check if email already exists
        $check_stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error = 'Email address is already registered.';
        } else {
            // Verify class exists
            $class_check = $mysqli->prepare("SELECT id FROM classes WHERE id = ?");
            $class_check->bind_param("i", $class_id);
            $class_check->execute();
            
            if ($class_check->get_result()->num_rows === 0) {
                $error = 'Invalid class selected.';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = ROLE_STUDENT;

                // Begin transaction
                $mysqli->begin_transaction();

                try {
                    // Insert user
                    $user_stmt = $mysqli->prepare(
                        "INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, ?)"
                    );
                    $user_stmt->bind_param("ssss", $email, $hashed_password, $full_name, $role);
                    
                    if (!$user_stmt->execute()) {
                        throw new Exception('Error creating user account: ' . $user_stmt->error);
                    }
                    
                    $user_id = $user_stmt->insert_id;
                    $user_stmt->close();

                    // Insert student record
                    $student_stmt = $mysqli->prepare(
                        "INSERT INTO students (user_id, class_id) VALUES (?, ?)"
                    );
                    $student_stmt->bind_param("ii", $user_id, $class_id);
                    
                    if (!$student_stmt->execute()) {
                        throw new Exception('Error creating student record: ' . $student_stmt->error);
                    }
                    
                    $student_id = $student_stmt->insert_id;
                    $student_stmt->close();

                    // Auto-assign all class activities to the new student
                    $assign_stmt = $mysqli->prepare("
                        INSERT INTO activity_assignments (activity_id, student_id, status)
                        SELECT id, ?, ?
                        FROM activities
                        WHERE class_id = ?
                    ");
                    
                    if ($assign_stmt === false) {
                        throw new Exception('Error preparing assignment statement: ' . $mysqli->error);
                    }
                    
                    $status_not_started = STATUS_NOT_STARTED;
                    $assign_stmt->bind_param("isi", $student_id, $status_not_started, $class_id);
                    
                    if (!$assign_stmt->execute()) {
                        throw new Exception('Error assigning activities: ' . $assign_stmt->error);
                    }
                    
                    $assigned_count = $assign_stmt->affected_rows;
                    $assign_stmt->close();

                    // Commit transaction
                    $mysqli->commit();

                    $success = "Registration successful! You've been assigned $assigned_count activity(ies). You can now <a href='login.php'>login</a> to your account.";
                    
                    // Clear form
                    $_POST = [];

                } catch (Exception $e) {
                    // Rollback transaction
                    $mysqli->rollback();
                    $error = $e->getMessage();
                }
            }
            
            $class_check->close();
        }
        
        $check_stmt->close();
    }
}

$page_title = 'Register';
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
        .register-container {
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
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            color: white;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
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
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="app-title">Student Registration</div>
        <div class="app-subtitle">Join your class and start learning</div>

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
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Enter password"
                >
                <div class="password-requirements">
                    Password must be at least 6 characters
                </div>
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

            <div class="mb-3">
                <label for="class_id" class="form-label">Select Your Class <span class="text-danger">*</span></label>
                <select class="form-select" id="class_id" name="class_id" required>
                    <option value="">Choose your class...</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo (intval($_POST['class_id'] ?? 0) === $class['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?> 
                            (Grade <?php echo htmlspecialchars($class['grade']); ?>, 
                            Section <?php echo htmlspecialchars($class['section']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted d-block mt-2">
                    <i class="bi bi-info-circle"></i> You will automatically be assigned all activities from your class.
                </small>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-register btn-primary w-100">Create Account</button>
            </div>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
