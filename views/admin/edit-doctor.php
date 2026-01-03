<?php
$pageTitle = 'Edit Doctor - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Edit Doctor: Dr. <?php echo htmlspecialchars($doctor['name']); ?></h2>
        <a href="?page=admin-doctors" class="btn btn-outline">Back to Doctors</a>
    </div>
    
    <div class="form-container">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>
        
        <form action="?page=admin-edit-doctor&id=<?php echo $doctor['id']; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $doctor['id']; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?php echo htmlspecialchars($doctor['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($doctor['email']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($doctor['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input type="text" class="form-control" id="specialization" name="specialization"
                           value="<?php echo htmlspecialchars($doctor['specialization'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="department_id">Department</label>
                    <select class="form-control" id="department_id" name="department_id">
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>" 
                            <?php echo ($doctor['department_id'] ?? 0) == $dept['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active" <?php echo $doctor['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $doctor['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="on_leave" <?php echo $doctor['status'] == 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">New Password (Leave empty to keep current)</label>
                <input type="password" class="form-control" id="password" name="password">
                <small class="text-muted">Only enter if you want to change the password</small>
            </div>
            
            <div class="doctor-info" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h4>Doctor Information</h4>
                <div class="row" style="display: flex; gap: 20px; margin-top: 10px;">
                    <div>
                        <strong>Doctor ID:</strong> #<?php echo $doctor['id']; ?>
                    </div>
                    <div>
                        <strong>Role:</strong> Doctor
                    </div>
                    <div>
                        <strong>Created:</strong> <?php echo date('M d, Y', strtotime($doctor['created_at'])); ?>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="?page=admin-doctors" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Doctor</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>