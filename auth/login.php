<?php
require_once '../includes/functions.php';
start_secure_session();

// Redirect if already logged in
if (is_logged_in()) {
    redirect('user/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    
    // Basic validation
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!validate_email($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }
    
    if (empty($errors)) {
        $db = Database::getInstance();
        
        // Check for account lockout
        $user = $db->fetch(
            "SELECT id, first_name, middle_name, last_name, password_hash, is_verified, 
                    failed_login_attempts, locked_until 
             FROM users WHERE email = ?", 
            [$email]
        );
        
        if (!$user) {
            $errors[] = 'Invalid email or password.';
        } elseif (!$user['is_verified']) {
            $_SESSION['registration_email'] = $email;
            set_flash_message('Please verify your email address first.', 'warning');
            redirect('auth/verify.php');
        } elseif ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $remaining_time = ceil((strtotime($user['locked_until']) - time()) / 60);
            $errors[] = "Account is locked. Try again in {$remaining_time} minutes.";
        } elseif (!verify_password($password, $user['password_hash'])) {
            // Increment failed login attempts
            $failed_attempts = $user['failed_login_attempts'] + 1;
            $max_attempts = (int)get_setting('max_login_attempts', 5);
            
            if ($failed_attempts >= $max_attempts) {
                $lockout_duration = (int)get_setting('lockout_duration', 1800); // 30 minutes
                $locked_until = date('Y-m-d H:i:s', time() + $lockout_duration);
                
                $db->query(
                    "UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?",
                    [$failed_attempts, $locked_until, $user['id']]
                );
                
                $errors[] = "Account locked due to too many failed login attempts. Try again in 30 minutes.";
            } else {
                $db->query(
                    "UPDATE users SET failed_login_attempts = ? WHERE id = ?",
                    [$failed_attempts, $user['id']]
                );
                
                $remaining_attempts = $max_attempts - $failed_attempts;
                $errors[] = "Invalid email or password. {$remaining_attempts} attempts remaining.";
            }
        } else {
            // Successful login
            try {
                // Reset failed login attempts
                $db->query(
                    "UPDATE users SET failed_login_attempts = 0, locked_until = NULL WHERE id = ?",
                    [$user['id']]
                );
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']);
                
                // Handle remember me
                if ($remember_me) {
                    $remember_token = generate_token();
                    setcookie('remember_token', $remember_token, time() + (30 * 24 * 60 * 60), '/', '', true, true); // 30 days
                    
                    // Store remember token in database (you might want to create a separate table for this)
                    $db->query(
                        "UPDATE users SET remember_token = ? WHERE id = ?",
                        [$remember_token, $user['id']]
                    );
                }
                
                // Add login notification
                add_notification($user['id'], 'Login Successful', 'You have successfully logged into your account.', 'Success');
                
                // Redirect to intended page or dashboard
                $redirect_url = $_SESSION['intended_url'] ?? 'user/dashboard.php';
                unset($_SESSION['intended_url']);
                
                set_flash_message('Welcome back!', 'success');
                redirect($redirect_url);
                
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                $errors[] = 'Login failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Buyunic Technologies Enrollment Portal</title>
    <meta name="description" content="Login to your Buyunic Technologies account">
    
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <img src="../assets/img/logo.png" alt="Buyunic Technologies Logo" onerror="this.style.display='none'">
                <div class="logo-text">Buyunic Technologies</div>
            </div>
            
            <nav class="nav">
                <ul class="nav-menu">
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../programs.php">Programs</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="../contact.php">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <?php
            $flash_message = get_flash_message();
            if ($flash_message): ?>
                <div class="alert alert-<?php echo $flash_message['type']; ?>">
                    <?php echo htmlspecialchars($flash_message['message']); ?>
                </div>
            <?php endif; ?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Sign In</h1>
        <p>Welcome back! Please sign in to your account</p>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" data-validate>
        <div class="form-group">
            <label for="email" class="form-label">Email Address *</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">Password *</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-check">
            <input type="checkbox" id="remember_me" name="remember_me" class="form-check-input">
            <label for="remember_me" class="form-label">Remember me for 30 days</label>
        </div>
        
        <div class="btn-group">
            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p><a href="forgot_password.php">Forgot your password?</a></p>
        <p>Don't have an account? <a href="register.php">Create one here</a></p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Account Security</h2>
    </div>
    
    <div class="alert alert-info">
        <h4>Security Features:</h4>
        <ul class="mb-0">
            <li>Account lockout after 5 failed login attempts</li>
            <li>Secure password encryption</li>
            <li>Session timeout after 15 minutes of inactivity</li>
            <li>Email notifications for important account activities</li>
        </ul>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Quick Access</h2>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="text-center p-3">
                <h3>For Students</h3>
                <p>Access your application, upload documents, and track your progress</p>
                <a href="register.php" class="btn btn-outline">Register Now</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="text-center p-3">
                <h3>For Administrators</h3>
                <p>Manage applications, review documents, and process enrollments</p>
                <a href="../admin/login.php" class="btn btn-secondary">Admin Login</a>
            </div>
        </div>
    </div>
</div>

<style>
.row {
    display: flex;
    flex-wrap: wrap;
    margin: -0.5rem;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 0.5rem;
}

@media (max-width: 768px) {
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="../about.php">About Us</a>
                <a href="../programs.php">Programs</a>
                <a href="../contact.php">Contact</a>
                <a href="../privacy.php">Privacy Policy</a>
                <a href="../terms.php">Terms & Conditions</a>
            </div>
            <div class="footer-info">
                <p>&copy; <?php echo date('Y'); ?> Buyunic Technologies. All rights reserved.</p>
                <p>Plot 28, North Road, Northern City Division, Mbale City</p>
                <p>WhatsApp: +256 207 901 434 | Email: info@buyunic.ug</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="../assets/js/main.js"></script>
</body>
</html>