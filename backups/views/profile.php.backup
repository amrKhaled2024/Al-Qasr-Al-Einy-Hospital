<?php
$pageTitle = 'My Profile - ' . APP_NAME;
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="page-header">
        <h2>My Profile</h2>
    </div>
    
    <div class="form-container">
        <form>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" class="form-control" value="<?php echo $_SESSION['user_name'] ?? 'Patient Name'; ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" value="<?php echo $_SESSION['user_email'] ?? 'patient@email.com'; ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" class="form-control" value="+20 123 456 7890">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" class="form-control" value="1990-01-01">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select class="form-control">
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea class="form-control" rows="3">123 Main Street, Cairo, Egypt</textarea>
            </div>
            
            <div class="form-group">
                <label>Emergency Contact</label>
                <input type="text" class="form-control" value="+20 987 654 3210">
            </div>
            
            <div class="form-group">
                <label>Blood Group</label>
                <input type="text" class="form-control" value="O+">
            </div>
            
            <div class="form-group">
                <label>Allergies</label>
                <textarea class="form-control" rows="2">Penicillin, Pollen</textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>