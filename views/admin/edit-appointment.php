<?php
$pageTitle = 'Edit Appointment - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Edit Appointment #<?php echo $appointment['id']; ?></h2>
        <a href="?page=admin-appointments" class="btn btn-outline">Back to Appointments</a>
    </div>
    
    <div class="form-container">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>
        
        <form action="?page=admin-edit-appointment&id=<?php echo $appointment['id']; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $appointment['id']; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="patient_id">Select Patient *</label>
                    <select class="form-control" id="patient_id" name="patient_id" required>
                        <option value="">Select a Patient</option>
                        <?php foreach ($patients as $patient): ?>
                        <option value="<?php echo $patient['id']; ?>" 
                            <?php echo $appointment['patient_id'] == $patient['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($patient['name']); ?> (<?php echo htmlspecialchars($patient['id']); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="doctor_id">Select Doctor *</label>
                    <select class="form-control" id="doctor_id" name="doctor_id" required>
                        <option value="">Select a Doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                        <option value="<?php echo $doctor['id']; ?>" 
                            <?php echo $appointment['doctor_id'] == $doctor['id'] ? 'selected' : ''; ?>>
                            Dr. <?php echo htmlspecialchars($doctor['name']); ?> - <?php echo htmlspecialchars($doctor['specialization']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date">Appointment Date *</label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="<?php echo htmlspecialchars($appointment['date']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="time">Appointment Time *</label>
                    <input type="time" class="form-control" id="time" name="time" 
                           value="<?php echo htmlspecialchars(substr($appointment['time'], 0, 5)); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="reason">Reason for Visit *</label>
                <textarea class="form-control" id="reason" name="reason" rows="3" required><?php echo htmlspecialchars($appointment['reason']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="status">Appointment Status *</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $appointment['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo $appointment['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            
            <div class="appointment-info" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h4>Appointment Details</h4>
                <div class="row" style="display: flex; gap: 20px; margin-top: 10px;">
                    <div>
                        <strong>Appointment ID:</strong> #<?php echo $appointment['id']; ?>
                    </div>
                    <div>
                        <strong>Created:</strong> <?php echo date('M d, Y', strtotime($appointment['created_at'])); ?>
                    </div>
                    <div>
                        <strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($appointment['updated_at'])); ?>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="?page=admin-appointments" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Appointment</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').min = today;
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>