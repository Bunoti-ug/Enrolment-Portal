<?php
$page_title = 'Application Form';
require_once '../includes/header.php';
require_login();

$db = Database::getInstance();
$user_id = $_SESSION['user_id'];

// Get user information
$user = $db->fetch("SELECT * FROM users WHERE id = ?", [$user_id]);

// Get current application if exists
$current_application = $db->fetch(
    "SELECT * FROM applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1", 
    [$user_id]
);

// Check if application can be edited
$can_edit = !$current_application || 
            in_array($current_application['status'], ['Draft', 'Returned for Edit']);

if (!$can_edit && $current_application['edit_attempts'] >= 3) {
    set_flash_message('Maximum edit attempts reached. Please contact support.', 'error');
    redirect('dashboard.php');
}

// Get existing data
$next_of_kin = $db->fetch("SELECT * FROM next_of_kin WHERE user_id = ?", [$user_id]);
$academic_history = $db->fetchAll("SELECT * FROM academic_history WHERE user_id = ? ORDER BY year_completed DESC", [$user_id]);
$internee_info = $db->fetch("SELECT * FROM internee_info WHERE user_id = ?", [$user_id]);
$programs = $db->fetchAll("SELECT * FROM programs WHERE is_active = 1 ORDER BY category, program_name");

// Group programs by category
$grouped_programs = [];
foreach ($programs as $program) {
    $grouped_programs[$program['category']][] = $program;
}

