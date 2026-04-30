/**
 * Egesel Perakende - Ana JavaScript Dosyası
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.querySelector('i').classList.toggle('fa-bars');
            this.querySelector('i').classList.toggle('fa-times');
        });
    }
    
    // Alert Close
    document.querySelectorAll('.alert-close').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
    
    // Auto hide alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
    
    // Favorite Toggle
    document.querySelectorAll('.favorite-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');
            const button = this;
            
            fetch('favori-toggle.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added') {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        showToast('Favorilere eklendi');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        showToast('Favorilerden çıkarıldı');
                        
                        // Favoriler sayfasındaysa kartı kaldır
                        if (window.location.pathname.includes('favoriler.php')) {
                            button.closest('.product-card').remove();
                        }
                    }
                } else {
                    showToast(data.message || 'Bir hata oluştu', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Bir hata oluştu', 'error');
            });
        });
    });
    
    // Favorite Button Large (Product Detail)
    document.querySelectorAll('.favorite-btn-lg').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');
            const button = this;
            
            fetch('favori-toggle.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added') {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        button.innerHTML = '<i class="fas fa-heart"></i> Favorilerimde';
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        button.innerHTML = '<i class="far fa-heart"></i> Favorilere Ekle';
                    }
                    showToast(data.message);
                } else {
                    showToast(data.message || 'Bir hata oluştu', 'error');
                }
            });
        });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
});

// Toast notification
function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.toast').forEach(t => t.remove());
    
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.innerHTML = message;
    
    // Style the toast
    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '100px',
        right: '30px',
        padding: '14px 24px',
        background: type === 'success' ? '#22c55e' : '#ef4444',
        color: 'white',
        borderRadius: '8px',
        boxShadow: '0 4px 20px rgba(0,0,0,0.2)',
        zIndex: '9999',
        animation: 'slideIn 0.3s ease',
        fontSize: '14px',
        fontWeight: '500'
    });
    
    document.body.appendChild(toast);
    
    // Add animation keyframes
    if (!document.querySelector('#toast-styles')) {
        const style = document.createElement('style');
        style.id = 'toast-styles';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Remove toast after 3 seconds
    setTimeout(function() {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 3000);
}

// Lazy load images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                observer.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}
