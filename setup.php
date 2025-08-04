<?php
// Buyunic Technologies Enrollment Portal Setup Script
// Run this file once to set up the database and check system requirements

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Buyunic Technologies Enrollment Portal Setup</h1>";

// Check PHP version
echo "<h2>System Requirements Check</h2>";
echo "<p>PHP Version: " . PHP_VERSION . " ";
if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
    echo "<span style='color: green;'>✓ OK</span></p>";
} else {
    echo "<span style='color: red;'>✗ Requires PHP 8.0 or higher</span></p>";
}

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'fileinfo'];
foreach ($required_extensions as $ext) {
    echo "<p>Extension {$ext}: ";
    if (extension_loaded($ext)) {
        echo "<span style='color: green;'>✓ OK</span></p>";
    } else {
        echo "<span style='color: red;'>✗ Missing</span></p>";
    }
}

// Database setup
echo "<h2>Database Setup</h2>";

try {
    require_once 'config/database.php';
    
    $db = Database::getInstance();
    echo "<p>Database connection: <span style='color: green;'>✓ Connected</span></p>";
    
    // Read and execute schema
    $schema = file_get_contents('database/schema.sql');
    if ($schema) {
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $db->getConnection()->exec($statement);
                } catch (PDOException $e) {
                    // Ignore table exists errors
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "<p style='color: orange;'>Warning: " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
        
        echo "<p>Database schema: <span style='color: green;'>✓ Created/Updated</span></p>";
    } else {
        echo "<p>Database schema: <span style='color: red;'>✗ Schema file not found</span></p>";
    }
    
    // Check if tables exist
    $tables = ['users', 'programs', 'applications', 'admin_users'];
    foreach ($tables as $table) {
        $result = $db->fetch("SHOW TABLES LIKE '{$table}'");
        echo "<p>Table {$table}: ";
        if ($result) {
            echo "<span style='color: green;'>✓ Exists</span></p>";
        } else {
            echo "<span style='color: red;'>✗ Missing</span></p>";
        }
    }
    
    // Check if admin user exists
    $admin = $db->fetch("SELECT id FROM admin_users WHERE username = 'admin'");
    echo "<p>Default admin user: ";
    if ($admin) {
        echo "<span style='color: green;'>✓ Exists (username: admin, password: admin123)</span></p>";
    } else {
        echo "<span style='color: red;'>✗ Missing</span></p>";
    }
    
} catch (Exception $e) {
    echo "<p>Database connection: <span style='color: red;'>✗ Failed - " . $e->getMessage() . "</span></p>";
    echo "<p><strong>Please check your database configuration in config/database.php</strong></p>";
}

// Directory structure check
echo "<h2>Directory Structure Check</h2>";
$required_dirs = [
    'assets/css',
    'assets/js', 
    'assets/img',
    'uploads',
    'user',
    'admin',
    'auth',
    'includes',
    'config',
    'database'
];

foreach ($required_dirs as $dir) {
    echo "<p>Directory {$dir}: ";
    if (is_dir($dir)) {
        echo "<span style='color: green;'>✓ Exists</span>";
        if (is_writable($dir)) {
            echo " <span style='color: green;'>✓ Writable</span>";
        } else {
            echo " <span style='color: orange;'>⚠ Not writable</span>";
        }
    } else {
        if (mkdir($dir, 0755, true)) {
            echo "<span style='color: green;'>✓ Created</span>";
        } else {
            echo "<span style='color: red;'>✗ Failed to create</span>";
        }
    }
    echo "</p>";
}

// File permissions check
echo "<h2>File Permissions</h2>";
$writable_dirs = ['uploads', 'assets/img'];
foreach ($writable_dirs as $dir) {
    if (is_dir($dir)) {
        echo "<p>Directory {$dir}: ";
        if (is_writable($dir)) {
            echo "<span style='color: green;'>✓ Writable</span></p>";
        } else {
            echo "<span style='color: red;'>✗ Not writable - Please set permissions to 755</span></p>";
        }
    }
}

echo "<h2>Setup Complete</h2>";
echo "<p><strong>If all checks pass, your system is ready!</strong></p>";
echo "<p><a href='index.php' style='background: #2c5282; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Portal</a></p>";
echo "<p><em>You can delete this setup.php file after successful setup.</em></p>";

// Add some basic styling
echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
h1, h2 { color: #2c5282; }
p { margin: 5px 0; }
</style>";
?>