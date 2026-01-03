<?php
$pageTitle = 'Add Appointment - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Schedule New Appointment</h2>
        <a href="?page=admin-appointments" class="btn btn-outline">Back to Appointments</a>
    </div>
    
    <div class="form-container">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>
        
        <form action="?page=admin-add-appointment" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="patient_id">Select Patient *</label>
                    <select class="form-control" id="patient_id" name="patient_id" required>
                        <option value="">Select a Patient</option>
                        <?php foreach ($patients as $patient): ?>
                        <option value="<?php echo $patient['id']; ?>">
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
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="time">Appointment Time *</label>
                    <input type="time" class="form-control" id="time" name="time" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="reason">Reason for Visit *</label>
                <textarea class="form-control" id="reason" name="reason" rows="3" required 
                          placeholder="Enter the reason for the appointment..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="status">Appointment Status *</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="pending" selected>Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="reset" class="btn btn-outline">Reset</button>
                <button type="submit" class="btn btn-primary">Schedule Appointment</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').min = today;
    document.getElementById('date').value = today;
    
    // Set default time to next hour
    const now = new Date();
    const nextHour = new Date(now.getTime() + 60 * 60 * 1000);
    const timeString = nextHour.getHours().toString().padStart(2, '0') + ':' + 
                      nextHour.getMinutes().toString().padStart(2, '0');
    document.getElementById('time').value = timeString;
    
    // Auto-fill common reasons based on doctor's specialization
    const doctorSelect = document.getElementById('doctor_id');
    const reasonTextarea = document.getElementById('reason');
    
    if (doctorSelect && reasonTextarea) {
        doctorSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const doctorText = selectedOption.text;
            
            // Extract specialization from doctor text
            if (doctorText.includes('-')) {
                const specialization = doctorText.split('-')[1].trim();
                
                const reasonPresets = {
                    'Cardiology': 'Heart checkup and cardiovascular assessment.',
                    'Neurology': 'Neurological consultation and examination.',
                    'Orthopedics': 'Bone and joint examination.',
                    'Pediatrics': 'Child health checkup.',
                    'Dermatology': 'Skin condition consultation.',
                    'Gynecology': 'Women\'s health examination.',
                    'General': 'General medical consultation and checkup.'
                };
                
                for (const [key, value] of Object.entries(reasonPresets)) {
                    if (specialization.includes(key)) {
                        reasonTextarea.value = value;
                        break;
                    }
                }
            }
        });
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>