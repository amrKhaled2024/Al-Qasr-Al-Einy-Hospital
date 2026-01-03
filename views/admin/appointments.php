<?php
$pageTitle = 'Manage Appointments - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Manage Appointments</h2>
    </div>
    
    <!-- Appointment Statistics -->
    <div class="stats-cards" style="margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon appointments">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total'] ?? 0; ?></h3>
                <p>Total Appointments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--warning-color);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['pending'] ?? 0; ?></h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--success-color);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['confirmed'] ?? 0; ?></h3>
                <p>Confirmed</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--danger-color);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['cancelled'] ?? 0; ?></h3>
                <p>Cancelled</p>
            </div>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="filter-container" style="background-color: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 20px;">
        <form method="GET" action="" class="form-row" style="display: flex; gap: 15px; align-items: flex-end;">
            <input type="hidden" name="page" value="admin-appointments">
            
            <div class="form-group" style="flex: 1;">
                <label>Search</label>
                <input type="text" class="form-control" name="search" placeholder="Search by patient, doctor, or reason..." 
                       value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo ($filters['status'] ?? '') == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo ($filters['status'] ?? '') == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo ($filters['status'] ?? '') == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($filters['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Date</label>
                <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($filters['date'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="?page=admin-appointments" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>
    
    <!-- Appointments Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date & Time</th>
                    <th>Specialization</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">No appointments found.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td>#<?php echo $appointment['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($appointment['patient_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($appointment['patient_email']); ?></small><br>
                        <small><?php echo htmlspecialchars($appointment['patient_phone'] ?? 'N/A'); ?></small>
                    </td>
                    <td>
                        <strong>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></strong>
                    </td>
                    <td>
                        <?php echo date('M d, Y', strtotime($appointment['date'])); ?><br>
                        <small><?php echo date('h:i A', strtotime($appointment['time'])); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($appointment['doctor_specialization'] ?? 'General'); ?></td>
                    <td><?php echo htmlspecialchars(substr($appointment['reason'] ?? '', 0, 50)); ?><?php echo strlen($appointment['reason'] ?? '') > 50 ? '...' : ''; ?></td>
                    <td>
                        <span class="status <?php echo strtolower($appointment['status']); ?>">
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($appointment['created_at'])); ?></td>
                    <td>
                        <form action="?page=update-appointment-status&id=<?php echo $appointment['id']; ?>" method="POST" style="display: inline;">
                            <select name="status" onchange="this.form.submit()" class="form-control" style="width: auto; padding: 5px; font-size: 0.9rem;">
                                <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $appointment['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>