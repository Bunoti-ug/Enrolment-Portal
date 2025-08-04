<?php
require_once 'functions.php';

start_secure_session();

// Check if user is logged in
if (!is_logged_in() && !is_admin_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Extend session by regenerating session ID
session_regenerate_id(true);

echo json_encode(['success' => true, 'message' => 'Session extended']);
?>