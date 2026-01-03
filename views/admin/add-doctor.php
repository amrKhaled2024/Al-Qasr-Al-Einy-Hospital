<?php
// views/admin/add-doctor-fixed.php
$pageTitle = 'Add Doctor - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Add New Doctor</h2>
        <a href="?page=admin-doctors" class="btn btn-outline">Back to Doctors</a>
    </div>
    
    <div class="form-container">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
        <?php endif; ?>
        
        <form action="?page=admin-add-doctor" method="POST" onsubmit="return validateDoctorForm()">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" class="form-control" id="name" name="name" required 
                        value="<?php echo isset($formData['name']) ? htmlspecialchars($formData['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" required
                        value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required
                        value="<?php echo isset($formData['phone']) ? htmlspecialchars($formData['phone']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="specialization">Specialization *</label>
                    <input type="text" class="form-control" id="specialization" name="specialization" required
                        value="<?php echo isset($formData['specialization']) ? htmlspecialchars($formData['specialization']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="department_id">Department *</label>
                    <select class="form-control" id="department_id" name="department_id" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>" 
                            <?php echo (isset($formData['department_id']) && $formData['department_id'] == $dept['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active" <?php echo (!isset($formData['status']) || $formData['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo (isset($formData['status']) && $formData['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        <option value="on_leave" <?php echo (isset($formData['status']) && $formData['status'] == 'on_leave') ? 'selected' : ''; ?>>On Leave</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <small class="text-muted">Minimum 6 characters</small>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="reset" class="btn btn-outline">Reset</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Doctor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    
document.addEventListener('DOMContentLoaded', function() {
    // Password validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validateDoctorForm() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password.length < 6) {
            alert('Password must be at least 6 characters long');
            return false;
        }
        
        if (password !== confirmPassword) {
            alert('Passwords do not match');
            return false;
        }
        
        return true;
    }
    
    // Form submission validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!validatePasswords()) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;
        
        return true;
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>