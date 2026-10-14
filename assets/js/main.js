/**
 * Bangladesh Students Community Contest Platform - Core JS
 */

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initUploadForm();
});

// ==========================================
// Navigation & Modal Toggles
// ==========================================

function initMobileMenu() {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    const openIcon = document.getElementById('menu-icon-open');
    const closeIcon = document.getElementById('menu-icon-close');
    
    if (!btn || !menu) return;
    
    btn.addEventListener('click', () => {
        const isExpanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', !isExpanded);
        menu.classList.toggle('hidden');
        openIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });
}

function toggleMobileMenu() {
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    const openIcon = document.getElementById('menu-icon-open');
    const closeIcon = document.getElementById('menu-icon-close');
    
    if (menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
        openIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
    }
}

// Global Help Modal Functions
function openHelpModal() {
    const modal = document.getElementById('help-modal');
    if (!modal) return;
    
    modal.classList.remove('pointer-events-none');
    modal.classList.add('opacity-100');
    
    const card = modal.querySelector('.relative');
    if (card) {
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
    }
    
    // Prevent background scrolling
    document.body.classList.add('overflow-hidden');
}

function closeHelpModal() {
    const modal = document.getElementById('help-modal');
    if (!modal) return;
    
    modal.classList.add('pointer-events-none');
    modal.classList.remove('opacity-100');
    
    const card = modal.querySelector('.relative');
    if (card) {
        card.classList.add('scale-95');
        card.classList.remove('scale-100');
    }
    
    // Restore scrolling
    document.body.classList.remove('overflow-hidden');
}

// ==========================================
// Contest Submission Portal Logic
// ==========================================

function initUploadForm() {
    const form = document.getElementById('contest-upload-form');
    if (!form) return;
    
    const fileInput = document.getElementById('pdf_file');
    const fileArea = document.getElementById('file-drag-area');
    const fileNameDisplay = document.getElementById('selected-file-name');
    const fileSizeDisplay = document.getElementById('selected-file-size');
    const fileDetailsBox = document.getElementById('file-details-box');
    const selectFilePrompt = document.getElementById('select-file-prompt');
    const alertBox = document.getElementById('form-alert-box');
    const alertText = document.getElementById('form-alert-text');
    const btnSubmit = document.getElementById('btn-submit-text');
    const btnLoader = document.getElementById('btn-submit-loader');
    const submitBtn = document.getElementById('submit-btn');
    
    // Drag and Drop listeners
    if (fileArea && fileInput) {
        ['dragenter', 'dragover'].forEach(eventName => {
            fileArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                fileArea.classList.add('border-emerald-500', 'bg-emerald-500/5');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            fileArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                fileArea.classList.remove('border-emerald-500', 'bg-emerald-500/5');
            }, false);
        });
        
        fileArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                fileInput.files = files;
                handleFileSelection(files[0]);
            }
        });
        
        fileArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', (e) => {
            if (fileInput.files.length) {
                handleFileSelection(fileInput.files[0]);
            }
        });
    }
    
    // Validate and show file stats
    function handleFileSelection(file) {
        // Clear previous alerts
        hideAlert();
        
        if (!file) {
            resetFileSelector();
            return;
        }
        
        // 1. Verify extension
        const ext = file.name.split('.').pop().toLowerCase();
        if (ext !== 'pdf') {
            showAlert('Invalid file format. Please upload a PDF (.pdf) file.');
            resetFileSelector();
            return;
        }
        
        // 2. Verify file size (100MB limit)
        const maxSize = 100 * 1024 * 1024; // 100MB
        if (file.size > maxSize) {
            showAlert('File is too large. Maximum allowed size is 100MB.');
            resetFileSelector();
            return;
        }
        
        // Populate UI details
        fileNameDisplay.textContent = file.name;
        fileSizeDisplay.textContent = formatBytes(file.size);
        
        // Transition panel states
        selectFilePrompt.classList.add('hidden');
        fileDetailsBox.classList.remove('hidden');
        fileArea.classList.add('border-emerald-500/40', 'bg-emerald-500/5');
    }
    
    function resetFileSelector() {
        fileInput.value = '';
        selectFilePrompt.classList.remove('hidden');
        fileDetailsBox.classList.add('hidden');
        fileArea.classList.remove('border-emerald-500/40', 'bg-emerald-500/5');
    }
    
    function showAlert(message) {
        alertText.textContent = message;
        alertBox.classList.remove('hidden');
        // Scroll to alert
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    function hideAlert() {
        alertBox.classList.add('hidden');
        alertText.textContent = '';
    }
    
    // Form AJAX Submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        hideAlert();
        
        // Basic fields check
        const studentName = document.getElementById('student_name').value.trim();
        const department = document.getElementById('department').value.trim();
        const semester = document.getElementById('semester').value;
        
        if (!studentName || !department || !semester) {
            showAlert('Please fill in all details.');
            return;
        }
        
        if (!fileInput.files.length) {
            showAlert('Please select your contest PDF entry.');
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        btnSubmit.textContent = 'Uploading Entry...';
        btnLoader.classList.remove('hidden');
        
        // Collect form data
        const formData = new FormData(form);
        
        // Post via fetch
        fetch(form.getAttribute('action'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            let data;
            try {
                data = await response.json();
            } catch (e) {
                throw new Error('Network error. Please try again.');
            }
            if (!response.ok && (!data || !data.message)) {
                throw new Error('Network error. Please try again.');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Perform Success Screen Transition
                showSuccessScreen(studentName);
            } else {
                showAlert(data.message || 'An error occurred during submission.');
                resetLoadingState();
            }
        })
        .catch(err => {
            showAlert(err.message || 'Server error. Please verify your connection.');
            resetLoadingState();
        });
    });
    
    function resetLoadingState() {
        submitBtn.disabled = false;
        btnSubmit.textContent = 'Submit Contest Entry';
        btnLoader.classList.add('hidden');
    }
    
    function showSuccessScreen(name) {
        const uploadBox = document.getElementById('contest-upload-card');
        const successBox = document.getElementById('contest-success-card');
        const successName = document.getElementById('success-student-name');
        
        if (uploadBox && successBox) {
            // Write student name to screen
            if (successName) successName.textContent = name;
            
            // Swap cards with slide transition
            uploadBox.classList.add('hidden');
            successBox.classList.remove('hidden');
            successBox.scrollIntoView({ behavior: 'smooth' });
        }
    }
}

// Utility formats
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
