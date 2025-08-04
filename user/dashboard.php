<?php
$page_title = 'Dashboard';
require_once '../includes/header.php';
require_login();

$db = Database::getInstance();
$user_id = $_SESSION['user_id'];

// Get user information
$user = $db->fetch(
    "SELECT u.*, 
            (SELECT COUNT(*) FROM applications WHERE user_id = u.id) as total_applications,
            (SELECT COUNT(*) FROM uploads WHERE user_id = u.id) as total_uploads,
            (SELECT COUNT(*) FROM payments WHERE user_id = u.id AND payment_status = 'Confirmed') as confirmed_payments
     FROM users u WHERE u.id = ?", 
    [$user_id]
);

// Get current application
$current_application = $db->fetch(
    "SELECT a.*, p.program_name, p.amount 
     FROM applications a 
     LEFT JOIN programs p ON a.program_id = p.id 
     WHERE a.user_id = ? 
     ORDER BY a.created_at DESC 
     LIMIT 1", 
    [$user_id]
);

// Get recent notifications
$notifications = get_user_notifications($user_id, false);
$unread_notifications = array_filter($notifications, function($n) { return !$n['is_read']; });

// Get recent uploads
$recent_uploads = $db->fetchAll(
    "SELECT * FROM uploads WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 5", 
    [$user_id]
);

// Get payment status
$payment_status = null;
if ($current_application) {
    $payment_status = $db->fetch(
        "SELECT * FROM payments WHERE application_id = ? ORDER BY created_at DESC LIMIT 1",
        [$current_application['id']]
    );
}
?>

