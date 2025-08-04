// Buyunic Technologies Enrollment Portal - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeFormValidation();
    initializeFileUploads();
    initializeNotifications();
    initializeModals();
    initializeTooltips();
    initializeSessionTimeout();
    
    // Auto-hide flash messages
    setTimeout(hideFlashMessages, 5000);
});

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const fieldType = field.type;
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        errorMessage = 'This field is required.';
        isValid = false;
    }
    
    // Email validation
    else if (fieldType === 'email' && value && !isValidEmail(value)) {
        errorMessage = 'Please enter a valid email address.';
        isValid = false;
    }
    
    // Phone validation
    else if (fieldName === 'phone' && value && !isValidPhone(value)) {
        errorMessage = 'Please enter a valid Uganda phone number.';
        isValid = false;
    }
    
    // NIN validation
    else if (fieldName === 'nin' && value && !isValidNIN(value)) {
        errorMessage = 'Please enter a valid NIN (CM followed by 13 digits).';
        isValid = false;
    }
    
    // Password validation
    else if (fieldType === 'password' && value && value.length < 8) {
        errorMessage = 'Password must be at least 8 characters long.';
        isValid = false;
    }
    
    // Confirm password validation
    else if (fieldName === 'confirm_password' && value) {
        const passwordField = document.querySelector('input[name="password"]');
        if (passwordField && value !== passwordField.value) {
            errorMessage = 'Passwords do not match.';
            isValid = false;
        }
    }
    
    if (isValid) {
        clearFieldError(field);
    } else {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('error');
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-sm mt-1';
    errorDiv.style.color = 'var(--error-color)';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('error');
    const errorMessage = field.parentNode.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

// Validation helper functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^(\+256|0)[7-9][0-9]{8}$/;
    return phoneRegex.test(phone);
}

function isValidNIN(nin) {
    const ninRegex = /^CM\d{13}$/;
    return ninRegex.test(nin);
}

// File upload functionality
function initializeFileUploads() {
    const uploadAreas = document.querySelectorAll('.file-upload-area');
    
    uploadAreas.forEach(area => {
        const fileInput = area.querySelector('input[type="file"]');
        const fileList = area.nextElementSibling;
        
        // Drag and drop events
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        area.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            handleFileSelection(files, fileInput, fileList);
        });
        
        // Click to select files
        area.addEventListener('click', function() {
            fileInput.click();
        });
        
        // File input change event
        fileInput.addEventListener('change', function() {
            handleFileSelection(this.files, fileInput, fileList);
        });
    });
}

function handleFileSelection(files, fileInput, fileList) {
    const maxFiles = 10;
    const maxSize = 1048576; // 1MB
    const allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];
    
    if (files.length > maxFiles) {
        showNotification('You can only upload up to ' + maxFiles + ' files.', 'error');
        return;
    }
    
    let validFiles = [];
    
    Array.from(files).forEach(file => {
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (file.size > maxSize) {
            showNotification(`File "${file.name}" is too large. Maximum size is 1MB.`, 'error');
            return;
        }
        
        if (!allowedTypes.includes(fileExtension)) {
            showNotification(`File "${file.name}" has an invalid type. Allowed types: ${allowedTypes.join(', ')}`, 'error');
            return;
        }
        
        validFiles.push(file);
    });
    
    if (validFiles.length > 0) {
        displayFileList(validFiles, fileList);
        updateFileInput(fileInput, validFiles);
    }
}

function displayFileList(files, fileList) {
    if (!fileList) return;
    
    fileList.innerHTML = '';
    
    files.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <div class="file-info">
                <span class="file-name">${file.name}</span>
                <span class="file-size">(${formatFileSize(file.size)})</span>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${index}, this)">Remove</button>
        `;
        fileList.appendChild(fileItem);
    });
}

function removeFile(index, button) {
    const fileItem = button.closest('.file-item');
    const fileList = fileItem.parentNode;
    const uploadArea = fileList.previousElementSibling;
    const fileInput = uploadArea.querySelector('input[type="file"]');
    
    // Remove from display
    fileItem.remove();
    
    // Update file input (this is a workaround since we can't directly modify FileList)
    const dt = new DataTransfer();
    const files = Array.from(fileInput.files);
    files.splice(index, 1);
    
    files.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
}

function updateFileInput(fileInput, files) {
    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Notification system
function initializeNotifications() {
    // Auto-hide notifications after 5 seconds
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        setTimeout(() => {
            hideNotification(notification);
        }, 5000);
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type} show`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button type="button" class="notification-close" onclick="hideNotification(this.parentNode.parentNode)">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        hideNotification(notification);
    }, 5000);
}

