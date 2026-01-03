<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle ?? 'Kasr Al Ainy Hospital'; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
  <!-- Link to your CSS file -->
   <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/admin.css">
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  
  <!-- Add this style for mobile navbar -->
  <style>
    /* Mobile Menu Button */
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--primary-color);
        cursor: pointer;
        padding: 10px;
        z-index: 1001;
    }
    
    /* Mobile Sidebar */
    .mobile-sidebar {
        position: fixed;
        top: 0;
        right: -300px;
        width: 280px;
        height: 100vh;
        background: var(--white);
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        transition: right 0.3s ease;
        overflow-y: auto;
        padding-top: 20px;
    }
    
    .mobile-sidebar.active {
        right: 0;
    }
    
    .mobile-sidebar-header {
        padding: 20px;
        border-bottom: 1px solid var(--gray-medium);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .mobile-sidebar-content {
        padding: 20px;
    }
    
    .mobile-sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .mobile-sidebar li {
        margin-bottom: 10px;
    }
    
    .mobile-sidebar a {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 15px;
        border-radius: 8px;
        transition: all 0.3s;
        color: var(--text-color);
        text-decoration: none;
        font-size: 1rem;
    }
    
    .mobile-sidebar a:hover {
        background: var(--primary-light);
        color: var(--primary-color);
    }
    
    .mobile-sidebar a.active {
        background: var(--primary-color);
        color: var(--white);
    }
    
    .mobile-sidebar a i {
        width: 20px;
        text-align: center;
    }
    
    /* User Info in Mobile Sidebar */
    .mobile-user-info {
        padding: 20px;
        border-bottom: 1px solid var(--gray-medium);
        margin-bottom: 20px;
        text-align: center;
    }
    
    .mobile-user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.2rem;
        color: var(--primary-color);
        font-weight: bold;
    }
    
    .mobile-user-name {
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--primary-dark);
    }
    
    .mobile-user-role {
        display: inline-block;
        padding: 4px 12px;
        background: var(--primary-light);
        color: var(--primary-color);
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    /* Overlay */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    }
    
    .sidebar-overlay.active {
        display: block;
    }
    
    /* Close button */
    .close-sidebar {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-color);
        cursor: pointer;
        padding: 5px;
    }
    
    /* Logout button styling */
    .mobile-sidebar .logout a {
        color: var(--danger-color) !important;
    }
    
    .mobile-sidebar .logout a:hover {
        background: rgba(231, 76, 60, 0.1) !important;
    }
    
    /* Responsive styles for less than 620px */
    @media (max-width: 620px) {
        .mobile-menu-btn {
            display: block;
        }
        
        #main-nav {
            display: none;
        }
        
        .user-info {
            display: none;
        }
        
        .header-content {
            padding: 10px 0;
        }
        
        .logo {
            flex: 1;
        }
        
        .logo-text h1 {
            font-size: 1.2rem;
        }
        
        .logo-text p {
            font-size: 0.8rem;
        }
        
        .logo img {
            width: 40px !important;
        }
    }
    
    /* For screens larger than 620px - show desktop nav */
    @media (min-width: 621px) {
        .mobile-sidebar {
            display: none !important;
        }
        
        .sidebar-overlay {
            display: none !important;
        }
    }
  </style>
</head>

