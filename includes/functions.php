<?php
// Common functions for Buyunic Technologies Enrollment Portal

require_once 'config/database.php';

// Security functions
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone) {
    // Uganda phone number validation
    $pattern = '/^(\+256|0)[7-9][0-9]{8}$/';
    return preg_match($pattern, $phone);
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function generate_otp() {
    return sprintf('%06d', mt_rand(100000, 999999));
}

function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

// Session management
function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        session_start();
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: auth/login.php');
        exit;
    }
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        header('Location: admin/login.php');
        exit;
    }
}

function logout_user() {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
}

// Application ID generation
function generate_application_id($is_internee = false) {
    $program_initial = $is_internee ? 'I' : 'T'; // I for Internee, T for Training
    $date = date('dmy'); // ddmmyy format
    $year = date('y');
    
    // Get the next serial number
    $db = Database::getInstance();
    $sql = "SELECT COUNT(*) + 1 as next_serial FROM applications WHERE DATE(created_at) = CURDATE()";
    $result = $db->fetch($sql);
    $serial = sprintf('%05d', $result['next_serial']);
    
    return "A-{$program_initial}-{$date}-{$year}{$serial}";
}

// File upload functions
function validate_file_upload($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'docx']) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error occurred.';
        return $errors;
    }
    
    $file_size = $file['size'];
    $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $max_size = 1048576; // 1MB
    
    if ($file_size > $max_size) {
        $errors[] = 'File size must not exceed 1MB.';
    }
    
    if (!in_array($file_type, $allowed_types)) {
        $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowed_types);
    }
    
    // Additional MIME type validation
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'image/jpeg', 'image/png', 'application/pdf', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    if (!in_array($mime_type, $allowed_mimes)) {
        $errors[] = 'Invalid file type detected.';
    }
    
    return $errors;
}

function upload_file($file, $user_id, $document_type) {
    $upload_dir = 'uploads/' . $user_id . '/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'original_name' => $file['name'],
            'size' => $file['size'],
            'type' => $file_extension
        ];
    }
    
    return ['success' => false, 'error' => 'Failed to move uploaded file.'];
}

// Email functions
function send_email($to, $subject, $message, $from = 'apply@buyunic.ug') {
    $headers = [
        'From' => $from,
        'Reply-To' => $from,
        'X-Mailer' => 'PHP/' . phpversion(),
        'Content-Type' => 'text/html; charset=UTF-8'
    ];
    
    $header_string = '';
    foreach ($headers as $key => $value) {
        $header_string .= $key . ': ' . $value . "\r\n";
    }
    
    return mail($to, $subject, $message, $header_string);
}

function send_otp_email($email, $otp, $name) {
    $subject = 'Buyunic Technologies - Email Verification';
    $message = "
    <html>
    <head>
        <title>Email Verification</title>
    </head>
    <body>
        <h2>Email Verification</h2>
        <p>Dear {$name},</p>
        <p>Your verification code is: <strong>{$otp}</strong></p>
        <p>This code will expire in 5 minutes.</p>
        <p>If you did not request this code, please ignore this email.</p>
        <br>
        <p>Best regards,<br>Buyunic Technologies Team</p>
    </body>
    </html>";
    
    return send_email($email, $subject, $message);
}

function send_password_reset_email($email, $token, $name) {
    $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/auth/reset_password.php?token=" . $token;
    $subject = 'Buyunic Technologies - Password Reset';
    $message = "
    <html>
    <head>
        <title>Password Reset</title>
    </head>
    <body>
        <h2>Password Reset Request</h2>
        <p>Dear {$name},</p>
        <p>You have requested to reset your password. Click the link below to reset your password:</p>
        <p><a href='{$reset_link}'>Reset Password</a></p>
        <p>This link will expire in 1 hour.</p>
        <p>If you did not request this reset, please ignore this email.</p>
        <br>
        <p>Best regards,<br>Buyunic Technologies Team</p>
    </body>
    </html>";
    
    return send_email($email, $subject, $message);
}

// Notification functions
function add_notification($user_id, $title, $message, $type = 'Info') {
    $db = Database::getInstance();
    $sql = "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)";
    return $db->query($sql, [$user_id, $title, $message, $type]);
}

function get_user_notifications($user_id, $unread_only = false) {
    $db = Database::getInstance();
    $sql = "SELECT * FROM notifications WHERE user_id = ?";
    $params = [$user_id];
    
    if ($unread_only) {
        $sql .= " AND is_read = 0";
    }
    
    $sql .= " ORDER BY created_at DESC";
    return $db->fetchAll($sql, $params);
}

function mark_notification_read($notification_id, $user_id) {
    $db = Database::getInstance();
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    return $db->query($sql, [$notification_id, $user_id]);
}

// Admin logging
function log_admin_action($admin_id, $action_type, $description, $affected_user_id = null, $affected_application_id = null) {
    $db = Database::getInstance();
    $sql = "INSERT INTO admin_logs (admin_id, action_type, description, ip_address, user_agent, affected_user_id, affected_application_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    return $db->query($sql, [
        $admin_id, $action_type, $description, $ip_address, 
        $user_agent, $affected_user_id, $affected_application_id
    ]);
}

// Utility functions
function format_currency($amount) {
    return 'UGX ' . number_format($amount, 0);
}

function format_date($date) {
    return date('F j, Y', strtotime($date));
}

function format_datetime($datetime) {
    return date('F j, Y g:i A', strtotime($datetime));
}

function time_ago($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    return floor($time/31536000) . ' years ago';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function set_flash_message($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Error handling
function handle_error($message, $redirect_url = null) {
    error_log($message);
    set_flash_message($message, 'error');
    if ($redirect_url) {
        redirect($redirect_url);
    }
}

// Input validation
function validate_required_fields($fields, $data) {
    $errors = [];
    foreach ($fields as $field => $label) {
        if (empty($data[$field])) {
            $errors[] = "$label is required.";
        }
    }
    return $errors;
}

function validate_nin($nin) {
    // Uganda NIN format: CM followed by 13 digits
    return preg_match('/^CM\d{13}$/', $nin);
}

function validate_passport($passport) {
    // Basic passport validation - alphanumeric, 6-9 characters
    return preg_match('/^[A-Z0-9]{6,9}$/', $passport);
}

// System settings
function get_setting($key, $default = null) {
    static $settings = null;
    
    if ($settings === null) {
        $db = Database::getInstance();
        $result = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        foreach ($result as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

function update_setting($key, $value) {
    $db = Database::getInstance();
    $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
    return $db->query($sql, [$value, $key]);
}
?>