<?php
$pageTitle = 'Edit User - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';

$db = \Core\Database::getInstance();
$departments = $db->query("SELECT * FROM departments ORDER BY name") ?? [];
?>

<div class="container">
    <div class="page-header">
        <h2>Edit User: <?php echo htmlspecialchars($user['name']); ?></h2>
        <a href="?page=admin-users" class="btn btn-outline">Back to Users</a>
    </div>
    
    <div class="form-container">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>
        
        <form action="?page=admin-edit-user&id=<?php echo $user['id']; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">New Password (Leave empty to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <small class="text-muted">Only enter if you want to change the password</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="on_leave" <?php echo $user['status'] == 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Current Role</label>
                    <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" readonly>
                    <small class="text-muted">Role cannot be changed after creation</small>
                </div>
            </div>
            
            <?php if ($user['role'] === 'doctor'): ?>
            <div id="doctor-fields">
                <div class="form-row">
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <input type="text" class="form-control" id="specialization" name="specialization"
                               value="<?php echo htmlspecialchars($user['specialization'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="department_id">Department</label>
                        <select class="form-control" id="department_id" name="department_id">
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>" 
                                <?php echo ($user['department_id'] ?? 0) == $dept['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="form-actions">
                <a href="?page=admin-users" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>