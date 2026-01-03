<?php
$pageTitle = 'Manage Doctors - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Manage Doctors</h2>
        <a class="btn btn-primary" href="?page=admin-add-doctor">
            <i class="fas fa-plus"></i> Add Doctor
        </a>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Department</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                    <td><?php echo htmlspecialchars($doctor['specialization'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($doctor['department_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($doctor['phone']); ?></td>
                    <td>
                        <span class="status <?php echo $doctor['status'] == 'active' ? 'confirmed' : 'pending'; ?>">
                            <?php echo ucfirst($doctor['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="?page=admin-edit-doctor&id=<?php echo $doctor['id']; ?>" class="btn btn-sm btn-outline" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?page=admin-delete-doctor&id=<?php echo $doctor['id']; ?>" 
                            class="btn btn-sm btn-danger" 
                            title="Delete"
                            onclick="return confirm('Are you sure you want to delete Dr. <?php echo htmlspecialchars($doctor['name']); ?>?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function editDoctor(doctorId) {
    // In a real app, you would fetch doctor data via AJAX
    alert('Edit functionality would be implemented with AJAX');
}

function deleteDoctor(doctorId) {
    if (confirm('Are you sure you want to delete this doctor?')) {
        window.location.href = '/hospital-management-system/admin/deleteDoctor/' + doctorId;
    }
}

function closeDoctorModal() {
    document.getElementById('doctor-modal').style.display = 'none';
}

</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>