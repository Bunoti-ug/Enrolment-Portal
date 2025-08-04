<?php
require_once '../includes/functions.php';

start_secure_session();

// Clear remember me cookie if it exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    
    // Also clear from database if user is logged in
    if (is_logged_in()) {
        $db = Database::getInstance();
        $db->query("UPDATE users SET remember_token = NULL WHERE id = ?", [$_SESSION['user_id']]);
    }
}

// Log admin action if admin is logging out
if (is_admin_logged_in()) {
    log_admin_action($_SESSION['admin_id'], 'Logout', 'Admin logged out');
    $redirect_url = '../admin/login.php';
} else {
    $redirect_url = 'login.php';
}

// Clear session
logout_user();

// Set flash message
set_flash_message('You have been successfully logged out.', 'success');

// Redirect to login page
redirect($redirect_url);
?>