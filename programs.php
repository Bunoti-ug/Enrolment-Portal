<?php
$page_title = 'Training Programs';
require_once 'includes/header.php';

$db = Database::getInstance();

// Get all active programs grouped by category
$programs = $db->fetchAll("SELECT * FROM programs WHERE is_active = 1 ORDER BY category, program_name");

// Group programs by category
$grouped_programs = [];
foreach ($programs as $program) {
    $grouped_programs[$program['category']][] = $program;
}
?>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">Training Programs</h1>
        <p>Comprehensive ICT training programs designed for professional development</p>
    </div>
    
    <?php foreach ($grouped_programs as $category => $category_programs): ?>
        <div class="program-category mb-5">
            <h2 class="text-xl font-semibold text-primary mb-4"><?php echo htmlspecialchars($category); ?></h2>
            
            <div class="program-grid">
                <?php foreach ($category_programs as $program): ?>
                    <div class="program-card">
                        <div class="program-header">
                            <h3 class="program-title"><?php echo htmlspecialchars($program['program_name']); ?></h3>
                            <div class="program-price"><?php echo format_currency($program['amount']); ?></div>
                        </div>
                        <div class="program-details">
                            <div class="program-duration">
                                <i class="ri-time-line"></i>
                                Duration: <?php echo htmlspecialchars($program['duration']); ?>
                            </div>
                        </div>
                        <div class="program-actions">
                            <?php if (is_logged_in()): ?>
                                <a href="user/application.php" class="btn btn-primary">Apply Now</a>
                            <?php else: ?>
                                <a href="auth/register.php" class="btn btn-primary">Register to Apply</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">How to Apply</h2>
    </div>
    
    <div class="application-steps">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <h3>Create Account</h3>
                <p>Register for a new account or login to your existing account</p>
            </div>
        </div>
        
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <h3>Complete Application</h3>
                <p>Fill out the application form with your personal and academic information</p>
            </div>
        </div>
        
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <h3>Upload Documents</h3>
                <p>Upload required documents including ID, certificates, and photos</p>
            </div>
        </div>
        
        <div class="step">
            <div class="step-number">4</div>
            <div class="step-content">
                <h3>Make Payment</h3>
                <p>Complete payment using Mobile Money, FlexiPay, or Pesapal</p>
            </div>
        </div>
        
        <div class="step">
            <div class="step-number">5</div>
            <div class="step-content">
                <h3>Start Learning</h3>
                <p>Receive confirmation and begin your training program</p>
            </div>
        </div>
    </div>
</div>

<style>
.program-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.program-card {
    background: white;
    border: 1px solid var(--medium-gray);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
}

.program-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
}

.program-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.program-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-color);
    flex: 1;
    margin-right: 1rem;
}

.program-price {
    font-size: 1.25rem;
    font-weight: bold;
    color: var(--primary-color);
    white-space: nowrap;
}

.program-details {
    margin-bottom: 1.5rem;
}

.program-duration {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--dark-gray);
    font-size: 0.875rem;
}

.program-actions {
    text-align: center;
}

.application-steps {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.step-number {
    width: 2.5rem;
    height: 2.5rem;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.step-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

.step-content p {
    color: var(--dark-gray);
    line-height: 1.5;
}

@media (max-width: 768px) {
    .program-grid {
        grid-template-columns: 1fr;
    }
    
    .program-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .program-title {
        margin-right: 0;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>