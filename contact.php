<?php
$page_title = 'Contact Us';
require_once 'includes/header.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    // Validation
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    elseif (!validate_email($email)) $errors[] = 'Please enter a valid email address.';
    if (empty($subject)) $errors[] = 'Subject is required.';
    if (empty($message)) $errors[] = 'Message is required.';
    
    if (empty($errors)) {
        // Send email to admin
        $email_subject = "Contact Form: " . $subject;
        $email_message = "
        <html>
        <head><title>Contact Form Submission</title></head>
        <body>
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <p><strong>Message:</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
            <hr>
            <p><em>Sent from Buyunic Technologies Enrollment Portal</em></p>
        </body>
        </html>";
        
        if (send_email('apply@buyunic.ug', $email_subject, $email_message, $email)) {
            $success = true;
            // Clear form data
            $_POST = [];
        } else {
            $errors[] = 'Failed to send message. Please try again or contact us directly.';
        }
    }
}
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Get In Touch</h1>
                <p>Have questions about our programs? We're here to help!</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <h4>Message Sent Successfully!</h4>
                    <p>Thank you for contacting us. We'll get back to you within 24 hours.</p>
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" id="name" name="name" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                                       placeholder="+256 or 0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject" class="form-label">Subject *</label>
                                <select id="subject" name="subject" class="form-control form-select" required>
                                    <option value="">Select Subject</option>
                                    <option value="General Inquiry" <?php echo ($_POST['subject'] ?? '') === 'General Inquiry' ? 'selected' : ''; ?>>General Inquiry</option>
                                    <option value="Program Information" <?php echo ($_POST['subject'] ?? '') === 'Program Information' ? 'selected' : ''; ?>>Program Information</option>
                                    <option value="Application Support" <?php echo ($_POST['subject'] ?? '') === 'Application Support' ? 'selected' : ''; ?>>Application Support</option>
                                    <option value="Payment Issues" <?php echo ($_POST['subject'] ?? '') === 'Payment Issues' ? 'selected' : ''; ?>>Payment Issues</option>
                                    <option value="Technical Support" <?php echo ($_POST['subject'] ?? '') === 'Technical Support' ? 'selected' : ''; ?>>Technical Support</option>
                                    <option value="Other" <?php echo ($_POST['subject'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="6" 
                                  placeholder="Please describe your inquiry in detail..." required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Contact Information</h2>
            </div>
            
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="ri-map-pin-line"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Location</h3>
                        <p>Plot 28, North Road<br>Northern City Division<br>Mbale City, Uganda</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="ri-phone-line"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Phone</h3>
                        <p>+256 394 839 851</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="ri-whatsapp-line"></i>
                    </div>
                    <div class="contact-details">
                        <h3>WhatsApp</h3>
                        <p>+256 207 901 434</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="ri-mail-line"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Email</h3>
                        <p>info@buyunic.ug<br>apply@buyunic.ug</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Office Hours</h3>
                        <p>Monday - Friday: 8:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 4:00 PM<br>Sunday: Closed</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quick Links</h2>
            </div>
            
            <div class="quick-links">
                <?php if (!is_logged_in()): ?>
                    <a href="auth/register.php" class="quick-link">
                        <i class="ri-user-add-line"></i>
                        <span>Create Account</span>
                    </a>
                    <a href="auth/login.php" class="quick-link">
                        <i class="ri-login-box-line"></i>
                        <span>Login</span>
                    </a>
                <?php else: ?>
                    <a href="user/dashboard.php" class="quick-link">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="user/application.php" class="quick-link">
                        <i class="ri-file-text-line"></i>
                        <span>Application</span>
                    </a>
                <?php endif; ?>
                <a href="programs.php" class="quick-link">
                    <i class="ri-book-line"></i>
                    <span>Training Programs</span>
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Follow Us</h2>
            </div>
            
            <div class="social-links">
                <a href="https://www.facebook.com/buyunicug/" target="_blank" rel="noopener noreferrer" class="social-link facebook">
                    <i class="ri-facebook-fill"></i>
                    <span>Facebook</span>
                </a>
                <a href="https://x.com/buyunict" target="_blank" rel="noopener noreferrer" class="social-link twitter">
                    <i class="ri-twitter-fill"></i>
                    <span>Twitter</span>
                </a>
                <a href="https://www.youtube.com/@buyunic" target="_blank" rel="noopener noreferrer" class="social-link youtube">
                    <i class="ri-youtube-fill"></i>
                    <span>YouTube</span>
                </a>
                <a href="https://instagram.com/buyunic" target="_blank" rel="noopener noreferrer" class="social-link instagram">
                    <i class="ri-instagram-fill"></i>
                    <span>Instagram</span>
                </a>
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

.col-md-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
    padding: 0.5rem;
}

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
    padding: 0.5rem;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 0.5rem;
}

@media (max-width: 768px) {
    .col-md-8,
    .col-md-4,
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.contact-icon {
    width: 2.5rem;
    height: 2.5rem;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.contact-details h3 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 0.25rem;
}

.contact-details p {
    color: var(--dark-gray);
    font-size: 0.875rem;
    line-height: 1.4;
    margin: 0;
}

.quick-links {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quick-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: var(--text-color);
    text-decoration: none;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.quick-link:hover {
    background-color: var(--light-gray);
    color: var(--primary-color);
}

.social-links {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.social-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: white;
    text-decoration: none;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.social-link.facebook {
    background-color: #1877f2;
}

.social-link.twitter {
    background-color: #1da1f2;
}

.social-link.youtube {
    background-color: #ff0000;
}

.social-link.instagram {
    background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
}

.social-link:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}
</style>

<?php require_once 'includes/footer.php'; ?>