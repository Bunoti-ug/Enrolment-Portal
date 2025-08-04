<?php
$page_title = 'Email Verification';
require_once '../includes/header.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('user/dashboard.php');
}

// Check if registration email is set
if (!isset($_SESSION['registration_email'])) {
    set_flash_message('Please register first.', 'error');
    redirect('auth/register.php');
}

$email = $_SESSION['registration_email'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp_code = sanitize_input($_POST['otp_code'] ?? '');
    $action = $_POST['action'] ?? 'verify';
    
    if ($action === 'verify') {
        if (empty($otp_code)) {
            $errors[] = 'Please enter the verification code.';
        } elseif (strlen($otp_code) !== 6 || !ctype_digit($otp_code)) {
            $errors[] = 'Verification code must be 6 digits.';
        }
        
        if (empty($errors)) {
            $db = Database::getInstance();
            $user = $db->fetch(
                "SELECT id, first_name, middle_name, last_name, otp_code, otp_expires_at 
                 FROM users WHERE email = ? AND is_verified = 0", 
                [$email]
            );
            
            if (!$user) {
                $errors[] = 'Invalid verification request.';
            } elseif (strtotime($user['otp_expires_at']) < time()) {
                $errors[] = 'Verification code has expired. Please request a new one.';
            } elseif ($user['otp_code'] !== $otp_code) {
                $errors[] = 'Invalid verification code.';
            } else {
                // Verify user
                try {
                    $db->query(
                        "UPDATE users SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL WHERE id = ?", 
                        [$user['id']]
                    );
                    
                    // Log user in
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']);
                    
                    // Add welcome notification
                    add_notification($user['id'], 'Welcome!', 'Your account has been successfully verified. Welcome to Buyunic Technologies!', 'Success');
                    
                    unset($_SESSION['registration_email']);
                    set_flash_message('Account verified successfully! Welcome to Buyunic Technologies.', 'success');
                    redirect('user/dashboard.php');
                    
                } catch (Exception $e) {
                    error_log("Verification error: " . $e->getMessage());
                    $errors[] = 'Verification failed. Please try again.';
                }
            }
        }
    } elseif ($action === 'resend') {
        // Resend OTP
        $db = Database::getInstance();
        $user = $db->fetch(
            "SELECT id, first_name, middle_name, last_name FROM users WHERE email = ? AND is_verified = 0", 
            [$email]
        );
        
        if ($user) {
            try {
                $new_otp = generate_otp();
                $otp_expires = date('Y-m-d H:i:s', time() + 300); // 5 minutes
                
                $db->query(
                    "UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?", 
                    [$new_otp, $otp_expires, $user['id']]
                );
                
                $full_name = trim($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']);
                if (send_otp_email($email, $new_otp, $full_name)) {
                    set_flash_message('New verification code sent to your email.', 'success');
                } else {
                    $errors[] = 'Failed to send verification code. Please try again.';
                }
                
            } catch (Exception $e) {
                error_log("Resend OTP error: " . $e->getMessage());
                $errors[] = 'Failed to resend verification code. Please try again.';
            }
        } else {
            $errors[] = 'Invalid resend request.';
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Verify Your Email</h1>
        <p>We've sent a 6-digit verification code to <strong><?php echo htmlspecialchars($email); ?></strong></p>
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
        <input type="hidden" name="action" value="verify">
        
        <div class="form-group">
            <label for="otp_code" class="form-label">Verification Code *</label>
            <input type="text" id="otp_code" name="otp_code" class="form-control text-center" 
                   maxlength="6" placeholder="000000" 
                   style="font-size: 1.5rem; letter-spacing: 0.5rem;" required>
            <div class="form-text">Enter the 6-digit code sent to your email</div>
        </div>
        
        <div class="btn-group">
            <button type="submit" class="btn btn-primary btn-lg">Verify Account</button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p>Didn't receive the code?</p>
        <form method="POST" style="display: inline;">
            <input type="hidden" name="action" value="resend">
            <button type="submit" class="btn btn-outline">Resend Code</button>
        </form>
    </div>
    
    <div class="text-center mt-3">
        <p><a href="register.php">Use a different email address</a></p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Verification Tips</h2>
    </div>
    
    <div class="alert alert-info">
        <ul class="mb-0">
            <li>Check your email inbox and spam/junk folder</li>
            <li>The verification code expires in 5 minutes</li>
            <li>You can request a new code if the current one expires</li>
            <li>Make sure to enter all 6 digits correctly</li>
        </ul>
    </div>
</div>

<script>
// Auto-focus on OTP input and format input
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp_code');
    
    // Auto-focus
    otpInput.focus();
    
    // Format input (only numbers)
    otpInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 6);
    });
    
    // Auto-submit when 6 digits entered
    otpInput.addEventListener('input', function() {
        if (this.value.length === 6) {
            // Add small delay for better UX
            setTimeout(() => {
                this.form.submit();
            }, 500);
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>