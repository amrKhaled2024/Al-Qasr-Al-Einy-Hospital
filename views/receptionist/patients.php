<?php
$pageTitle = 'Manage Patients - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Patient Management</h2>
        <button type="button" class="btn btn-primary" onclick="showAddPatientModal()">
            <i class="fas fa-user-plus"></i> Add New Patient
        </button>
    </div>
    
    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        <?php unset($_SESSION['success_message']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
    <?php endif; ?>
    
    <!-- Filter Form -->
    <div class="filter-container">
        <form method="GET" action="" class="form-row">
            <input type="hidden" name="page" value="receptionist-patients">
            
            <div class="form-group" style="flex: 2;">
                <label>Search Patients</label>
                <input type="text" class="form-control" name="search" placeholder="Search by name, email, or phone..." 
                       value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                    <option value="">All Status</option>
                    <option value="active" <?php echo ($filters['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($filters['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="?page=receptionist-patients" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>
    
    <!-- Patients Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No patients found.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($patients as $patient): 
                    // Get patient details
                    $db = \Core\Database::getInstance();
                    $patientDetails = $db->query("
                        SELECT gender, date_of_birth, address 
                        FROM patient_details 
                        WHERE user_id = ?
                    ", [$patient['id']])[0] ?? [];
                ?>
                <tr>
                    <td>#<?php echo $patient['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($patient['name']); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                    <td><?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($patientDetails['gender'] ?? 'N/A'); ?></td>
                    <td>
                        <span class="status <?php echo $patient['status'] == 'active' ? 'confirmed' : 'cancelled'; ?>">
                            <?php echo ucfirst($patient['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($patient['created_at'])); ?></td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="?page=receptionist-book-for-patient&patient_id=<?php echo $patient['id']; ?>" 
                               class="btn btn-sm btn-primary" title="Book Appointment">
                                <i class="fas fa-calendar-plus"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline" 
                                    onclick="viewPatientDetails(<?php echo $patient['id']; ?>)"
                                    title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="?page=receptionist-edit-patient&id=<?php echo $patient['id']; ?>" 
                               class="btn btn-sm btn-outline" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Patient Modal -->
<div id="addPatientModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Register New Patient</h3>
            <button type="button" class="close-modal" onclick="closeAddPatientModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form action="?page=receptionist-patients" method="POST">
                <h4>Personal Information</h4>
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
                        <label for="phone">Phone Number *</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender *</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth *</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required 
                               max="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="send_welcome" name="send_welcome" checked>
                        <label for="send_welcome">Send welcome email with login details</label>
                    </div>
                    <small class="text-muted">
                        Default password: patient123<br>
                        Patient can change password after first login
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeAddPatientModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Patient Details Modal -->
<div id="patientDetailsModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="max-width: 800px;">
        <div class="modal-header">
            <h3>Patient Details</h3>
            <button type="button" class="close-modal" onclick="closePatientDetailsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="patientDetailsContent">
                <!-- Content will be loaded via AJAX -->
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Loading patient details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showAddPatientModal() {
    document.getElementById('addPatientModal').style.display = 'flex';
}

function closeAddPatientModal() {
    document.getElementById('addPatientModal').style.display = 'none';
}

function viewPatientDetails(patientId) {
    document.getElementById('patientDetailsModal').style.display = 'flex';
    
    // Load patient details via AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `ajax/get_patient_details.php?patient_id=${patientId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('patientDetailsContent').innerHTML = xhr.responseText;
        } else {
            document.getElementById('patientDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Failed to load patient details.
                </div>
            `;
        }
    };
    xhr.send();
}

function closePatientDetailsModal() {
    document.getElementById('patientDetailsModal').style.display = 'none';
}

// Close modals when clicking outside
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            if (this.id === 'addPatientModal') closeAddPatientModal();
            if (this.id === 'patientDetailsModal') closePatientDetailsModal();
        }
    });
});

// Initialize date of birth max to today
document.getElementById('date_of_birth').max = new Date().toISOString().split('T')[0];

// Add age calculator
document.getElementById('date_of_birth').addEventListener('change', function() {
    const dob = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    document.getElementById('age_display').innerText = `Age: ${age} years`;
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>