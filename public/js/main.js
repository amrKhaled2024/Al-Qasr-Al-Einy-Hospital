// Main JavaScript file for the hospital management system
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    initTooltips();
    
    // Initialize date pickers
    initDatePickers();
    
    // Initialize form validation
    initFormValidation();
    
    // Initialize notification system
    initNotifications();
});

function initTooltips() {
    // Add tooltips to buttons with data-tooltip attribute
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            tooltip.style.position = 'absolute';
            tooltip.style.background = 'rgba(0,0,0,0.8)';
            tooltip.style.color = 'white';
            tooltip.style.padding = '5px 10px';
            tooltip.style.borderRadius = '4px';
            tooltip.style.fontSize = '0.85rem';
            tooltip.style.zIndex = '1000';
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
            tooltip.style.left = (rect.left + (rect.width - tooltip.offsetWidth) / 2) + 'px';
            
            this._tooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                this._tooltip = null;
            }
        });
    });
}

function initDatePickers() {
    // Set min date for appointment booking
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    
    dateInputs.forEach(input => {
        if (!input.value) {
            input.value = today;
        }
        input.min = today;
    });
}

function initFormValidation() {
    // Password confirmation validation
    const passwordForms = document.querySelectorAll('form[id$="form"]');
    passwordForms.forEach(form => {
        const passwordField = form.querySelector('input[name="password"]');
        const confirmField = form.querySelector('input[name="confirm_password"]');
        
        if (passwordField && confirmField) {
            form.addEventListener('submit', function(e) {
                if (passwordField.value !== confirmField.value) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    confirmField.focus();
                    return false;
                }
                
                if (passwordField.value.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long!');
                    passwordField.focus();
                    return false;
                }
            });
        }
    });
}

function initNotifications() {
    // Check for new notifications every 30 seconds
    if (document.querySelector('.notifications-container')) {
        setInterval(checkNewNotifications, 30000);
    }
}

function checkNewNotifications() {
    // In a real app, this would make an AJAX request
    console.log('Checking for new notifications...');
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeModal(e.target.id);
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal-overlay[style*="display: flex"]');
        modals.forEach(modal => {
            closeModal(modal.id);
        });
    }
});

// Form submission feedback
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
        }
    });
});

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert, .error-message, .success-message');
    alerts.forEach(alert => {
        if (alert.classList.contains('active')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        }
    });
}, 1000);

// Tab functionality for role selection
function activateRoleTab(role) {
    document.querySelectorAll('.role-option').forEach(tab => {
        tab.classList.remove('active');
    });
    
    const activeTab = document.querySelector(`.role-option[data-role="${role}"]`);
    if (activeTab) {
        activeTab.classList.add('active');
    }
}

// Dynamic form field toggling based on role
function toggleFormFields(role) {
    const doctorFields = document.querySelectorAll('.doctor-field');
    const patientFields = document.querySelectorAll('.patient-field');
    const adminFields = document.querySelectorAll('.admin-field');
    
    // Hide all special fields
    [doctorFields, patientFields, adminFields].forEach(fields => {
        fields.forEach(field => {
            field.style.display = 'none';
        });
    });
    
    // Show fields based on role
    switch(role) {
        case 'doctor':
            doctorFields.forEach(field => field.style.display = 'block');
            break;
        case 'patient':
            patientFields.forEach(field => field.style.display = 'block');
            break;
        case 'admin':
            adminFields.forEach(field => field.style.display = 'block');
            break;
    }
}

// Export data functions
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let row of rows) {
        const cells = row.querySelectorAll('th, td');
        const rowData = [];
        
        for (let cell of cells) {
            // Skip action columns
            if (cell.querySelector('button, a, select')) continue;
            rowData.push(cell.innerText.replace(/,/g, ';'));
        }
        
        if (rowData.length > 0) {
            csv.push(rowData.join(','));
        }
    }
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.href = url;
    a.download = filename || 'export.csv';
    a.click();
    
    window.URL.revokeObjectURL(url);
}

// Print functionality
function printPage(elementId) {
    const element = elementId ? document.getElementById(elementId) : document.body;
    const originalContents = document.body.innerHTML;
    
    if (elementId) {
        document.body.innerHTML = element.innerHTML;
    }
    
    window.print();
    
    if (elementId) {
        document.body.innerHTML = originalContents;
    }
}

// Search functionality for tables
function filterTable(tableId, searchId) {
    const searchInput = document.getElementById(searchId);
    const table = document.getElementById(tableId);
    
    if (!searchInput || !table) return;
    
    searchInput.addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
}

// Initialize date picker ranges
function initDateRangePickers() {
    const startDateInputs = document.querySelectorAll('input[data-date-start]');
    const endDateInputs = document.querySelectorAll('input[data-date-end]');
    
    startDateInputs.forEach(startInput => {
        const endInputId = startInput.getAttribute('data-date-end');
        const endInput = document.getElementById(endInputId);
        
        if (endInput) {
            startInput.addEventListener('change', function() {
                endInput.min = this.value;
                if (endInput.value && endInput.value < this.value) {
                    endInput.value = this.value;
                }
            });
        }
    });
}

// Toggle password visibility
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
}

// Character counter for textareas
function initCharacterCounters() {
    const textareas = document.querySelectorAll('textarea[data-maxlength]');
    
    textareas.forEach(textarea => {
        const maxLength = parseInt(textarea.getAttribute('data-maxlength'));
        const counterId = textarea.id + '-counter';
        
        // Create counter element
        const counter = document.createElement('div');
        counter.id = counterId;
        counter.className = 'character-counter';
        counter.style.fontSize = '0.8rem';
        counter.style.color = 'var(--text-light)';
        counter.style.textAlign = 'right';
        counter.style.marginTop = '5px';
        
        textarea.parentNode.appendChild(counter);
        
        function updateCounter() {
            const length = textarea.value.length;
            counter.textContent = `${length}/${maxLength} characters`;
            
            if (length > maxLength) {
                counter.style.color = 'var(--danger-color)';
            } else if (length > maxLength * 0.8) {
                counter.style.color = 'var(--warning-color)';
            } else {
                counter.style.color = 'var(--text-light)';
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial call
    });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initCharacterCounters();
    initDateRangePickers();
    
    // Initialize search for tables with search inputs
    const searchInputs = document.querySelectorAll('input[data-search-table]');
    searchInputs.forEach(input => {
        const tableId = input.getAttribute('data-search-table');
        filterTable(tableId, input.id);
    });
});