<div class="dashboard-stats">
    <div class="stat-card">
        <span class="stat-number"><?php echo $user['total_applications']; ?></span>
        <span class="stat-label">Applications</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo $user['total_uploads']; ?></span>
        <span class="stat-label">Documents Uploaded</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo $user['confirmed_payments']; ?></span>
        <span class="stat-label">Confirmed Payments</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?php echo count($unread_notifications); ?></span>
        <span class="stat-label">Unread Notifications</span>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Current Application Status -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Application Status</h2>
            </div>
            
            <?php if ($current_application): ?>
                <div class="application-status">
                    <div class="d-flex justify-between align-center mb-3">
                        <div>
                            <h3><?php echo htmlspecialchars($current_application['program_name'] ?? 'Internship Program'); ?></h3>
                            <p class="text-sm">Application ID: <strong><?php echo htmlspecialchars($current_application['application_id']); ?></strong></p>
                        </div>
                        <div>
                            <?php
                            $status_class = match($current_application['status']) {
                                'Draft' => 'badge-secondary',
                                'Submitted' => 'badge-info',
                                'Under Review' => 'badge-warning',
                                'Approved' => 'badge-success',
                                'Rejected' => 'badge-error',
                                'Returned for Edit' => 'badge-warning',
                                default => 'badge-secondary'
                            };
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $current_application['status']; ?></span>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <?php
                    $progress = match($current_application['status']) {
                        'Draft' => 20,
                        'Submitted' => 40,
                        'Under Review' => 60,
                        'Approved' => 100,
                        'Rejected' => 100,
                        'Returned for Edit' => 30,
                        default => 0
                    };
                    ?>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $progress; ?>%">
                            <?php echo $progress; ?>%
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <?php if ($current_application['status'] === 'Draft'): ?>
                            <p>Complete your application to submit it for review.</p>
                            <a href="application.php" class="btn btn-primary">Continue Application</a>
                        <?php elseif ($current_application['status'] === 'Submitted'): ?>
                            <p>Your application has been submitted and is waiting for review.</p>
                        <?php elseif ($current_application['status'] === 'Under Review'): ?>
                            <p>Your application is currently being reviewed by our admissions team.</p>
                        <?php elseif ($current_application['status'] === 'Approved'): ?>
                            <p>Congratulations! Your application has been approved.</p>
                            <?php if ($payment_status && $payment_status['payment_status'] === 'Confirmed'): ?>
                                <a href="certificate.php" class="btn btn-success">Download Certificate</a>
                            <?php else: ?>
                                <a href="payments.php" class="btn btn-warning">Complete Payment</a>
                            <?php endif; ?>
                        <?php elseif ($current_application['status'] === 'Rejected'): ?>
                            <p>Unfortunately, your application was not approved. You can apply for other programs.</p>
                            <a href="application.php" class="btn btn-primary">New Application</a>
                        <?php elseif ($current_application['status'] === 'Returned for Edit'): ?>
                            <p>Your application needs some corrections. Please review and resubmit.</p>
                            <a href="application.php" class="btn btn-warning">Edit Application</a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($current_application['admin_notes']): ?>
                        <div class="alert alert-info mt-3">
                            <h4>Admin Notes:</h4>
                            <p><?php echo nl2br(htmlspecialchars($current_application['admin_notes'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center p-4">
                    <h3>No Application Yet</h3>
                    <p>Start your journey with Buyunic Technologies by creating your first application.</p>
                    <a href="application.php" class="btn btn-primary btn-lg">Start Application</a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Activity</h2>
            </div>
            
            <?php if (!empty($recent_uploads)): ?>
                <div class="activity-list">
                    <?php foreach ($recent_uploads as $upload): ?>
                        <div class="activity-item d-flex justify-between align-center p-3">
                            <div>
                                <strong>Document Uploaded</strong>
                                <p class="text-sm"><?php echo htmlspecialchars($upload['original_filename']); ?></p>
                                <p class="text-sm text-muted"><?php echo time_ago($upload['uploaded_at']); ?></p>
                            </div>
                            <div>
                                <?php
                                $validation_class = match($upload['validation_status']) {
                                    'Approved' => 'badge-success',
                                    'Flagged' => 'badge-error',
                                    default => 'badge-warning'
                                };
                                ?>
                                <span class="badge <?php echo $validation_class; ?>"><?php echo $upload['validation_status']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center p-4">No recent activity</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quick Actions</h2>
            </div>
            
            <div class="quick-actions">
                <a href="application.php" class="btn btn-primary w-full mb-2">
                    <?php echo $current_application ? 'View Application' : 'Start Application'; ?>
                </a>
                <a href="uploads.php" class="btn btn-secondary w-full mb-2">Upload Documents</a>
                <a href="payments.php" class="btn btn-warning w-full mb-2">Payment Status</a>
                <a href="profile.php" class="btn btn-outline w-full">Update Profile</a>
            </div>
        </div>
        
        <!-- Notifications -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Notifications</h2>
                <?php if (!empty($unread_notifications)): ?>
                    <span class="badge badge-error"><?php echo count($unread_notifications); ?> unread</span>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($notifications)): ?>
                <div class="notification-list">
                    <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                        <div class="notification-item p-3 <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                            <h4 class="font-semibold"><?php echo htmlspecialchars($notification['title']); ?></h4>
                            <p class="text-sm"><?php echo htmlspecialchars($notification['message']); ?></p>
                            <p class="text-sm text-muted"><?php echo time_ago($notification['created_at']); ?></p>
                            <?php if (!$notification['is_read']): ?>
                                <button onclick="markAsRead(<?php echo $notification['id']; ?>)" class="btn btn-sm btn-outline">Mark as Read</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($notifications) > 5): ?>
                    <div class="text-center p-3">
                        <a href="notifications.php" class="btn btn-outline">View All Notifications</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-center p-4">No notifications</p>
            <?php endif; ?>
        </div>
        
        <!-- Help & Support -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Help & Support</h2>
            </div>
            
            <div class="help-links">
                <a href="help.php" class="d-block p-2">📚 Application Guide</a>
                <a href="faq.php" class="d-block p-2">❓ Frequently Asked Questions</a>
                <a href="contact.php" class="d-block p-2">📞 Contact Support</a>
                <a href="programs.php" class="d-block p-2">📋 Available Programs</a>
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

@media (max-width: 768px) {
    .col-md-8,
    .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

.activity-item {
    border-bottom: 1px solid var(--medium-gray);
}

.activity-item:last-child {
    border-bottom: none;
}

.notification-item {
    border-bottom: 1px solid var(--medium-gray);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background-color: rgba(74, 144, 226, 0.05);
}

.quick-actions .btn {
    margin-bottom: 0.5rem;
}

.help-links a {
    color: var(--text-color);
    text-decoration: none;
    border-bottom: 1px solid var(--medium-gray);
}

.help-links a:hover {
    background-color: var(--light-gray);
}

.help-links a:last-child {
    border-bottom: none;
}
</style>

<script>
function markAsRead(notificationId) {
    fetch('ajax/mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>