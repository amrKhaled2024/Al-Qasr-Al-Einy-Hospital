<?php
$pageTitle = 'Receptionist Dashboard - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Receptionist Dashboard</h2>
        <p>Welcome back, <?php echo $_SESSION['user_name']; ?>. Manage patients and appointments.</p>
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
                <i class="fas fa-user-injured"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_patients']; ?></h3>
                <p>Total Patients</p>
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
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--success-color);">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['available_doctors']; ?></h3>
                <p>Available Doctors</p>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="background-color: white; padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 30px;">
        <h3>Quick Actions</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
            <a href="?page=receptionist-appointments" class="btn btn-primary" style="text-align: center; padding: 15px;">
                <i class="fas fa-calendar-plus fa-2x"></i><br>
                <span>Book Appointment</span>
            </a>
            <a href="?page=receptionist-patients" class="btn btn-secondary" style="text-align: center; padding: 15px;">
                <i class="fas fa-user-plus fa-2x"></i><br>
                <span>Register Patient</span>
            </a>
            <a href="?page=receptionist-doctors" class="btn btn-success" style="text-align: center; padding: 15px;">
                <i class="fas fa-user-md fa-2x"></i><br>
                <span>View Doctors</span>
            </a>
            <a href="#" class="btn btn-warning" style="text-align: center; padding: 15px;" onclick="showQuickAppointmentModal()">
                <i class="fas fa-bolt fa-2x"></i><br>
                <span>Quick Book</span>
            </a>
        </div>
    </div>
    
    <!-- Today's Appointments -->
    <div class="page-header">
        <h3>Today's Appointments</h3>
        <a href="?page=receptionist-appointments" class="btn btn-outline">
            <i class="fas fa-eye"></i> View All
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
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Specialization</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($todayAppointments as $appointment): ?>
                <tr>
                    <td><strong><?php echo $appointment['formatted_time']; ?></strong></td>
                    <td>
                        <strong><?php echo htmlspecialchars($appointment['patient_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($appointment['patient_phone'] ?? 'N/A'); ?></small>
                    </td>
                    <td>
                        <strong>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($appointment['doctor_specialization']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($appointment['doctor_specialization']); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($appointment['status']); ?>">
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <form action="?page=update-appointment-status" method="POST" style="display: inline;">
                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                <select name="status" class="form-control" style="width: auto; padding: 5px; font-size: 0.9rem;" 
                                        onchange="if(confirm('Change appointment status?')) this.form.submit()">
                                    <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $appointment['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                            <a href="?page=receptionist-edit-appointment&id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Recent Patients -->
    <?php if (!empty($recentPatients)): ?>
    <div class="page-header" style="margin-top: 40px;">
        <h3>Recent Patients</h3>
        <a href="?page=receptionist-patients" class="btn btn-outline">
            <i class="fas fa-users"></i> View All Patients
        </a>
    </div>
    
    <div class="cards-container">
        <?php foreach ($recentPatients as $patient): ?>
        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background-color: var(--secondary-light); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($patient['name']); ?></h4>
                        <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 0;">
                            <?php echo htmlspecialchars($patient['email']); ?>
                        </p>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="status confirmed" style="font-size: 0.8rem;">
                        <?php echo ucfirst($patient['status']); ?>
                    </span>
                    <a href="?page=receptionist-book-for-patient&patient_id=<?php echo $patient['id']; ?>" 
                       class="btn btn-sm btn-primary" title="Book Appointment">
                        <i class="fas fa-calendar-plus"></i> Book
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Quick Appointment Modal -->
<div id="quickAppointmentModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Quick Appointment Booking</h3>
            <button type="button" class="close-modal" onclick="closeQuickModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="quickAppointmentForm" action="?page=receptionist-appointments" method="POST">
                <input type="hidden" name="add_appointment" value="1">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="quick_patient">Select Patient *</label>
                        <select class="form-control" id="quick_patient" name="patient_id" required>
                            <option value="">Select Patient</option>
                            <?php 
                            $db = \Core\Database::getInstance();
                            $allPatients = $db->query("SELECT id, name FROM users WHERE role = 'patient' AND status = 'active' ORDER BY name");
                            foreach ($allPatients as $patient): ?>
                            <option value="<?php echo $patient['id']; ?>">
                                <?php echo htmlspecialchars($patient['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quick_doctor">Select Doctor *</label>
                        <select class="form-control" id="quick_doctor" name="doctor_id" required>
                            <option value="">Select Doctor</option>
                            <?php 
                            $allDoctors = $db->query("SELECT id, name, specialization FROM users WHERE role = 'doctor' AND status = 'active' ORDER BY name");
                            foreach ($allDoctors as $doctor): ?>
                            <option value="<?php echo $doctor['id']; ?>">
                                Dr. <?php echo htmlspecialchars($doctor['name']); ?> - <?php echo htmlspecialchars($doctor['specialization']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="quick_date">Date *</label>
                        <input type="date" class="form-control" id="quick_date" name="date" required 
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="quick_time">Time *</label>
                        <input type="time" class="form-control" id="quick_time" name="time" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="quick_reason">Reason</label>
                    <textarea class="form-control" id="quick_reason" name="reason" rows="2" 
                              placeholder="Brief reason for appointment"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="quick_status">Status</label>
                    <select class="form-control" id="quick_status" name="status">
                        <option value="pending">Pending</option>
                        <option value="confirmed" selected>Confirmed</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeQuickModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showQuickAppointmentModal() {
    // Set default date to today
    document.getElementById('quick_date').valueAsDate = new Date();
    
    // Set default time to next hour
    const now = new Date();
    const nextHour = new Date(now.getTime() + 60 * 60 * 1000);
    document.getElementById('quick_time').value = nextHour.toTimeString().substring(0, 5);
    
    document.getElementById('quickAppointmentModal').style.display = 'flex';
}

function closeQuickModal() {
    document.getElementById('quickAppointmentModal').style.display = 'none';
    document.getElementById('quickAppointmentForm').reset();
}

// Close modal when clicking outside
document.getElementById('quickAppointmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuickModal();
    }
});

// Auto-refresh dashboard every 2 minutes
setTimeout(function() {
    location.reload();
}, 2 * 60 * 1000);
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>