function hideNotification(notification) {
    notification.classList.remove('show');
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

function hideFlashMessages() {
    const flashMessages = document.querySelectorAll('.alert');
    flashMessages.forEach(message => {
        message.style.opacity = '0';
        setTimeout(() => {
            if (message.parentNode) {
                message.parentNode.removeChild(message);
            }
        }, 300);
    });
}

// Modal functionality
function initializeModals() {
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const modals = document.querySelectorAll('.modal');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                showModal(modal);
            }
        });
    });
    
    modals.forEach(modal => {
        const closeButtons = modal.querySelectorAll('.modal-close, [data-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                hideModal(modal);
            });
        });
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal(this);
            }
        });
    });
}

function showModal(modal) {
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function hideModal(modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Tooltip functionality
function initializeTooltips() {
    const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
    
    tooltipTriggers.forEach(trigger => {
        trigger.addEventListener('mouseenter', function() {
            showTooltip(this);
        });
        
        trigger.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
}

function showTooltip(element) {
    const tooltipText = element.getAttribute('data-tooltip');
    if (!tooltipText) return;
    
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = tooltipText;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 0.5rem;
        border-radius: 4px;
        font-size: 0.875rem;
        z-index: 1000;
        pointer-events: none;
        white-space: nowrap;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

// Session timeout handling
function initializeSessionTimeout() {
    let timeoutWarning;
    let sessionTimeout;
    const warningTime = 13 * 60 * 1000; // 13 minutes
    const timeoutTime = 15 * 60 * 1000; // 15 minutes
    
    function resetTimers() {
        clearTimeout(timeoutWarning);
        clearTimeout(sessionTimeout);
        
        timeoutWarning = setTimeout(() => {
            showSessionWarning();
        }, warningTime);
        
        sessionTimeout = setTimeout(() => {
            handleSessionTimeout();
        }, timeoutTime);
    }
    
    function showSessionWarning() {
        if (confirm('Your session will expire in 2 minutes. Click OK to extend your session.')) {
            // Make an AJAX request to extend session
            fetch('includes/extend_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            }).then(() => {
                resetTimers();
            });
        }
    }
    
    function handleSessionTimeout() {
        alert('Your session has expired. You will be redirected to the login page.');
        window.location.href = 'auth/login.php';
    }
    
    // Reset timers on user activity
    ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
        document.addEventListener(event, resetTimers, true);
    });
    
    // Initialize timers
    resetTimers();
}

// AJAX form submission
function submitFormAjax(form, callback) {
    const formData = new FormData(form);
    const url = form.action || window.location.href;
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (callback) {
            callback(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// Progress bar animation
function updateProgressBar(percentage, progressBar) {
    if (!progressBar) return;
    
    const progressBarFill = progressBar.querySelector('.progress-bar');
    if (progressBarFill) {
        progressBarFill.style.width = percentage + '%';
        progressBarFill.textContent = Math.round(percentage) + '%';
    }
}

// Auto-save functionality
function initializeAutoSave(formId, saveUrl, interval = 30000) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    let autoSaveTimer;
    let hasChanges = false;
    
    // Track changes
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            hasChanges = true;
        });
    });
    
    function autoSave() {
        if (!hasChanges) return;
        
        const formData = new FormData(form);
        formData.append('auto_save', '1');
        
        fetch(saveUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hasChanges = false;
                showNotification('Draft saved automatically', 'success');
            }
        })
        .catch(error => {
            console.error('Auto-save error:', error);
        });
    }
    
    // Start auto-save timer
    autoSaveTimer = setInterval(autoSave, interval);
    
    // Save before page unload
    window.addEventListener('beforeunload', (e) => {
        if (hasChanges) {
            autoSave();
            e.preventDefault();
            e.returnValue = '';
        }
    });
}

// Data table functionality
function initializeDataTable(tableId, options = {}) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    // Add search functionality
    if (options.search !== false) {
        addTableSearch(table);
    }
    
    // Add sorting functionality
    if (options.sort !== false) {
        addTableSorting(table);
    }
    
    // Add pagination
    if (options.pagination !== false) {
        addTablePagination(table, options.pageSize || 10);
    }
}

function addTableSearch(table) {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search...';
    searchInput.className = 'form-control mb-3';
    
    table.parentNode.insertBefore(searchInput, table);
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

function addTableSorting(table) {
    const headers = table.querySelectorAll('th');
    
    headers.forEach((header, index) => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', () => {
            sortTable(table, index);
        });
    });
}

function sortTable(table, columnIndex) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    const isAscending = !table.dataset.sortAsc || table.dataset.sortAsc === 'false';
    
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        if (isAscending) {
            return aValue.localeCompare(bValue, undefined, { numeric: true });
        } else {
            return bValue.localeCompare(aValue, undefined, { numeric: true });
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
    table.dataset.sortAsc = isAscending.toString();
}

// Utility functions
function formatCurrency(amount) {
    return 'UGX ' + new Intl.NumberFormat().format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export functions for global use
window.BuyunicPortal = {
    showNotification,
    hideNotification,
    showModal,
    hideModal,
    validateForm,
    submitFormAjax,
    updateProgressBar,
    initializeAutoSave,
    initializeDataTable,
    formatCurrency,
    formatDate,
    formatDateTime
};