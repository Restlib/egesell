/**
 * Egesel Perakende - Admin Panel JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                if (!adminSidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    adminSidebar.classList.remove('active');
                }
            }
        });
    }
    
    // Auto hide alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
    
    // Confirm delete actions
    document.querySelectorAll('[data-confirm]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });
    
    // Image preview on file select
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                const previewId = this.id + '-preview';
                let preview = document.getElementById(previewId);
                
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = previewId;
                    preview.className = 'image-preview';
                    this.parentElement.appendChild(preview);
                }
                
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Character counter for textareas
    document.querySelectorAll('textarea[maxlength]').forEach(function(textarea) {
        const maxLength = textarea.getAttribute('maxlength');
        const counter = document.createElement('small');
        counter.className = 'char-counter';
        counter.textContent = '0 / ' + maxLength;
        textarea.parentElement.appendChild(counter);
        
        textarea.addEventListener('input', function() {
            counter.textContent = this.value.length + ' / ' + maxLength;
        });
    });
    
    // Table row click to edit
    document.querySelectorAll('.admin-table tbody tr[data-edit-url]').forEach(function(row) {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            if (!e.target.closest('a') && !e.target.closest('button')) {
                window.location.href = this.dataset.editUrl;
            }
        });
    });
    
});

// Utility functions
function formatPrice(price) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY'
    }).format(price);
}

function showLoading(button) {
    button.disabled = true;
    button.dataset.originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yükleniyor...';
}

function hideLoading(button) {
    button.disabled = false;
    button.innerHTML = button.dataset.originalText;
}
