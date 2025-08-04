-- Buyunic Technologies Enrollment Portal Database Schema
-- Database: buyunicu_enrolment

CREATE DATABASE IF NOT EXISTS buyunicu_enrolment;
USE buyunicu_enrolment;

-- Users table for registration and authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    nin VARCHAR(50),
    passport_number VARCHAR(50),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    nationality VARCHAR(100),
    physical_address TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    otp_code VARCHAR(6),
    otp_expires_at TIMESTAMP NULL,
    reset_token VARCHAR(100),
    reset_expires_at TIMESTAMP NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Next of kin information
CREATE TABLE next_of_kin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    relationship VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Academic history
CREATE TABLE academic_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    level ENUM('PLE', 'UCE', 'UACE', 'Certificate', 'Diploma', 'Bachelors', 'Other') NOT NULL,
    institution_name VARCHAR(255) NOT NULL,
    year_completed YEAR NOT NULL,
    grade_obtained VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Internee specific information
CREATE TABLE internee_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    current_institution VARCHAR(255) NOT NULL,
    course_of_study VARCHAR(255) NOT NULL,
    registration_number VARCHAR(100) NOT NULL,
    areas_of_interest TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Training programs catalog
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    program_name VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    duration VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Applications
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    application_id VARCHAR(50) UNIQUE NOT NULL,
    program_id INT,
    is_internee BOOLEAN DEFAULT FALSE,
    status ENUM('Draft', 'Submitted', 'Under Review', 'Approved', 'Rejected', 'Returned for Edit') DEFAULT 'Draft',
    edit_attempts INT DEFAULT 0,
    admin_notes TEXT,
    submitted_at TIMESTAMP NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(id)
);

-- Document uploads
CREATE TABLE uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    application_id INT,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL,
    document_type ENUM('National ID', 'Passport', 'Passport Photo', 'Academic Certificate', 'Transcript', 'Internship Letter', 'Institutional ID', 'Disability Certificate', 'Other') NOT NULL,
    validation_status ENUM('Pending', 'Approved', 'Flagged') DEFAULT 'Pending',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Payments
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    application_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Mobile Money', 'FlexiPay', 'Pesapal', 'Other') NOT NULL,
    transaction_reference VARCHAR(255),
    payment_status ENUM('Pending', 'Confirmed', 'Failed') DEFAULT 'Pending',
    verification_flag BOOLEAN DEFAULT FALSE,
    transaction_logs TEXT,
    paid_at TIMESTAMP NULL,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Admin users
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('Super Admin', 'Admin', 'Viewer') DEFAULT 'Admin',
    mfa_enabled BOOLEAN DEFAULT TRUE,
    mfa_secret VARCHAR(100),
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin activity logs
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action_type VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    affected_user_id INT,
    affected_application_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
);

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('Info', 'Success', 'Warning', 'Error') DEFAULT 'Info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Messages between admin and applicants
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_type ENUM('Admin', 'User') NOT NULL,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    application_id INT,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- System settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default programs
INSERT INTO programs (category, program_name, amount, duration) VALUES
-- Microsoft Office Suite
('MICROSOFT OFFICE SUITE', 'General intro + MS Word (2-in-1)', 120000.00, '3 Wks (2 hrs/day)'),
('MICROSOFT OFFICE SUITE', 'Microsoft PowerPoint', 50000.00, '2 Wks (2 hrs/day)'),
('MICROSOFT OFFICE SUITE', 'Microsoft Excel', 60000.00, '2 Wks (2 hrs/day)'),
('MICROSOFT OFFICE SUITE', 'Microsoft Access/Database', 60000.00, '2 Wks (2 hrs/day)'),
('MICROSOFT OFFICE SUITE', 'Microsoft Publisher', 50000.00, '2 Wks (2 hrs/day)'),
('MICROSOFT OFFICE SUITE', 'Internet Basics', 50000.00, '2 Wks (2 hrs/day)'),

