<?php
$page_title = 'Forgot Password';
require_once '../includes/header.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('user/dashboard.php');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    
    // Validation
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!validate_email($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($errors)) {
        $db = Database::getInstance();
        $user = $db->fetch(
            "SELECT id, first_name, middle_name, last_name FROM users WHERE email = ? AND is_verified = 1", 
            [$email]
        );
        
        if ($user) {
            try {
                $reset_token = generate_token();
                $reset_expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
                
                $db->query(
                    "UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id = ?",
                    [$reset_token, $reset_expires, $user['id']]
                );
                
                $full_name = trim($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']);
                if (send_password_reset_email($email, $reset_token, $full_name)) {
                    $success = true;
                } else {
                    $errors[] = 'Failed to send reset email. Please try again.';
                }
                
            } catch (Exception $e) {
                error_log("Password reset error: " . $e->getMessage());
                $errors[] = 'Failed to process password reset. Please try again.';
            }
        } else {
            // Don't reveal if email exists or not for security
            $success = true;
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Reset Your Password</h1>
        <p>Enter your email address and we'll send you a link to reset your password</p>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <h4>Reset Link Sent!</h4>
            <p>If an account with that email address exists, we've sent you a password reset link. Please check your email and follow the instructions.</p>
            <p><strong>Note:</strong> The reset link will expire in 1 hour.</p>
        </div>
        
        <div class="text-center">
            <a href="login.php" class="btn btn-primary">Back to Login</a>
        </div>
    <?php else: ?>
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
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                       placeholder="Enter your registered email address" required>
                <div class="form-text">We'll send a password reset link to this email</div>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary btn-lg">Send Reset Link</button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <p>Remember your password? <a href="login.php">Sign in here</a></p>
            <p>Don't have an account? <a href="register.php">Create one here</a></p>
        </div>
    <?php endif; ?>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Password Reset Instructions</h2>
    </div>
    
    <div class="alert alert-info">
        <h4>What happens next?</h4>
        <ol class="mb-0">
            <li>Check your email inbox (and spam folder) for a reset link</li>
            <li>Click the reset link in the email</li>
            <li>Enter your new password on the reset page</li>
            <li>Sign in with your new password</li>
        </ol>
    </div>
    
    <div class="alert alert-warning">
        <h4>Security Tips:</h4>
        <ul class="mb-0">
            <li>The reset link expires in 1 hour for security</li>
            <li>Only the most recent reset link will work</li>
            <li>Choose a strong password with at least 8 characters</li>
            <li>Don't share your password with anyone</li>
        </ul>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>