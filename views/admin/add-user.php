<?php
$pageTitle = 'Add User - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';

$db = \Core\Database::getInstance();
$departments = $db->query("SELECT * FROM departments ORDER BY name") ?? [];
?>

<div class="container">
    <div class="page-header">
        <h2>Add New User</h2>
        <a href="?page=admin-users" class="btn btn-outline">Back to Users</a>
    </div>
    
    <div class="form-container">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>
        
        <form action="?page=admin-add-user" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="text-muted">Default: password123</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="role">Role *</label>
                    <select class="form-control" id="role" name="role" required onchange="toggleDoctorFields()">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="doctor">Doctor</option>
                        <option value="receptionist">Receptionist</option>
                        <option value="patient">Patient</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="on_leave">On Leave</option>
                    </select>
                </div>
            </div>
            
            <!-- Doctor-specific fields (hidden by default) -->
            <div id="doctor-fields" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <input type="text" class="form-control" id="specialization" name="specialization">
                    </div>
                    <div class="form-group">
                        <label for="department_id">Department</label>
                        <select class="form-control" id="department_id" name="department_id">
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="reset" class="btn btn-outline">Reset</button>
                <button type="submit" class="btn btn-primary">Add User</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDoctorFields() {
    const role = document.getElementById('role').value;
    const doctorFields = document.getElementById('doctor-fields');
    
    if (role === 'doctor') {
        doctorFields.style.display = 'block';
        document.getElementById('specialization').required = true;
        document.getElementById('department_id').required = true;
    } else {
        doctorFields.style.display = 'none';
        document.getElementById('specialization').required = false;
        document.getElementById('department_id').required = false;
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>