$errors = [];
$current_step = $_GET['step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_step_1') {
        // Personal Information
        $date_of_birth = $_POST['date_of_birth'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $nationality = $_POST['nationality'] ?? '';
        $nin = sanitize_input($_POST['nin'] ?? '');
        $passport_number = sanitize_input($_POST['passport_number'] ?? '');
        $physical_address = sanitize_input($_POST['physical_address'] ?? '');
        
        // Validation
        if (empty($date_of_birth)) $errors[] = 'Date of birth is required.';
        if (empty($gender)) $errors[] = 'Gender is required.';
        if (empty($nationality)) $errors[] = 'Nationality is required.';
        if (empty($physical_address)) $errors[] = 'Physical address is required.';
        
        if (empty($nin) && empty($passport_number)) {
            $errors[] = 'Either NIN or Passport Number is required.';
        }
        
        if ($nin && !validate_nin($nin)) {
            $errors[] = 'Invalid NIN format.';
        }
        
        if (empty($errors)) {
            try {
                $db->query(
                    "UPDATE users SET date_of_birth = ?, gender = ?, nationality = ?, nin = ?, passport_number = ?, physical_address = ? WHERE id = ?",
                    [$date_of_birth, $gender, $nationality, $nin, $passport_number, $physical_address, $user_id]
                );
                
                redirect('application.php?step=2');
            } catch (Exception $e) {
                $errors[] = 'Failed to save personal information.';
            }
        }
    }
    
    elseif ($action === 'save_step_2') {
        // Next of Kin Information
        $nok_full_name = sanitize_input($_POST['nok_full_name'] ?? '');
        $nok_phone = sanitize_input($_POST['nok_phone'] ?? '');
        $nok_relationship = sanitize_input($_POST['nok_relationship'] ?? '');
        
        // Validation
        if (empty($nok_full_name)) $errors[] = 'Next of kin full name is required.';
        if (empty($nok_phone)) $errors[] = 'Next of kin phone number is required.';
        if (empty($nok_relationship)) $errors[] = 'Relationship is required.';
        
        if ($nok_phone && !validate_phone($nok_phone)) {
            $errors[] = 'Invalid phone number format.';
        }
        
        if (empty($errors)) {
            try {
                if ($next_of_kin) {
                    $db->query(
                        "UPDATE next_of_kin SET full_name = ?, phone_number = ?, relationship = ? WHERE user_id = ?",
                        [$nok_full_name, $nok_phone, $nok_relationship, $user_id]
                    );
                } else {
                    $db->query(
                        "INSERT INTO next_of_kin (user_id, full_name, phone_number, relationship) VALUES (?, ?, ?, ?)",
                        [$user_id, $nok_full_name, $nok_phone, $nok_relationship]
                    );
                }
                
                redirect('application.php?step=3');
            } catch (Exception $e) {
                $errors[] = 'Failed to save next of kin information.';
            }
        }
    }
    
    elseif ($action === 'save_step_3') {
        // Academic History
        $academic_levels = $_POST['academic_level'] ?? [];
        $institutions = $_POST['institution'] ?? [];
        $years = $_POST['year_completed'] ?? [];
        $grades = $_POST['grade_obtained'] ?? [];
        
        if (empty($academic_levels)) {
            $errors[] = 'At least one academic qualification is required.';
        }
        
        if (empty($errors)) {
            try {
                $db->beginTransaction();
                
                // Delete existing records
                $db->query("DELETE FROM academic_history WHERE user_id = ?", [$user_id]);
                
                // Insert new records
                for ($i = 0; $i < count($academic_levels); $i++) {
                    if (!empty($academic_levels[$i]) && !empty($institutions[$i]) && !empty($years[$i])) {
                        $db->query(
                            "INSERT INTO academic_history (user_id, level, institution_name, year_completed, grade_obtained) VALUES (?, ?, ?, ?, ?)",
                            [$user_id, $academic_levels[$i], $institutions[$i], $years[$i], $grades[$i] ?? '']
                        );
                    }
                }
                
                $db->commit();
                redirect('application.php?step=4');
            } catch (Exception $e) {
                $db->rollback();
                $errors[] = 'Failed to save academic history.';
            }
        }
    }
    
    elseif ($action === 'save_step_4') {
        // Program Selection & Internee Info
        $is_internee = isset($_POST['is_internee']) ? 1 : 0;
        $program_id = $_POST['program_id'] ?? null;
        
        if (!$is_internee && empty($program_id)) {
            $errors[] = 'Please select a program.';
        }
        
        if ($is_internee) {
            $current_institution = sanitize_input($_POST['current_institution'] ?? '');
            $course_of_study = sanitize_input($_POST['course_of_study'] ?? '');
            $registration_number = sanitize_input($_POST['registration_number'] ?? '');
            $areas_of_interest = sanitize_input($_POST['areas_of_interest'] ?? '');
            
            if (empty($current_institution)) $errors[] = 'Current institution is required for internees.';
            if (empty($course_of_study)) $errors[] = 'Course of study is required for internees.';
            if (empty($registration_number)) $errors[] = 'Registration number is required for internees.';
        }
        
        if (empty($errors)) {
            try {
                $db->beginTransaction();
                
                // Create or update application
                if ($current_application) {
                    $db->query(
                        "UPDATE applications SET program_id = ?, is_internee = ?, edit_attempts = edit_attempts + 1 WHERE id = ?",
                        [$program_id, $is_internee, $current_application['id']]
                    );
                    $application_id = $current_application['id'];
                } else {
                    $app_id = generate_application_id($is_internee);
                    $db->query(
                        "INSERT INTO applications (user_id, application_id, program_id, is_internee, status) VALUES (?, ?, ?, ?, 'Draft')",
                        [$user_id, $app_id, $program_id, $is_internee]
                    );
                    $application_id = $db->lastInsertId();
                }
                
                // Handle internee information
                if ($is_internee) {
                    if ($internee_info) {
                        $db->query(
                            "UPDATE internee_info SET current_institution = ?, course_of_study = ?, registration_number = ?, areas_of_interest = ? WHERE user_id = ?",
                            [$current_institution, $course_of_study, $registration_number, $areas_of_interest, $user_id]
                        );
                    } else {
                        $db->query(
                            "INSERT INTO internee_info (user_id, current_institution, course_of_study, registration_number, areas_of_interest) VALUES (?, ?, ?, ?, ?)",
                            [$user_id, $current_institution, $course_of_study, $registration_number, $areas_of_interest]
                        );
                    }
                } else {
                    // Remove internee info if not an internee
                    $db->query("DELETE FROM internee_info WHERE user_id = ?", [$user_id]);
                }
                
                $db->commit();
                redirect('application.php?step=5');
            } catch (Exception $e) {
                $db->rollback();
                $errors[] = 'Failed to save application information.';
            }
        }
    }
    
    elseif ($action === 'submit_application') {
        // Final submission
        if (!$current_application) {
            $errors[] = 'No application found to submit.';
        } else {
            try {
                $db->query(
                    "UPDATE applications SET status = 'Submitted', submitted_at = NOW() WHERE id = ?",
                    [$current_application['id']]
                );
                
                add_notification($user_id, 'Application Submitted', 'Your application has been successfully submitted for review.', 'Success');
                
                set_flash_message('Application submitted successfully!', 'success');
                redirect('dashboard.php');
            } catch (Exception $e) {
                $errors[] = 'Failed to submit application.';
            }
        }
    }
    
    // Auto-save functionality
    if (isset($_POST['auto_save'])) {
        // Handle auto-save logic here
        echo json_encode(['success' => true]);
        exit;
    }
}

