<?php
$pageTitle = 'Edit Department - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Edit Department: <?php echo htmlspecialchars($department['name']); ?></h2>
        <a href="?page=admin-departments" class="btn btn-outline">Back to Departments</a>
    </div>
    
    <div class="form-container">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>
        
        <form action="?page=admin-edit-department&id=<?php echo $department['id']; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $department['id']; ?>">
            
            <div class="form-group">
                <label for="name">Department Name *</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo htmlspecialchars($department['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($department['description'] ?? ''); ?></textarea>
            </div>
            
            <!-- Department Stats -->
            <div class="department-stats" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h4>Department Information</h4>
                <div class="row" style="display: flex; gap: 20px; margin-top: 10px;">
                    <div>
                        <strong>ID:</strong> #<?php echo $department['id']; ?>
                    </div>
                    <div>
                        <strong>Created:</strong> <?php echo date('M d, Y', strtotime($department['created_at'])); ?>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="?page=admin-departments" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Department</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for description
    const description = document.getElementById('description');
    const counter = document.createElement('div');
    counter.className = 'text-muted';
    counter.style.textAlign = 'right';
    counter.style.fontSize = '0.9rem';
    counter.style.marginTop = '5px';
    
    description.parentNode.appendChild(counter);
    
    function updateCounter() {
        const length = description.value.length;
        counter.textContent = `${length}/500 characters`;
        
        if (length > 500) {
            counter.style.color = 'var(--danger-color)';
        } else if (length > 400) {
            counter.style.color = 'var(--warning-color)';
        } else {
            counter.style.color = 'var(--text-light)';
        }
    }
    
    description.addEventListener('input', updateCounter);
    updateCounter(); // Initial call
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>