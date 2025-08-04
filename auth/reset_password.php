<?php
$page_title = 'Reset Password';
require_once '../includes/header.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('user/dashboard.php');
}

$token = $_GET['token'] ?? '';
$errors = [];
$success = false;
$valid_token = false;

// Validate token
if (!empty($token)) {
    $db = Database::getInstance();
    $user = $db->fetch(
        "SELECT id, first_name, middle_name, last_name, reset_expires_at 
         FROM users WHERE reset_token = ? AND is_verified = 1", 
        [$token]
    );
    
    if ($user && strtotime($user['reset_expires_at']) > time()) {
        $valid_token = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    
    if (empty($confirm_password)) {
        $errors[] = 'Please confirm your password.';
    } elseif ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (empty($errors)) {
        try {
            $password_hash = hash_password($password);
            
            $db->query(
                "UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires_at = NULL, 
                        failed_login_attempts = 0, locked_until = NULL WHERE reset_token = ?",
                [$password_hash, $token]
            );
            
            // Add notification
            add_notification($user['id'], 'Password Reset', 'Your password has been successfully reset.', 'Success');
            
            $success = true;
            
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            $errors[] = 'Failed to reset password. Please try again.';
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Reset Your Password</h1>
        <?php if ($valid_token): ?>
            <p>Enter your new password below</p>
        <?php else: ?>
            <p>Invalid or expired reset link</p>
        <?php endif; ?>
    </div>
    
    <?php if (!$valid_token): ?>
        <div class="alert alert-error">
            <h4>Invalid Reset Link</h4>
            <p>The password reset link is invalid or has expired. Reset links are only valid for 1 hour.</p>
        </div>
        
        <div class="text-center">
            <a href="forgot_password.php" class="btn btn-primary">Request New Reset Link</a>
            <a href="login.php" class="btn btn-outline">Back to Login</a>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success">
            <h4>Password Reset Successful!</h4>
            <p>Your password has been successfully reset. You can now sign in with your new password.</p>
        </div>
        
        <div class="text-center">
            <a href="login.php" class="btn btn-primary btn-lg">Sign In Now</a>
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
                <label for="password" class="form-label">New Password *</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <div class="form-text">Minimum 8 characters</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm New Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <p><a href="login.php">Back to Login</a></p>
        </div>
    <?php endif; ?>
</div>

<?php if ($valid_token && !$success): ?>
<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Password Requirements</h2>
    </div>
    
    <div class="alert alert-info">
        <h4>Your new password should:</h4>
        <ul class="mb-0">
            <li>Be at least 8 characters long</li>
            <li>Include a mix of uppercase and lowercase letters (recommended)</li>
            <li>Include numbers and special characters (recommended)</li>
            <li>Be unique and not used elsewhere</li>
        </ul>
    </div>
</div>

<script>
// Real-time password strength indicator
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            showPasswordStrength(strength);
        });
    }
    
    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
    }
});

function calculatePasswordStrength(password) {
    let score = 0;
    
    if (password.length >= 8) score++;
    if (password.match(/[a-z]/)) score++;
    if (password.match(/[A-Z]/)) score++;
    if (password.match(/[0-9]/)) score++;
    if (password.match(/[^a-zA-Z0-9]/)) score++;
    
    return score;
}

function showPasswordStrength(strength) {
    // Remove existing indicator
    const existing = document.querySelector('.password-strength');
    if (existing) existing.remove();
    
    const passwordInput = document.getElementById('password');
    const indicator = document.createElement('div');
    indicator.className = 'password-strength mt-2';
    
    const colors = ['#f56565', '#ed8936', '#ecc94b', '#48bb78', '#38a169'];
    const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    
    indicator.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="flex: 1; height: 4px; background: #e2e8f0; border-radius: 2px;">
                <div style="height: 100%; width: ${(strength / 5) * 100}%; background: ${colors[strength - 1] || '#e2e8f0'}; border-radius: 2px; transition: all 0.3s ease;"></div>
            </div>
            <span style="font-size: 0.875rem; color: ${colors[strength - 1] || '#a0aec0'};">${labels[strength - 1] || ''}</span>
        </div>
    `;
    
    passwordInput.parentNode.appendChild(indicator);
}
</script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>