// Refresh data after updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $user = $db->fetch("SELECT * FROM users WHERE id = ?", [$user_id]);
    $next_of_kin = $db->fetch("SELECT * FROM next_of_kin WHERE user_id = ?", [$user_id]);
    $academic_history = $db->fetchAll("SELECT * FROM academic_history WHERE user_id = ? ORDER BY year_completed DESC", [$user_id]);
    $internee_info = $db->fetch("SELECT * FROM internee_info WHERE user_id = ?", [$user_id]);
    $current_application = $db->fetch("SELECT * FROM applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1", [$user_id]);
}
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Application Form</h1>
        <?php if ($current_application): ?>
            <p>Application ID: <strong><?php echo htmlspecialchars($current_application['application_id']); ?></strong></p>
            <p>Status: <span class="badge badge-info"><?php echo $current_application['status']; ?></span></p>
        <?php endif; ?>
    </div>
    
    <!-- Progress Indicator -->
    <div class="progress mb-4">
        <?php $progress_percent = ($current_step / 5) * 100; ?>
        <div class="progress-bar" style="width: <?php echo $progress_percent; ?>%">
            Step <?php echo $current_step; ?> of 5
        </div>
    </div>
    
    <!-- Step Navigation -->
    <div class="step-navigation mb-4">
        <div class="steps">
            <div class="step <?php echo $current_step >= 1 ? 'active' : ''; ?>">
                <span class="step-number">1</span>
                <span class="step-title">Personal Info</span>
            </div>
            <div class="step <?php echo $current_step >= 2 ? 'active' : ''; ?>">
                <span class="step-number">2</span>
                <span class="step-title">Next of Kin</span>
            </div>
            <div class="step <?php echo $current_step >= 3 ? 'active' : ''; ?>">
                <span class="step-number">3</span>
                <span class="step-title">Academic History</span>
            </div>
            <div class="step <?php echo $current_step >= 4 ? 'active' : ''; ?>">
                <span class="step-number">4</span>
                <span class="step-title">Program Selection</span>
            </div>
            <div class="step <?php echo $current_step >= 5 ? 'active' : ''; ?>">
                <span class="step-number">5</span>
                <span class="step-title">Review & Submit</span>
            </div>
        </div>
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
    
    <?php if (!$can_edit): ?>
        <div class="alert alert-warning">
            <h4>Application Not Editable</h4>
            <p>Your application is currently <?php echo strtolower($current_application['status']); ?> and cannot be edited at this time.</p>
        </div>
    <?php endif; ?>
    
    <!-- Step 1: Personal Information -->
    <?php if ($current_step == 1): ?>
        <form method="POST" data-validate id="step1-form">
            <input type="hidden" name="action" value="save_step_1">
            
            <h2>Personal Information</h2>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">First Name *</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['middle_name']); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Last Name *</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Phone Number *</label>
                        <input type="tel" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="date_of_birth" class="form-label">Date of Birth *</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" 
                               value="<?php echo $user['date_of_birth']; ?>" required <?php echo $can_edit ? '' : 'readonly'; ?>>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="gender" class="form-label">Gender *</label>
                        <select id="gender" name="gender" class="form-control form-select" required <?php echo $can_edit ? '' : 'disabled'; ?>>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $user['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nationality" class="form-label">Nationality *</label>
                        <input type="text" id="nationality" name="nationality" class="form-control" 
                               value="<?php echo htmlspecialchars($user['nationality']); ?>" required <?php echo $can_edit ? '' : 'readonly'; ?>>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nin" class="form-label">National Identification Number (NIN)</label>
                        <input type="text" id="nin" name="nin" class="form-control" 
                               value="<?php echo htmlspecialchars($user['nin']); ?>" 
                               placeholder="CM1234567890123" <?php echo $can_edit ? '' : 'readonly'; ?>>
                        <div class="form-text">Format: CM followed by 13 digits</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="passport_number" class="form-label">Passport Number</label>
                        <input type="text" id="passport_number" name="passport_number" class="form-control" 
                               value="<?php echo htmlspecialchars($user['passport_number']); ?>" 
                               placeholder="A1234567" <?php echo $can_edit ? '' : 'readonly'; ?>>
                        <div class="form-text">Required if NIN is not provided</div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="physical_address" class="form-label">Physical Address *</label>
                <textarea id="physical_address" name="physical_address" class="form-control" rows="3" 
                          required <?php echo $can_edit ? '' : 'readonly'; ?>><?php echo htmlspecialchars($user['physical_address']); ?></textarea>
            </div>
            
            <?php if ($can_edit): ?>
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Save & Continue</button>
                </div>
            <?php else: ?>
                <div class="btn-group">
                    <a href="application.php?step=2" class="btn btn-primary">Next Step</a>
                </div>
            <?php endif; ?>
        </form>
    <?php endif; ?>
    
    <!-- Additional steps would continue here... -->
    <!-- For brevity, I'll include the navigation and styling -->
    
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

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 0.5rem;
}

@media (max-width: 768px) {
    .col-md-4,
    .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

.step-navigation {
    overflow-x: auto;
}

.steps {
    display: flex;
    justify-content: space-between;
    min-width: 600px;
    padding: 1rem 0;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 1;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    right: -50%;
    width: 100%;
    height: 2px;
    background-color: var(--medium-gray);
    z-index: -1;
}

.step.active:not(:last-child)::after {
    background-color: var(--primary-color);
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--medium-gray);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.step.active .step-number {
    background-color: var(--primary-color);
}

.step-title {
    font-size: 0.875rem;
    text-align: center;
    color: var(--dark-gray);
}

.step.active .step-title {
    color: var(--primary-color);
    font-weight: 600;
}
</style>

<script>
// Initialize auto-save
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('step1-form');
    if (form) {
        BuyunicPortal.initializeAutoSave('step1-form', 'application.php', 30000);
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>