<?php
$pageTitle = 'My Appointments - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>My Appointments</h2>
        <p>Manage all your appointments</p>
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
    <div class="stats-cards" style="margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $stats['total']; ?></h3>
                <p>Total Appointments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $stats['pending']; ?></h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $stats['confirmed']; ?></h3>
                <p>Confirmed</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $stats['completed']; ?></h3>
                <p>Completed</p>
            </div>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="filter-container">
        <form method="GET" action="" class="form-row">
            <input type="hidden" name="page" value="doctor-appointments">
            
            <div class="form-group" style="flex: 1;">
                <label>Search Patient</label>
                <input type="text" class="form-control" name="search" placeholder="Search by patient name or reason..." 
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
                <input type="date" class="form-control" name="date" 
                       value="<?php echo htmlspecialchars($filters['date'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="?page=doctor-appointments" class="btn btn-outline">Reset</a>
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
                    <th>Date & Time</th>
                    <th>Contact</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No appointments found.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td>#<?php echo $appointment['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($appointment['patient_name']); ?></strong>
                    </td>
                    <td>
                        <strong><?php echo $appointment['formatted_date']; ?></strong><br>
                        <small><?php echo $appointment['formatted_time']; ?></small>
                    </td>
                    <td>
                        <small><?php echo htmlspecialchars($appointment['patient_phone'] ?? 'N/A'); ?><br>
                        <?php echo htmlspecialchars($appointment['patient_email']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                    <td>
                        <span class="status <?php echo strtolower($appointment['status']); ?>">
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <!-- Update Status Form -->
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
                            
                            <!-- Add Notes Button -->
                            <button type="button" class="btn btn-sm btn-outline" onclick="showNotesModal(<?php echo $appointment['id']; ?>, '<?php echo htmlspecialchars($appointment['patient_name']); ?>')" 
                                    title="Add Notes">
                                <i class="fas fa-notes-medical"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Notes Modal -->
<div id="notesModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Add Medical Notes</h3>
            <button type="button" class="close-modal" onclick="closeNotesModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="notesForm" action="?page=update-appointment-status" method="POST">
                <input type="hidden" name="appointment_id" id="modalAppointmentId">
                <input type="hidden" name="status" value="">
                
                <div class="form-group">
                    <label for="patientName">Patient</label>
                    <input type="text" class="form-control" id="patientName" readonly>
                </div>
                
                <div class="form-group">
                    <label for="notes">Medical Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="5" placeholder="Enter medical notes, prescriptions, or recommendations..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeNotesModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Notes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showNotesModal(appointmentId, patientName) {
    document.getElementById('modalAppointmentId').value = appointmentId;
    document.getElementById('patientName').value = patientName;
    document.getElementById('notesModal').style.display = 'flex';
}

function closeNotesModal() {
    document.getElementById('notesModal').style.display = 'none';
    document.getElementById('notesForm').reset();
}

// Close modal when clicking outside
document.getElementById('notesModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNotesModal();
    }
});

// Initialize date picker with today as minimum
document.querySelector('input[name="date"]').min = new Date().toISOString().split('T')[0];
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>