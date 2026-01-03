<?php
$pageTitle = 'Register - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Create Patient Account</h2>
            <p>Register to access healthcare services</p>
        </div>
        <div class="auth-body">
            <?php if (isset($error) && $error): ?>
            <div class="error-message active">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($success) && $success): ?>
            <div class="success-message active">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <form action="<?php echo APP_URL; ?>/public/index.php?page=register" method="POST" id="signup-form">
                <div class="form-group">
                    <label for="signup-name">Full Name *</label>
                    <input type="text" id="signup-name" name="name" class="form-control" 
                           placeholder="Enter your full name" required 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="signup-email">Email Address *</label>
                    <input type="email" id="signup-email" name="email" class="form-control" 
                           placeholder="Enter your email address" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="signup-phone">Phone Number *</label>
                    <input type="tel" id="signup-phone" name="phone" class="form-control" 
                           placeholder="Enter your phone number" required 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="signup-password">Password *</label>
                    <input type="password" id="signup-password" name="password" class="form-control" 
                           placeholder="Create a password (min. 6 characters)" required>
                    <small style="color: var(--text-light); display: block; margin-top: 5px;">
                        Password must be at least 6 characters long
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="signup-confirm-password">Confirm Password *</label>
                    <input type="password" id="signup-confirm-password" name="confirm_password" 
                           class="form-control" placeholder="Confirm your password" required>
                </div>
                
                <div class="form-check">
                    <input type="checkbox" id="signup-terms" name="terms" required>
                    <label for="signup-terms">
                        I agree to the <a href="#" id="terms-link">Terms & Conditions</a> and 
                        <a href="#" id="privacy-link">Privacy Policy</a> *
                    </label>
                </div>
                
                <div style="background-color: var(--primary-light); padding: 15px; border-radius: var(--border-radius-sm); margin-bottom: 20px;">
                    <p style="font-size: 0.9rem; margin-bottom: 0; color: var(--primary-dark);">
                        <i class="fas fa-info-circle"></i> 
                        Note: Registration is for <strong>Patient accounts only</strong>. 
                        Doctor and Admin accounts are created by hospital administration.
                    </p>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="auth-switch">
                <p>Already have an account? <a href="<?php echo APP_URL; ?>/public/index.php?page=login">Log In</a></p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('terms-link').addEventListener('click', function(e) {
    e.preventDefault();
    alert('Terms & Conditions: By creating an account, you agree to our hospital policies and data protection guidelines.');
});

document.getElementById('privacy-link').addEventListener('click', function(e) {
    e.preventDefault();
    alert('Privacy Policy: Your personal information is protected and will only be used for healthcare purposes.');
});

document.getElementById('signup-form').addEventListener('submit', function(e) {
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('signup-confirm-password').value;
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long');
        return false;
    }
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match');
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
        submitBtn.disabled = true;
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>