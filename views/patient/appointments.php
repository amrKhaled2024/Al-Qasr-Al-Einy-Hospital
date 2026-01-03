<?php
$pageTitle = 'My Appointments - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>My Appointments</h2>
        <a href="?page=patient-book" class="btn btn-primary">
            <i class="fas fa-plus"></i> Book New Appointment
        </a>
    </div>
    
    <?php if (!empty($successMessage)): ?>
    <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; color: #155724; border-radius: 5px;">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; border-radius: 5px;">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (empty($appointments)): ?>
    <div class="alert" style="background-color: #e8f4fc; padding: 20px; border-radius: 10px; text-align: center;">
        <p style="margin: 0 0 15px 0;">You don't have any appointments yet.</p>
        <a href="?page=patient-book" class="btn btn-primary">
            <i class="fas fa-calendar-plus"></i> Book Your First Appointment
        </a>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo date('M d, Y', strtotime($appointment['date'])); ?></td>
                    <td><?php echo date('h:i A', strtotime($appointment['time'])); ?></td>
                    <td>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($appointment['status']); ?>">
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed'): ?>
                        <form action="?page=cancel-appointment&id=<?php echo $appointment['id']; ?>" method="POST" style="display: inline;">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </form>
                        <?php else: ?>
                        <span class="text-muted">No actions available</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>