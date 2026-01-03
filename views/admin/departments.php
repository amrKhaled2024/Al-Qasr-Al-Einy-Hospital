<?php
$pageTitle = 'Manage Departments - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Manage Departments</h2>
        <a href="?page=admin-add-department" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Department
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
    
    <!-- Departments Table -->
    <?php if (empty($departments)): ?>
    <div class="alert" style="background-color: var(--primary-light); padding: 20px; border-radius: var(--border-radius); text-align: center;">
        <p style="margin: 0 0 15px 0;">No departments found. Add your first department!</p>
        <a href="?page=admin-add-department" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Department
        </a>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Department Name</th>
                    <th>Description</th>
                    <th>Doctors Count</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $dept): ?>
                <tr>
                    <td>#<?php echo $dept['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($dept['name']); ?></strong>
                    </td>
                    <td>
                        <?php if (!empty($dept['description'])): ?>
                            <?php echo htmlspecialchars(substr($dept['description'], 0, 100)); ?>
                            <?php if (strlen($dept['description']) > 100): ?>...<?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">No description</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge" style="background-color: var(--primary-color); color: white; padding: 4px 8px; border-radius: 12px;">
                            <?php echo $dept['doctor_count'] ?? 0; ?> doctors
                        </span>
                    </td>
                    <td>
                        <?php echo date('M d, Y', strtotime($dept['created_at'])); ?>
                    </td>
                    <td>
                        <a href="?page=admin-edit-department&id=<?php echo $dept['id']; ?>" class="btn btn-sm btn-outline" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="?page=admin-delete-department&id=<?php echo $dept['id']; ?>" 
                           class="btn btn-sm btn-danger" 
                           title="Delete"
                           onclick="return confirm('Are you sure you want to delete the <?php echo htmlspecialchars($dept['name']); ?> department?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Quick Stats -->
    <div class="stats-cards" style="margin-top: 30px;">
        <div class="stat-card">
            <div class="stat-icon departments">
                <i class="fas fa-clinic-medical"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($departments); ?></h3>
                <p>Total Departments</p>
            </div>
        </div>
        
        <?php
        // Calculate total doctors in all departments
        $totalDoctors = 0;
        foreach ($departments as $dept) {
            $totalDoctors += $dept['doctor_count'] ?? 0;
        }
        ?>
        
        <div class="stat-card">
            <div class="stat-icon doctors">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $totalDoctors; ?></h3>
                <p>Total Doctors</p>
            </div>
        </div>
        
        <?php
        // Find department with most doctors
        $mostDoctors = 0;
        $busiestDept = '';
        foreach ($departments as $dept) {
            if (($dept['doctor_count'] ?? 0) > $mostDoctors) {
                $mostDoctors = $dept['doctor_count'];
                $busiestDept = $dept['name'];
            }
        }
        ?>
        
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--success-color);">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $mostDoctors; ?></h3>
                <p>Busiest Department<br><small><?php echo htmlspecialchars($busiestDept); ?></small></p>
            </div>
        </div>
        
        <?php
        // Find empty departments
        $emptyDepartments = 0;
        foreach ($departments as $dept) {
            if (($dept['doctor_count'] ?? 0) == 0) {
                $emptyDepartments++;
            }
        }
        ?>
        
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--warning-color);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $emptyDepartments; ?></h3>
                <p>Empty Departments</p>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add search input if needed
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search departments...';
    searchInput.className = 'form-control';
    searchInput.style.marginBottom = '20px';
    
    // Insert search input before table
    const tableContainer = document.querySelector('.table-container');
    if (tableContainer) {
        tableContainer.parentNode.insertBefore(searchInput, tableContainer);
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = tableContainer.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>