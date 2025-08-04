<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

$flash_message = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Buyunic Technologies Enrollment Portal</title>
    <meta name="description" content="Buyunic Technologies Professional Training and Internship Enrollment Portal">
    <meta name="keywords" content="technology training, internship, Uganda, Buyunic Technologies">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Additional CSS -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <img src="assets/img/logo.png" alt="Buyunic Technologies Logo" onerror="this.style.display='none'">
                <div class="logo-text">Buyunic Technologies</div>
            </div>
            
            <nav class="nav">
                <ul class="nav-menu">
                    <?php if (is_logged_in()): ?>
                        <li><a href="user/dashboard.php">Dashboard</a></li>
                        <li><a href="user/application.php">Application</a></li>
                        <li><a href="user/uploads.php">Documents</a></li>
                        <li><a href="user/payments.php">Payments</a></li>
                        <li><a href="auth/logout.php">Logout</a></li>
                    <?php elseif (is_admin_logged_in()): ?>
                        <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
                        <li><a href="admin/applications.php">Applications</a></li>
                        <li><a href="admin/users.php">Users</a></li>
                        <li><a href="admin/reports.php">Reports</a></li>
                        <li><a href="admin/settings.php">Settings</a></li>
                        <li><a href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="programs.php">Programs</a></li>
                        <li><a href="auth/login.php">Login</a></li>
                        <li><a href="auth/register.php">Register</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <?php if ($flash_message): ?>
                <div class="alert alert-<?php echo $flash_message['type']; ?>">
                    <?php echo htmlspecialchars($flash_message['message']); ?>
                </div>
            <?php endif; ?>