<body>
  <!-- Overlay for mobile sidebar -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <header>
    <div class="container">
      <div class="header-content">
        <div class="logo">
            <img src="../public/assets/images/logo.png" alt="logo" width="60px">
          <div class="logo-text">
            <h1>مستشفى طب القصر العيني</h1>
            <p>الاولى في مصر و الشرق الاوسط</p>
          </div>
        </div>
        
        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" id="mobileMenuBtn">
          <i class="fas fa-bars"></i>
        </button>
        
        <!-- Desktop Navigation (visible on > 620px) -->
        <nav id="main-nav">
          <?php if (isset($_SESSION['user_id'])): ?>
          <ul>
            <!-- Update the admin navigation section -->
            <!-- In the admin section of header.php -->
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=admin-dashboard" class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a></li>
                
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=admin-doctors" class="<?php echo ($currentPage ?? '') === 'doctors' ? 'active' : ''; ?>">
                    <i class="fas fa-user-md"></i> Doctors
                </a></li>
                
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=admin-users" class="<?php echo ($currentPage ?? '') === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Users
                </a></li>
                
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=admin-departments" class="<?php echo ($currentPage ?? '') === 'departments' ? 'active' : ''; ?>">
                    <i class="fas fa-clinic-medical"></i> Departments
                </a></li>
                
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=admin-appointments" class="<?php echo ($currentPage ?? '') === 'appointments' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i> Appointments
                </a></li>
            <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=admin-dashboard" class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=admin-doctors" class="<?php echo ($currentPage ?? '') === 'doctors' ? 'active' : ''; ?>">Doctors</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=admin-appointments" class="<?php echo ($currentPage ?? '') === 'appointments' ? 'active' : ''; ?>">Appointments</a></li>
            <?php elseif ($_SESSION['user_role'] === 'doctor'): ?>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=doctor-dashboard" class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=doctor-appointments" class="<?php echo ($currentPage ?? '') === 'appointments' ? 'active' : ''; ?>">Appointments</a></li>
            <?php elseif ($_SESSION['user_role'] === 'receptionist'): ?>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=receptionist-dashboard" class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
            <?php elseif ($_SESSION['user_role'] === 'patient'): ?>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=patient-dashboard" class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=patient-appointments" class="<?php echo ($currentPage ?? '') === 'appointments' ? 'active' : ''; ?>">Appointments</a></li>
                <li><a href="<?php echo APP_URL; ?>/public/index.php?page=patient-book" class="<?php echo ($currentPage ?? '') === 'book' ? 'active' : ''; ?>">Book Appointment</a></li>
            <?php endif; ?>
            <li class="logout"><a href="<?php echo APP_URL; ?>/public/index.php?page=logout">Logout</a></li>
          </ul>
          <?php else: ?>
          <ul>
            <li><a href="<?php echo APP_URL; ?>/public/index.php?page=home" class="<?php echo ($currentPage ?? '') === 'home' ? 'active' : ''; ?>">Home</a></li>
            <li><a href="<?php echo APP_URL; ?>/public/index.php?page=login" class="<?php echo ($currentPage ?? '') === 'login' ? 'active' : ''; ?>">Login</a></li>
            <li><a href="<?php echo APP_URL; ?>/public/index.php?page=register" class="<?php echo ($currentPage ?? '') === 'register' ? 'active' : ''; ?>">Sign Up</a></li>
          </ul>
          <?php endif; ?>
        </nav>

        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-info">
          <div class="user-avatar">
            <?php
                        $initials = '';
                        if (isset($_SESSION['user_name'])) {
                            $nameParts = explode(' ', $_SESSION['user_name']);
                            foreach ($nameParts as $part) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                        }
                        echo substr($initials, 0, 2);
                        ?>
          </div>
          <div>
            <p id="user-name"><?php echo $_SESSION['user_name'] ?? 'User'; ?></p>
            <span style="font-size: 0.8rem; color: var(--text-light);"><?php echo ucfirst($_SESSION['user_role'] ?? ''); ?></span>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Mobile Sidebar (hidden by default, shows on < 620px) -->
  <div class="mobile-sidebar" id="mobileSidebar">
    <div class="mobile-sidebar-header">
      <div class="logo" style="gap: 10px;">
        <img src="../public/assets/images/logo.png" alt="logo" width="40px">
        <div class="logo-text">
          <h1 style="font-size: 1rem;">مستشفى طب القصر العيني</h1>
          <p style="font-size: 0.7rem;">الاولى في مصر و الشرق الاوسط</p>
        </div>
      </div>
      <button class="close-sidebar" id="closeSidebar">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="mobile-user-info">
      <div class="mobile-user-avatar">
        <?php
        $initials = '';
        if (isset($_SESSION['user_name'])) {
            $nameParts = explode(' ', $_SESSION['user_name']);
            foreach ($nameParts as $part) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        echo substr($initials, 0, 2);
        ?>
      </div>
      <h3 class="mobile-user-name"><?php echo $_SESSION['user_name'] ?? 'User'; ?></h3>
      <span class="mobile-user-role"><?php echo ucfirst($_SESSION['user_role'] ?? ''); ?></span>
    </div>
    <?php endif; ?>
    
    <div class="mobile-sidebar-content">
      <?php if (isset($_SESSION['user_id'])): ?>
      <ul>
        <!-- Admin Navigation -->
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=admin-dashboard" 
                   class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=admin-doctors" 
                   class="<?php echo ($currentPage ?? '') === 'doctors' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-user-md"></i> Doctors
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=admin-users" 
                   class="<?php echo ($currentPage ?? '') === 'users' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=admin-departments" 
                   class="<?php echo ($currentPage ?? '') === 'departments' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-clinic-medical"></i> Departments
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=admin-appointments" 
                   class="<?php echo ($currentPage ?? '') === 'appointments' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-calendar-check"></i> Appointments
                </a>
            </li>
            
        <?php elseif ($_SESSION['user_role'] === 'doctor'): ?>
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=doctor-dashboard" 
                   class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=doctor-appointments" 
                   class="<?php echo ($currentPage ?? '') === 'appointments' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-calendar-check"></i> Appointments
                </a>
            </li>
            
        <?php elseif ($_SESSION['user_role'] === 'receptionist'): ?>
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=receptionist-dashboard" 
                   class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
        <?php elseif ($_SESSION['user_role'] === 'patient'): ?>
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=patient-dashboard" 
                   class="<?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=patient-appointments" 
                   class="<?php echo ($currentPage ?? '') === 'appointments' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-calendar-check"></i> Appointments
                </a>
            </li>
            
            <li>
                <a href="<?php echo APP_URL; ?>/public/index.php?page=patient-book" 
                   class="<?php echo ($currentPage ?? '') === 'book' ? 'active' : ''; ?>"
                   onclick="closeMobileSidebar()">
                    <i class="fas fa-calendar-plus"></i> Book Appointment
                </a>
            </li>
        <?php endif; ?>
        
        <li class="logout">
            <a href="<?php echo APP_URL; ?>/public/index.php?page=logout" onclick="closeMobileSidebar()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
      </ul>
      <?php else: ?>
      <ul>
        <li>
            <a href="<?php echo APP_URL; ?>/public/index.php?page=home" 
               class="<?php echo ($currentPage ?? '') === 'home' ? 'active' : ''; ?>"
               onclick="closeMobileSidebar()">
                <i class="fas fa-home"></i> Home
            </a>
        </li>
        
        <li>
            <a href="<?php echo APP_URL; ?>/public/index.php?page=login" 
               class="<?php echo ($currentPage ?? '') === 'login' ? 'active' : ''; ?>"
               onclick="closeMobileSidebar()">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </li>
        
        <li>
            <a href="<?php echo APP_URL; ?>/public/index.php?page=register" 
               class="<?php echo ($currentPage ?? '') === 'register' ? 'active' : ''; ?>"
               onclick="closeMobileSidebar()">
                <i class="fas fa-user-plus"></i> Sign Up
            </a>
        </li>
      </ul>
      <?php endif; ?>
    </div>
  </div>

  <main>
  
  <!-- JavaScript for mobile sidebar -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const closeSidebar = document.getElementById('closeSidebar');
        const body = document.body;

        // Function to open mobile sidebar
        function openMobileSidebar() {
            mobileSidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            body.style.overflow = 'hidden';
        }

        // Function to close mobile sidebar
        function closeMobileSidebar() {
            mobileSidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            body.style.overflow = '';
        }

        // Toggle mobile sidebar
        mobileMenuBtn.addEventListener('click', openMobileSidebar);
        closeSidebar.addEventListener('click', closeMobileSidebar);
        sidebarOverlay.addEventListener('click', closeMobileSidebar);

        // Close sidebar when clicking on a link
        const mobileLinks = mobileSidebar.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Don't close immediately for external links or special cases
                if (!this.href.includes('logout')) {
                    setTimeout(closeMobileSidebar, 300);
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 620) {
                closeMobileSidebar();
            }
        });

        // Keyboard shortcut to close sidebar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileSidebar();
            }
        });

        // Make function available globally
        window.closeMobileSidebar = closeMobileSidebar;
    });

    // Add swipe to close functionality
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function(e) {
        if (e.target.closest('.mobile-sidebar')) {
            touchStartX = e.changedTouches[0].screenX;
        }
    });

    document.addEventListener('touchend', function(e) {
        if (e.target.closest('.mobile-sidebar')) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;
        
        // Swipe left to close (right to left swipe)
        if (swipeDistance < -swipeThreshold) {
            closeMobileSidebar();
        }
    }
  </script>