-- Graphics & Web Development
('GRAPHICS & WEB DEVELOPMENT', 'Adobe Photoshop', 550000.00, '4 Wks (2 hrs/day)'),
('GRAPHICS & WEB DEVELOPMENT', 'Adobe Illustrator', 550000.00, '4 Wks (2 hrs/day)'),
('GRAPHICS & WEB DEVELOPMENT', 'PageMaker', 250000.00, '4 Wks (2 hrs/day)'),
('GRAPHICS & WEB DEVELOPMENT', 'HTML Language', 600000.00, '4 Wks (2 hrs/day)'),
('GRAPHICS & WEB DEVELOPMENT', 'WordPress CMS', 500000.00, '4 Wks (2 hrs/day)'),
('GRAPHICS & WEB DEVELOPMENT', 'CorelDRAW', 550000.00, '4 Wks (2 hrs/day)'),

-- Specialized IT Programs
('SPECIALIZED IT PROGRAMS', 'Computer Networking', 550000.00, '6 Wks (2 hrs/day)'),
('SPECIALIZED IT PROGRAMS', 'Hardware/Software Installation & Troubleshooting', 300000.00, '3 Wks (2 hrs/day)'),
('SPECIALIZED IT PROGRAMS', 'CCTV Camera Installation & Config', 550000.00, '3 Wks (2 hrs/day)'),
('SPECIALIZED IT PROGRAMS', 'Tally Accounting Software', 550000.00, '6 Wks (2 hrs/day)'),
('SPECIALIZED IT PROGRAMS', 'QuickBooks', 550000.00, '6 Wks (2 hrs/day)'),
('SPECIALIZED IT PROGRAMS', 'Epinfo', 550000.00, '6 Wks (2 hrs/day)'),
('SPECIALIZED IT PROGRAMS', 'SPSS (Statistical Package)', 550000.00, '6 Wks (2 hrs/day)'),
('SPECIALIZED IT PROGRAMS', 'Stata', 350000.00, '6 Wks (2 hrs/day)'),
('SPECIALIZED IT PROGRAMS', 'Epi Data', 350000.00, '6 Wks (2 hrs/day)'),

-- Internship & Research
('INTERNSHIP & RESEARCH', 'Custom IT Project / Research-Based Training', 400000.00, '2 Months'),

-- Registration
('REGISTRATION', 'Registration Fee', 30000.00, '—');

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@buyunic.ug', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'Super Admin');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_name', 'Buyunic Technologies Enrollment Portal', 'Site name'),
('contact_email', 'apply@buyunic.ug', 'Primary contact email'),
('contact_phone', '+256 207 901 434', 'Contact phone number'),
('max_file_size', '1048576', 'Maximum file size in bytes (1MB)'),
('max_files_per_user', '10', 'Maximum files per user'),
('session_timeout', '900', 'Session timeout in seconds (15 minutes)'),
('max_login_attempts', '5', 'Maximum login attempts before lockout'),
('lockout_duration', '1800', 'Account lockout duration in seconds (30 minutes)'),
('otp_expiry', '300', 'OTP expiry time in seconds (5 minutes)'),
('reset_token_expiry', '3600', 'Password reset token expiry in seconds (1 hour)');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_otp ON users(otp_code, otp_expires_at);
CREATE INDEX idx_users_reset_token ON users(reset_token, reset_expires_at);
CREATE INDEX idx_applications_user_id ON applications(user_id);
CREATE INDEX idx_applications_status ON applications(status);
CREATE INDEX idx_uploads_user_id ON uploads(user_id);
CREATE INDEX idx_payments_user_id ON payments(user_id);
CREATE INDEX idx_payments_status ON payments(payment_status);
CREATE INDEX idx_admin_logs_admin_id ON admin_logs(admin_id);
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_messages_recipient ON messages(recipient_id, is_read);