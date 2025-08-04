<?php
$page_title = 'User Registration';
require_once '../includes/header.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('user/dashboard.php');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize_input($_POST['first_name'] ?? '');
    $middle_name = sanitize_input($_POST['middle_name'] ?? '');
    $last_name = sanitize_input($_POST['last_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $agree_terms = isset($_POST['agree_terms']);
    
    // Validation
    $required_fields = [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'phone' => 'Phone Number',
        'password' => 'Password'
    ];
    
    $errors = array_merge($errors, validate_required_fields($required_fields, $_POST));
    
    if (!validate_email($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (!validate_phone($phone)) {
        $errors[] = 'Please enter a valid Uganda phone number.';
    }
    
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (!$agree_terms) {
        $errors[] = 'You must agree to the Terms and Conditions.';
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $db = Database::getInstance();
        $existing_user = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing_user) {
            $errors[] = 'An account with this email already exists.';
        }
    }
    
    // Register user
    if (empty($errors)) {
        try {
            $db->beginTransaction();
            
            $password_hash = hash_password($password);
            $otp_code = generate_otp();
            $otp_expires = date('Y-m-d H:i:s', time() + 300); // 5 minutes
            
            $sql = "INSERT INTO users (first_name, middle_name, last_name, email, phone, password_hash, otp_code, otp_expires_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $db->query($sql, [
                $first_name, $middle_name, $last_name, $email, $phone, 
                $password_hash, $otp_code, $otp_expires
            ]);
            
            // Send OTP email
            $full_name = trim("$first_name $middle_name $last_name");
            if (send_otp_email($email, $otp_code, $full_name)) {
                $db->commit();
                $_SESSION['registration_email'] = $email;
                redirect('auth/verify.php');
            } else {
                $db->rollback();
                $errors[] = 'Failed to send verification email. Please try again.';
            }
            
        } catch (Exception $e) {
            $db->rollback();
            error_log("Registration error: " . $e->getMessage());
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Create Your Account</h1>
        <p>Join Buyunic Technologies to start your professional journey</p>
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
            <label for="first_name" class="form-label">First Name *</label>
            <input type="text" id="first_name" name="first_name" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="middle_name" class="form-label">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['middle_name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text" id="last_name" name="last_name" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email Address *</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            <div class="form-text">We'll send a verification code to this email</div>
        </div>
        
        <div class="form-group">
            <label for="phone" class="form-label">Phone Number *</label>
            <input type="tel" id="phone" name="phone" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                   placeholder="+256 or 0" required>
            <div class="form-text">Format: +256XXXXXXXXX or 0XXXXXXXXX</div>
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">Password *</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <div class="form-text">Minimum 8 characters</div>
        </div>
        
        <div class="form-group">
            <label for="confirm_password" class="form-label">Confirm Password *</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        
        <div class="form-check">
            <input type="checkbox" id="agree_terms" name="agree_terms" class="form-check-input" required>
            <label for="agree_terms" class="form-label">
                I agree to the <a href="terms.php" target="_blank">Terms and Conditions</a> 
                and <a href="privacy.php" target="_blank">Privacy Policy</a> *
            </label>
        </div>
        
        <div class="btn-group">
            <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p>Already have an account? <a href="login.php">Sign in here</a></p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Why Choose Buyunic Technologies?</h2>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="text-center p-3">
                <h3>Professional Training</h3>
                <p>Industry-relevant courses designed to enhance your technical skills</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-3">
                <h3>Internship Opportunities</h3>
                <p>Gain practical experience through our structured internship programs</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-3">
                <h3>Career Development</h3>
                <p>Build your career with our comprehensive skill development programs</p>
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

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
    padding: 0.5rem;
}

@media (max-width: 768px) {
    .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>