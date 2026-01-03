<?php
$pageTitle = 'Manage Appointments - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Appointment Management</h2>
        <button type="button" class="btn btn-primary" onclick="showAddAppointmentModal()">
            <i class="fas fa-calendar-plus"></i> Book New Appointment
        </button>
    </div>
    
    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        <?php unset($_SESSION['success_message']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
    <?php endif; ?>
    
    <!-- Appointment Statistics -->
    <?php 
    $db = \Core\Database::getInstance();
    $appointmentStats = $db->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
        FROM appointments
    ")[0] ?? [];
    ?>
    
    <div class="stats-cards" style="margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $appointmentStats['total'] ?? 0; ?></h3>
                <p>Total Appointments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $appointmentStats['pending'] ?? 0; ?></h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $appointmentStats['confirmed'] ?? 0; ?></h3>
                <p>Confirmed</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $appointmentStats['completed'] ?? 0; ?></h3>
                <p>Completed</p>
            </div>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="filter-container">
        <form method="GET" action="" class="form-row">
            <input type="hidden" name="page" value="receptionist-appointments">
            
            <div class="form-group" style="flex: 1;">
                <label>Date</label>
                <input type="date" class="form-control" name="date" 
                       value="<?php echo htmlspecialchars($filters['date'] ?? ''); ?>">
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label>Status</label>
                <select class="form-control" name="status">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo ($filters['status'] ?? '') == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo ($filters['status'] ?? '') == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo ($filters['status'] ?? '') == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($filters['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label>Doctor</label>
                <select class="form-control" name="doctor_id">
                    <option value="">All Doctors</option>
                    <?php foreach ($doctors as $doctor): ?>
                    <option value="<?php echo $doctor['id']; ?>" 
                        <?php echo ($filters['doctor_id'] ?? '') == $doctor['id'] ? 'selected' : ''; ?>>
                        Dr. <?php echo htmlspecialchars($doctor['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label>Patient</label>
                <select class="form-control" name="patient_id">
                    <option value="">All Patients</option>
                    <?php foreach ($patients as $patient): ?>
                    <option value="<?php echo $patient['id']; ?>" 
                        <?php echo ($filters['patient_id'] ?? '') == $patient['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($patient['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="?page=receptionist-appointments" class="btn btn-outline">Reset</a>
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
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No appointments found.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td>#<?php echo $appointment['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($appointment['patient_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($appointment['patient_phone'] ?? 'N/A'); ?></small>
                    </td>
                    <td>
                        <strong>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($appointment['doctor_specialization']); ?></small>
                    </td>
                    <td>
                        <strong><?php echo $appointment['formatted_date']; ?></strong><br>
                        <small><?php echo $appointment['formatted_time']; ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($appointment['status']); ?>">
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($appointment['created_at'])); ?></td>
                    <td>
                        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <!-- Status Update Form -->
                            <form action="?page=receptionist-appointments" method="POST" style="display: inline;">
                                <input type="hidden" name="update_appointment" value="1">
                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                <select name="status" class="form-control" style="width: auto; padding: 5px; font-size: 0.9rem;" 
                                        onchange="if(confirm('Change appointment status?')) this.form.submit()">
                                    <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $appointment['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                            
                            <a href="?page=receptionist-edit-appointment&id=<?php echo $appointment['id']; ?>" 
                               class="btn btn-sm btn-outline" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <a href="?page=receptionist-cancel-appointment&id=<?php echo $appointment['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to cancel this appointment?')"
                               title="Cancel">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Appointment Modal -->
<div id="addAppointmentModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Book New Appointment</h3>
            <button type="button" class="close-modal" onclick="closeAddAppointmentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form action="?page=receptionist-appointments" method="POST">
                <input type="hidden" name="add_appointment" value="1">
                
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label for="patient_id">Select Patient *</label>
                        <select class="form-control" id="patient_id" name="patient_id" required>
                            <option value="">Select Patient</option>
                            <?php foreach ($patients as $patient): ?>
                            <option value="<?php echo $patient['id']; ?>">
                                <?php echo htmlspecialchars($patient['name']); ?> - <?php echo htmlspecialchars($patient['phone'] ?? ''); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="doctor_id">Select Doctor *</label>
                        <select class="form-control" id="doctor_id" name="doctor_id" required>
                            <option value="">Select Doctor</option>
                            <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['id']; ?>">
                                Dr. <?php echo htmlspecialchars($doctor['name']); ?> - <?php echo htmlspecialchars($doctor['specialization']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Appointment Date *</label>
                        <input type="date" class="form-control" id="date" name="date" required 
                               min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="time">Appointment Time *</label>
                        <input type="time" class="form-control" id="time" name="time" required 
                               min="09:00" max="17:00" step="900"> <!-- 15 minute intervals -->
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="reason">Reason for Visit *</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required 
                              placeholder="Please describe the reason for the appointment..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="status">Appointment Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="pending">Pending</option>
                        <option value="confirmed" selected>Confirmed</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="send_notification" name="send_notification" checked>
                        <label for="send_notification">Send notification to patient</label>
                    </div>
                    <small class="text-muted">
                        Patient will receive an email/SMS notification about the appointment
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeAddAppointmentModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddAppointmentModal() {
    // Set default date to today
    document.getElementById('date').valueAsDate = new Date();
    
    // Set default time to next hour
    const now = new Date();
    const nextHour = new Date(now.getTime() + 60 * 60 * 1000);
    let hours = nextHour.getHours().toString().padStart(2, '0');
    let minutes = Math.ceil(nextHour.getMinutes() / 15) * 15;
    if (minutes === 60) {
        hours = (parseInt(hours) + 1).toString().padStart(2, '0');
        minutes = '00';
    } else {
        minutes = minutes.toString().padStart(2, '0');
    }
    document.getElementById('time').value = `${hours}:${minutes}`;
    
    document.getElementById('addAppointmentModal').style.display = 'flex';
}

function closeAddAppointmentModal() {
    document.getElementById('addAppointmentModal').style.display = 'none';
    document.getElementById('patient_id').selectedIndex = 0;
    document.getElementById('doctor_id').selectedIndex = 0;
}

// Close modal when clicking outside
document.getElementById('addAppointmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddAppointmentModal();
    }
});

// Check doctor availability when date/time changes
document.getElementById('date').addEventListener('change', checkAvailability);
document.getElementById('time').addEventListener('change', checkAvailability);
document.getElementById('doctor_id').addEventListener('change', checkAvailability);

async function checkAvailability() {
    const doctorId = document.getElementById('doctor_id').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    
    if (!doctorId || !date || !time) return;
    
    try {
        const response = await fetch(`ajax/check_availability.php?doctor_id=${doctorId}&date=${date}&time=${time}`);
        const data = await response.json();
        
        if (data.available === false) {
            alert('This time slot is already booked. Please choose a different time.');
            document.getElementById('time').focus();
        }
    } catch (error) {
        console.error('Error checking availability:', error);
    }
}

// Initialize date picker with today as minimum
document.querySelector('input[name="date"]').min = new Date().toISOString().split('T')[0];
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>