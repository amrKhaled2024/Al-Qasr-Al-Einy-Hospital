<?php
$pageTitle = 'Login - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';

// Get error from session if it exists
$error = $_SESSION['login_error'] ?? ($error ?? '');
unset($_SESSION['login_error']);
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Welcome to Kasr Al Ainy</h2>
            <p>Please login to continue</p>
        </div>
        <div class="auth-body">
            <?php if (!empty($error)): ?>
            <div class="error-message active" id="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form action="<?php echo APP_URL; ?>/public/index.php?page=login" method="POST" id="login-form">
                <div class="form-group">
                    <label for="login-email">Email Address</label>
                    <input type="email" id="login-email" name="email" class="form-control" 
                           placeholder="Enter your email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" 
                           class="form-control" placeholder="Enter your password" required>
                </div>
                
                <!-- <div class="form-group">
                    <label>Select Role</label>
                    <div class="role-selector">
                        <div class="role-option <?php echo (($_POST['role'] ?? 'admin') === 'admin') ? 'active' : ''; ?>" 
                             data-role="admin">Admin</div>
                        <div class="role-option <?php echo (($_POST['role'] ?? '') === 'doctor') ? 'active' : ''; ?>" 
                             data-role="doctor">Doctor</div>
                        <div class="role-option <?php echo (($_POST['role'] ?? '') === 'receptionist') ? 'active' : ''; ?>" 
                             data-role="receptionist">Receptionist</div>
                        <div class="role-option <?php echo (($_POST['role'] ?? 'patient') === 'patient') ? 'active' : ''; ?>" 
                             data-role="patient">Patient</div>
                    </div>
                    <input type="hidden" name="role" id="selected-role" 
                           value="<?php echo $_POST['role'] ?? 'admin'; ?>">
                </div> -->
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="auth-switch">
                <p>Don't have an account? <a href="<?php echo APP_URL; ?>/public/index.php?page=register">Register as Patient</a></p>
                <p style="margin-top: 10px; font-size: 0.9rem; color: #666;">
                    <i class="fas fa-info-circle"></i> Demo credentials are provided above
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleOptions = document.querySelectorAll('.role-option');
    const roleInput = document.getElementById('selected-role');
    
    roleOptions.forEach(option => {
        option.addEventListener('click', function() {
            roleOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            roleInput.value = this.getAttribute('data-role');
        });
    });
    
    // Auto-hide error after 5 seconds
    const errorMsg = document.getElementById('error-message');
    if (errorMsg) {
        setTimeout(() => {
            errorMsg.style.opacity = '0';
            setTimeout(() => errorMsg.remove(), 500);
        }, 5000);
    }
    
    // Form submission feedback
    const form = document.getElementById('login-form');
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            submitBtn.disabled = true;
            
            // Re-enable after 5 seconds if page doesn't redirect
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        }
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>