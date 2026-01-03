<?php
$pageTitle = 'Doctor Dashboard - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Doctor Dashboard</h2>
        <p>Welcome back, Dr. <?php echo $_SESSION['user_name']; ?>. Here's your schedule for today.</p>
    </div>
    
    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; color: #155724; border-radius: 5px;">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        <?php unset($_SESSION['success_message']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; border-radius: 5px;">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--primary-color);">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['today_appointments']; ?></h3>
                <p>Today's Appointments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--secondary-color);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_appointments']; ?></h3>
                <p>Total Appointments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--warning-color);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['pending_appointments']; ?></h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--success-color);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['completed_appointments']; ?></h3>
                <p>Completed</p>
            </div>
        </div>
    </div>
    
    <!-- Doctor Information -->
    <div style="background-color: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background-color: var(--primary-light); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fas fa-user-md"></i>
            </div>
            <div>
                <h3 style="margin-bottom: 5px;">Dr. <?php echo htmlspecialchars($doctorInfo['name'] ?? $_SESSION['user_name']); ?></h3>
                <p style="color: var(--primary-color); margin-bottom: 5px;">
                    <i class="fas fa-stethoscope"></i> 
                    <?php echo htmlspecialchars($doctorInfo['specialization'] ?? 'General Practitioner'); ?>
                </p>
                <p style="color: var(--text-light); font-size: 0.9rem;">
                    <i class="fas fa-hospital"></i> 
                    <?php echo htmlspecialchars($doctorInfo['department_name'] ?? 'No department assigned'); ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Today's Appointments -->
    <div class="page-header" style="margin-top: 40px;">
        <h3>Today's Appointments</h3>
        <a href="?page=doctor-appointments" class="btn btn-outline">
            <i class="fas fa-calendar-alt"></i> View All Appointments
        </a>
    </div>
    
    <?php if (empty($todayAppointments)): ?>
    <div class="alert" style="background-color: var(--primary-light); padding: 20px; border-radius: var(--border-radius);">
        <p style="margin: 0;">No appointments scheduled for today.</p>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Patient Name</th>
                    <th>Contact</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($todayAppointments as $appointment): ?>
                <tr>
                    <td><?php echo date('h:i A', strtotime($appointment['time'])); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($appointment['patient_name']); ?></strong>
                    </td>
                    <td>
                        <small><?php echo htmlspecialchars($appointment['patient_phone'] ?? 'N/A'); ?><br>
                        <?php echo htmlspecialchars($appointment['patient_email'] ?? ''); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($appointment['status']); ?>">
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
                    </td>
                    <td>
                        <form action="?page=update-appointment-status" method="POST" style="display: inline-block;">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                            <select name="status" class="form-control" style="width: auto; display: inline-block; padding: 5px;" 
                                    onchange="if(confirm('Change appointment status?')) this.form.submit()">
                                <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $appointment['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Upcoming Appointments -->
    <?php if (!empty($upcomingAppointments)): ?>
    <div class="page-header" style="margin-top: 40px;">
        <h3>Upcoming Appointments (Next 7 Days)</h3>
    </div>
    
    <div class="cards-container">
        <?php foreach ($upcomingAppointments as $appointment): ?>
        <div class="card">
            <div class="card-header">
                <h4><?php echo $appointment['formatted_date']; ?></h4>
                <small><?php echo date('h:i A', strtotime($appointment['time'])); ?></small>
            </div>
            <div class="card-body">
                <h5><?php echo htmlspecialchars($appointment['patient_name']); ?></h5>
                <p style="font-size: 0.9rem; color: var(--text-light);">
                    <?php echo htmlspecialchars(substr($appointment['reason'] ?? 'No reason specified', 0, 50)); ?>...
                </p>
                <span class="status <?php echo strtolower($appointment['status']); ?>" style="margin-top: 10px;">
                    <?php echo ucfirst($appointment['status']); ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Quick Actions -->
    <div style="margin-top: 40px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <a href="?page=doctor-appointments" class="btn btn-primary" style="text-align: center; padding: 15px;">
            <i class="fas fa-calendar-alt fa-2x"></i><br>
            <span>All Appointments</span>
        </a>
        <a href="?page=doctor-schedule" class="btn btn-secondary" style="text-align: center; padding: 15px;">
            <i class="fas fa-clock fa-2x"></i><br>
            <span>My Schedule</span>
        </a>
        <a href="?page=doctor-patients" class="btn btn-success" style="text-align: center; padding: 15px;">
            <i class="fas fa-user-injured fa-2x"></i><br>
            <span>My Patients</span>
        </a>
        <a href="?page=doctor-profile" class="btn btn-warning" style="text-align: center; padding: 15px;">
            <i class="fas fa-user-cog fa-2x"></i><br>
            <span>My Profile</span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh page every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 5 * 60 * 1000);
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>