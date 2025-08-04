-- Buyunic Technologies Enrollment Portal Database Schema
-- Created: <?php echo date('Y-m-d H:i:s'); ?>

CREATE DATABASE IF NOT EXISTS buyunicu_enrolment;
USE buyunicu_enrolment;

-- Users table (Applicants)
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

-- Next of Kin table
CREATE TABLE next_of_kin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    relationship VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    physical_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Academic History table
CREATE TABLE academic_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    institution_name VARCHAR(255) NOT NULL,
    qualification VARCHAR(100) NOT NULL,
    field_of_study VARCHAR(100),
    start_year YEAR NOT NULL,
    end_year YEAR,
    grade_obtained VARCHAR(50),
    is_current BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Internee Information table
CREATE TABLE internee_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    current_employer VARCHAR(255),
    job_title VARCHAR(100),
    work_experience_years INT DEFAULT 0,
    skills TEXT,
    linkedin_profile VARCHAR(255),
    github_profile VARCHAR(255),
    portfolio_website VARCHAR(255),
    career_objectives TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Training Programs table
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100) NOT NULL,
    duration_weeks INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    max_participants INT DEFAULT 20,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Applications table
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    program_id INT NOT NULL,
    is_internee BOOLEAN DEFAULT FALSE,
    status ENUM('Draft', 'Submitted', 'Under Review', 'Approved', 'Rejected', 'Withdrawn') DEFAULT 'Draft',
    submission_attempts INT DEFAULT 0,
    max_attempts INT DEFAULT 3,
    submitted_at TIMESTAMP NULL,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    review_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE RESTRICT,
    FOREIGN KEY (reviewed_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Document Uploads table
CREATE TABLE document_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    document_type ENUM('Passport Photo', 'Academic Certificates', 'Transcripts', 'CV/Resume', 'Cover Letter', 'ID Copy', 'Other') NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    upload_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    rejection_reason TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Payments table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Mobile Money', 'Bank Transfer', 'Cash', 'Card') NOT NULL,
    payment_reference VARCHAR(100),
    transaction_id VARCHAR(100),
    status ENUM('Pending', 'Completed', 'Failed', 'Refunded') DEFAULT 'Pending',
    payment_date TIMESTAMP NULL,
    verified_by INT NULL,
    verification_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Admin Users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('Super Admin', 'Admin', 'Reviewer') DEFAULT 'Admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    mfa_secret VARCHAR(32),
    mfa_enabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin Activity Logs table
CREATE TABLE admin_activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    affected_user_id INT NULL,
    affected_application_id INT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    FOREIGN KEY (affected_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (affected_application_id) REFERENCES applications(id) ON DELETE SET NULL
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('Info', 'Success', 'Warning', 'Error') DEFAULT 'Info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Messages table (between admin and users)
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_type ENUM('User', 'Admin') NOT NULL,
    sender_id INT NOT NULL,
    recipient_type ENUM('User', 'Admin') NOT NULL,
    recipient_id INT NOT NULL,
    application_id INT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE SET NULL
);

-- System Settings table
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_verification ON users(is_verified, otp_expires_at);
CREATE INDEX idx_applications_user ON applications(user_id);
CREATE INDEX idx_applications_status ON applications(status);
CREATE INDEX idx_applications_submitted ON applications(submitted_at);
CREATE INDEX idx_document_uploads_application ON document_uploads(application_id);
CREATE INDEX idx_payments_application ON payments(application_id);
CREATE INDEX idx_notifications_user ON notifications(user_id, is_read);
CREATE INDEX idx_admin_logs_admin ON admin_activity_logs(admin_id);
CREATE INDEX idx_messages_recipient ON messages(recipient_type, recipient_id, is_read);

-- Insert default training programs
INSERT INTO programs (name, description, category, duration_weeks, amount) VALUES
('Web Development Fundamentals', 'Learn HTML, CSS, JavaScript, and basic web development concepts', 'Web Development', 12, 500000.00),
('Advanced JavaScript & React', 'Master modern JavaScript ES6+ and React framework', 'Web Development', 16, 750000.00),
('Full Stack Development', 'Complete web development with frontend and backend technologies', 'Web Development', 24, 1200000.00),
('Mobile App Development', 'Build mobile applications using React Native and Flutter', 'Mobile Development', 20, 900000.00),
('Data Science & Analytics', 'Learn Python, data analysis, machine learning basics', 'Data Science', 18, 800000.00),
('Digital Marketing', 'Social media marketing, SEO, content marketing strategies', 'Marketing', 10, 400000.00),
('Graphic Design', 'Adobe Creative Suite, design principles, branding', 'Design', 14, 600000.00),
('Project Management', 'Agile, Scrum, project planning and execution', 'Management', 8, 350000.00),
('Cybersecurity Basics', 'Network security, ethical hacking, security protocols', 'Security', 16, 700000.00),
('Database Administration', 'MySQL, PostgreSQL, database design and optimization', 'Database', 12, 550000.00);

-- Insert default admin user
INSERT INTO admin_users (username, email, password_hash, first_name, last_name, role) VALUES
('admin', 'admin@buyunic.ug', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'Super Admin');

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('site_name', 'Buyunic Technologies Enrollment Portal', 'Website name'),
('contact_email', 'apply@buyunic.ug', 'Main contact email'),
('contact_phone', '+256 207 901 434', 'Main contact phone'),
('max_file_size', '5242880', 'Maximum file upload size in bytes (5MB)'),
('allowed_file_types', 'pdf,doc,docx,jpg,jpeg,png', 'Allowed file extensions'),
('application_deadline', '2024-12-31', 'Application deadline date'),
('enrollment_open', '1', 'Whether enrollment is currently open (1=yes, 0=no)');