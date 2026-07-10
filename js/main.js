/**
 * Child of Hope - Main JavaScript
 * Modern NGO Website Interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===========================
    // Initialize AOS (Animate On Scroll)
    // ===========================
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    }

    // ===========================
    // Navbar Effects on Scroll
    // ===========================
    const navbar = document.getElementById('mainNavbar');
    let lastScrollTop = 0;

    if (navbar) {
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        });
    }

    // ===========================
    // Smooth Scroll for Navigation Links
    // ===========================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            
            // Don't prevent default for forms or external links
            if (href === '#' || href === '#!' || !href) return;
            
            const targetElement = document.querySelector(href);
            
            if (targetElement) {
                e.preventDefault();
                
                // Close navbar if mobile
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const navbarToggler = document.querySelector('.navbar-toggler');
                    navbarToggler.click();
                }
                
                // Smooth scroll
                const navHeight = navbar ? navbar.offsetHeight : 0;
                const targetPosition = targetElement.offsetTop - navHeight - 10;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ===========================
    // Counter Animation for Statistics
    // ===========================
    function animateCounter(element, target) {
        const duration = 2000; // 2 seconds
        const start = 0;
        const increment = target / (duration / 16); // 60fps
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    }

    // Observe when stats section comes into view
    const statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.dataset.animated) {
                    entry.target.dataset.animated = 'true';
                    const targetAttr = entry.target.getAttribute('data-target');

                    if (targetAttr === null) {
                        entry.target.textContent = entry.target.textContent.trim() || '0';
                        return;
                    }

                    const target = parseInt(targetAttr, 10);
                    if (Number.isNaN(target)) {
                        entry.target.textContent = entry.target.textContent.trim() || '0';
                        return;
                    }

                    animateCounter(entry.target, target);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(stat => observer.observe(stat));
    }

    // ===========================
    // Newsletter Form
    // ===========================
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.querySelector('input[type="email"]').value;
            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
            button.disabled = true;
            
            // Simulate submission (replace with actual API call)
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
                button.style.background = '#10B981';
                
                this.querySelector('input[type="email"]').value = '';
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    button.style.background = '';
                }, 3000);
            }, 1500);
        });
    }

    // ===========================
    // Gallery Lightbox
    // ===========================
    const galleryLinks = document.querySelectorAll('.gallery-link');
    galleryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            // Simple placeholder - can be replaced with Lightbox.js, GLightbox, etc.
            console.log('Gallery lightbox would open here');
        });
    });

    // ===========================
    // Disable image/video downloads
    // ===========================
    const protectedMedia = document.querySelectorAll('img, video');
    protectedMedia.forEach(media => {
        media.addEventListener('contextmenu', event => event.preventDefault());
        media.addEventListener('dragstart', event => event.preventDefault());
        media.addEventListener('mousedown', event => {
            if (event.button === 2) {
                event.preventDefault();
            }
        });
    });

    // ===========================
    // Add watermark overlay to media
    // ===========================
    function addMediaWatermark(media) {
        if (media.closest('.watermark-container')) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'watermark-container';

        const overlay = document.createElement('div');
        overlay.className = 'watermark-overlay';

        const logo = document.createElement('img');
        logo.className = 'watermark-logo';
        logo.src = 'images/logo.png';
        logo.alt = 'Child of Hope watermark';

        overlay.appendChild(logo);

        media.parentNode.insertBefore(wrapper, media);
        wrapper.appendChild(media);
        wrapper.appendChild(overlay);
    }

    protectedMedia.forEach(media => addMediaWatermark(media));

    // ===========================
    // Active Nav Link on Scroll
    // ===========================
    function updateActiveNavLink() {
        const sections = document.querySelectorAll('section[id]');
        let current = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (pageYOffset >= sectionTop - 200) {
                current = section.getAttribute('id');
            }
        });

        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', updateActiveNavLink);
    updateActiveNavLink(); // Initial call

    // ===========================
    // Lazy Load Images
    // ===========================
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

    // ===========================
    // Mobile Menu Close on Link Click
    // ===========================
    const navLinks = document.querySelectorAll('.nav-link');
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (navbarCollapse && navbarCollapse.classList.contains('show') && navbarToggler) {
                navbarToggler.click();
            }
        });
    });

    // ===========================
    // Parallax Effect for Hero
    // ===========================
    const heroBackground = document.querySelector('.hero-background');
    if (heroBackground) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            if (scrolled < window.innerHeight) {
                heroBackground.style.backgroundPosition = `center ${scrolled * 0.5}px`;
            }
        });
    }

    // ===========================
    // Button Ripple Effect
    // ===========================
    function createRipple(event) {
        const button = event.currentTarget;
        const ripple = document.createElement('span');
        
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        button.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    }

    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', createRipple);
    });

    // ===========================
    // Add CSS for Ripple Effect
    // ===========================
    const style = document.createElement('style');
    style.textContent = `
        .btn {
            position: relative;
            overflow: hidden;
        }
        
        .btn .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: rippleAnimation 0.6s ease-out;
            pointer-events: none;
        }
        
        @keyframes rippleAnimation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // ===========================
    // Print Initialization Complete
    // ===========================
    console.log('🤝 Child of Hope website loaded successfully!');
});

// ===========================
// Performance: Throttle function for scroll events
// ===========================
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// ===========================
// Expose throttle to window for use in other scripts
// ===========================
window.throttle = throttle;
