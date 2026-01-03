<?php
$pageTitle = 'Admin Dashboard - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Admin Dashboard</h2>
        <p>Welcome back, <?php echo $_SESSION['user_name']; ?>. Here's what's happening today.</p>
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
    
    <!-- System Alerts -->
    <?php if (!empty($alerts)): ?>
    <div class="alerts-container" style="margin-bottom: 30px;">
        <h3>System Alerts</h3>
        <div class="cards-container">
            <?php foreach ($alerts as $alert): ?>
            <?php if ($alert['count'] > 0): ?>
            <div class="card" style="border-left: 4px solid var(--warning-color);">
                <div class="card-body">
                    <h4><?php echo htmlspecialchars($alert['title']); ?></h4>
                    <p style="font-size: 1.5rem; font-weight: bold; color: var(--warning-color);">
                        <?php echo $alert['count']; ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon doctors">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['doctors']; ?></h3>
                <p>Active Doctors</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon patients">
                <i class="fas fa-user-injured"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['patients']; ?></h3>
                <p>Active Patients</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon appointments">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['appointments']; ?></h3>
                <p>Today's Appointments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon departments">
                <i class="fas fa-clinic-medical"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['departments']; ?></h3>
                <p>Departments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--secondary-color);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['receptionists']; ?></h3>
                <p>Receptionists</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--warning-color);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['pending_appointments']; ?></h3>
                <p>Pending Appointments</p>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="background-color: white; padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 30px;">
        <h3>Quick Actions</h3>
        <div style="display: flex; gap: 15px; margin-top: 20px; flex-wrap: wrap;">
            <a href="?page=admin-add-doctor" class="btn btn-primary">
                <i class="fas fa-user-md"></i> Add Doctor
            </a>
            <a href="?page=admin-add-user" class="btn btn-secondary">
                <i class="fas fa-user-plus"></i> Add User
            </a>
            <a href="?page=admin-add-department" class="btn btn-success">
                <i class="fas fa-clinic-medical"></i> Add Department
            </a>
            <a href="?page=admin-reports" class="btn btn-warning">
                <i class="fas fa-chart-bar"></i> View Reports
            </a>
            <a href="?page=admin-settings" class="btn btn-outline">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
    </div>
    
    <!-- Recent Appointments -->
    <div class="page-header">
        <h3>Recent Appointments</h3>
        <a href="?page=admin-appointments" class="btn btn-outline">
            <i class="fas fa-eye"></i> View All
        </a>
    </div>
    
    <?php if (empty($appointments)): ?>
    <div class="alert" style="background-color: var(--primary-light); padding: 20px; border-radius: var(--border-radius);">
        <p style="margin: 0;">No recent appointments found.</p>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                    <td>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($appointment['date'])); ?></td>
                    <td><?php echo date('h:i A', strtotime($appointment['time'])); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($appointment['status']); ?>">
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
                    </td>
                    <td>
                        <form action="?page=update-appointment-status&id=<?php echo $appointment['id']; ?>" method="POST" style="display: inline;">
                            <select name="status" onchange="this.form.submit()" class="form-control" style="width: auto; display: inline-block; padding: 5px;">
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
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>