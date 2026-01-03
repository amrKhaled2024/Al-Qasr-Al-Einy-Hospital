<?php
$pageTitle = 'Book Appointment - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>Book Appointment</h2>
        <a href="?page=patient-appointments" class="btn btn-outline">Back to Appointments</a>
    </div>
    
    <!-- Progress Steps -->
    <div class="progress-steps">
        <div class="progress-step <?php echo empty($selectedDepartment) ? 'active' : 'completed'; ?>">
            <div class="step-number">1</div>
            <div class="step-label">Select Department</div>
        </div>
        <div class="progress-step <?php echo !empty($selectedDepartment) && empty($selectedSpecialization) ? 'active' : 
                                  (!empty($selectedSpecialization) ? 'completed' : ''); ?>">
            <div class="step-number">2</div>
            <div class="step-label">Select Specialization</div>
        </div>
        <div class="progress-step <?php echo !empty($selectedSpecialization) && empty($doctors) ? 'active' : 
                                  (!empty($doctors) ? 'completed' : ''); ?>">
            <div class="step-number">3</div>
            <div class="step-label">Select Doctor</div>
        </div>
        <div class="progress-step <?php echo !empty($doctors) ? 'active' : ''; ?>">
            <div class="step-number">4</div>
            <div class="step-label">Book Appointment</div>
        </div>
    </div>
    
    <!-- Error Message -->
    <?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <!-- Step 1: Select Department -->
    <?php if (empty($selectedDepartment)): ?>
    <div class="form-container">
        <h3>Step 1: Select Medical Department</h3>
        <p>Choose the department for your medical needs.</p>
        
        <form action="?page=patient-book" method="POST">
            <input type="hidden" name="step" value="select_department">
            
            <div class="form-group">
                <label for="department_id">Select Department *</label>
                <select class="form-control" id="department_id" name="department_id" required>
                    <option value="">Choose a department</option>
                    <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo $dept['id']; ?>" 
                        <?php echo $selectedDepartment == $dept['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dept['name']); ?>
                        <?php if (!empty($dept['description'])): ?>
                        <small style="color: #666;">- <?php echo htmlspecialchars($dept['description']); ?></small>
                        <?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Next: Select Specialization</button>
            </div>
        </form>
    </div>
    
    <!-- Step 2: Select Specialization -->
    <?php elseif (!empty($selectedDepartment) && empty($selectedSpecialization)): ?>
    <div class="form-container">
        <h3>Step 2: Select Specialization</h3>
        <p>Now choose the medical specialization within the selected department.</p>
        
        <form action="?page=patient-book" method="POST">
            <input type="hidden" name="step" value="select_specialization">
            <input type="hidden" name="department_id" value="<?php echo $selectedDepartment; ?>">
            
            <div class="form-group">
                <label for="specialization">Select Specialization *</label>
                <?php if (empty($specializations)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No specializations available for this department.
                    <br>
                    <button type="button" class="btn btn-outline btn-sm" onclick="window.location.href='?page=patient-book'">
                        Back to Departments
                    </button>
                </div>
                <?php else: ?>
                <select class="form-control" id="specialization" name="specialization" required>
                    <option value="">Choose a specialization</option>
                    <?php foreach ($specializations as $spec): ?>
                    <option value="<?php echo htmlspecialchars($spec['specialization']); ?>" 
                        <?php echo $selectedSpecialization == $spec['specialization'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($spec['specialization']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="window.location.href='?page=patient-book'">
                    Back to Departments
                </button>
                <?php if (!empty($specializations)): ?>
                <button type="submit" class="btn btn-primary">Next: Select Doctor</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Step 3: Select Doctor -->
    <?php elseif (!empty($selectedDepartment) && !empty($selectedSpecialization) && empty($doctors)): ?>
    <div class="form-container">
        <h3>Step 3: Select Doctor</h3>
        <p>Choose a doctor from the available specialists.</p>
        
        <?php if (empty($doctors)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No doctors available for this specialization.
            <br>
            <button type="button" class="btn btn-outline btn-sm" onclick="window.location.href='?page=patient-book'">
                Back to Specializations
            </button>
        </div>
        <?php else: ?>
        <!-- This section will show doctors after form submission -->
        <?php endif; ?>
    </div>
    
    <!-- Step 4: Book Appointment (Show Doctors and Booking Form) -->
    <?php elseif (!empty($doctors)): ?>
    <div class="form-container">
        <h3>Step 4: Book Appointment</h3>
        <p>Select a doctor and choose appointment details.</p>
        
        <!-- Doctors List -->
        <div class="doctors-list" style="margin-bottom: 30px;">
            <h4>Available Doctors</h4>
            <div class="cards-container">
                <?php foreach ($doctors as $doctor): ?>
                <div class="card">
                    <div class="card-body">
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; background-color: var(--primary-light); 
                                      border-radius: 50%; display: flex; align-items: center; 
                                      justify-content: center; margin: 0 auto 15px; font-size: 2rem; color: var(--primary-color);">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <h4><?php echo htmlspecialchars($doctor['name']); ?></h4>
                            <p style="color: var(--primary-color); font-weight: 500; margin-bottom: 5px;">
                                <?php echo htmlspecialchars($doctor['specialization']); ?>
                            </p>
                            <p style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">
                                <?php echo htmlspecialchars($doctor['department_name']); ?>
                            </p>
                            <?php if ($doctor['phone']): ?>
                            <p style="font-size: 0.9rem;">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($doctor['phone']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Booking Form -->
        <h4>Appointment Details</h4>
        <form action="?page=patient-book" method="POST">
            <input type="hidden" name="step" value="book_appointment">
            <input type="hidden" name="department_id" value="<?php echo $selectedDepartment; ?>">
            <input type="hidden" name="specialization" value="<?php echo htmlspecialchars($selectedSpecialization); ?>">
            
            <div class="form-group">
                <label for="doctor_id">Select Doctor *</label>
                <select class="form-control" id="doctor_id" name="doctor_id" required>
                    <option value="">Choose a doctor</option>
                    <?php foreach ($doctors as $doctor): ?>
                    <option value="<?php echo $doctor['id']; ?>">
                        Dr. <?php echo htmlspecialchars($doctor['name']); ?> - 
                        <?php echo htmlspecialchars($doctor['specialization']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date">Appointment Date *</label>
                    <input type="date" class="form-control" id="date" name="date" required 
                           min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="time">Appointment Time *</label>
                    <select class="form-control" id="time" name="time" required>
                        <option value="">Select Time</option>
                        <?php for ($hour = 9; $hour <= 17; $hour++): ?>
                        <?php if ($hour != 13): ?> <!-- Skip lunch hour -->
                        <option value="<?php echo sprintf('%02d:00:00', $hour); ?>">
                            <?php echo sprintf('%02d:00', $hour); ?> AM
                        </option>
                        <option value="<?php echo sprintf('%02d:30:00', $hour); ?>">
                            <?php echo sprintf('%02d:30', $hour); ?> AM
                        </option>
                        <?php endif; ?>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="reason">Reason for Visit *</label>
                <textarea class="form-control" id="reason" name="reason" rows="3" required 
                          placeholder="Please describe your symptoms or reason for the appointment"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-outline" 
                        onclick="window.location.href='?page=patient-book&department_id=<?php echo $selectedDepartment; ?>'">
                    Back to Doctors
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-plus"></i> Book Appointment
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set min date to today
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('date');
    if (dateInput) {
        dateInput.min = today;
        if (!dateInput.value) {
            dateInput.value = today;
        }
    }
    
    // Form validation
    const bookingForm = document.querySelector('form[action*="patient-book"]');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                submitBtn.disabled = true;
            }
        });
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>