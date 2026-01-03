<?php
$pageTitle = 'Manage Users - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Manage Users</h2>
        <a href="?page=admin-add-user" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add User
        </a>
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
    
    <!-- User Statistics -->
    <div class="stats-cards" style="margin-bottom: 30px;">
        <?php foreach ($stats as $stat): ?>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: 
                <?php echo $stat['role'] == 'admin' ? 'var(--warning-color)' : 
                          ($stat['role'] == 'doctor' ? 'var(--primary-color)' : 
                          ($stat['role'] == 'patient' ? 'var(--secondary-color)' : 'var(--success-color)')); ?>">
                <i class="fas fa-user<?php echo $stat['role'] == 'doctor' ? '-md' : 
                                       ($stat['role'] == 'patient' ? '-injured' : ''); ?>"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stat['count']; ?></h3>
                <p><?php echo ucfirst($stat['role']); ?>s</p>
                <small>Active: <?php echo $stat['active'] ?? 0; ?> | Inactive: <?php echo $stat['inactive'] ?? 0; ?></small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Filter Form -->
    <div class="filter-container" style="background-color: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 20px;">
        <form method="GET" action="" class="form-row" style="display: flex; gap: 15px; align-items: flex-end;">
            <input type="hidden" name="page" value="admin-users">
            
            <div class="form-group" style="flex: 1;">
                <label>Search</label>
                <input type="text" class="form-control" name="search" placeholder="Search by name, email, phone..." 
                       value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select class="form-control" name="role">
                    <option value="">All Roles</option>
                    <option value="admin" <?php echo ($filters['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="doctor" <?php echo ($filters['role'] ?? '') == 'doctor' ? 'selected' : ''; ?>>Doctor</option>
                    <option value="patient" <?php echo ($filters['role'] ?? '') == 'patient' ? 'selected' : ''; ?>>Patient</option>
                    <option value="receptionist" <?php echo ($filters['role'] ?? '') == 'receptionist' ? 'selected' : ''; ?>>Receptionist</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                    <option value="">All Status</option>
                    <option value="active" <?php echo ($filters['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($filters['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="on_leave" <?php echo ($filters['status'] ?? '') == 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
                </select>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="?page=admin-users" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No users found.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>#<?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span class="badge" style="background-color: 
                            <?php echo $user['role'] == 'admin' ? 'var(--warning-color)' : 
                                      ($user['role'] == 'doctor' ? 'var(--primary-color)' : 
                                      ($user['role'] == 'patient' ? 'var(--secondary-color)' : 'var(--success-color)')); ?>; 
                            color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.85rem;">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                    <td>
                        <span class="status <?php echo $user['status'] == 'active' ? 'confirmed' : 
                                              ($user['status'] == 'on_leave' ? 'pending' : 'cancelled'); ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $user['status'])); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="?page=admin-edit-user&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <a href="?page=admin-delete-user&id=<?php echo $user['id']; ?>" 
                        class="btn btn-sm btn-danger" 
                        onclick="return confirm('Are you sure you want to delete this user?')">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>