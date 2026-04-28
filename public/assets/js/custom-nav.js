/**
 * MartinezService - Custom Navigation Handler
 * Handles sidebar navigation and mobile menu without CSP violations
 */

(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // ========================================
        // MOBILE MENU TOGGLE
        // ========================================
        const iconNavbarSidenav = document.getElementById('iconNavbarSidenav');
        const iconSidenav = document.getElementById('iconSidenav');
        const sidenav = document.getElementById('sidenav-main');
        const body = document.getElementsByTagName('body')[0];
        
        // Toggle sidebar when clicking hamburger menu
        if (iconNavbarSidenav) {
            iconNavbarSidenav.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                if (body.classList.contains('g-sidenav-show')) {
                    // Cerrar
                    body.classList.remove('g-sidenav-show');
                    body.classList.remove('g-sidenav-pinned');
                    body.classList.add('g-sidenav-hidden');
                } else {
                    // Abrir
                    body.classList.add('g-sidenav-show');
                    body.classList.add('g-sidenav-pinned');
                    body.classList.remove('g-sidenav-hidden');
                }
            }, { passive: false });
        }
        
        // Close button inside sidebar (X)
        if (iconSidenav) {
            iconSidenav.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                body.classList.remove('g-sidenav-pinned');
                body.classList.remove('g-sidenav-show');
                body.classList.add('g-sidenav-hidden');
            }, { passive: false });
        }
        
        // ========================================
        // NAVIGATION LINKS
        // ========================================
        const navLinks = document.querySelectorAll('#sidenav-main a.nav-link');
        
        navLinks.forEach(function(link) {
            const href = link.getAttribute('href');
            
            // Skip logout link and empty hrefs
            if (!href || href === '#') {
                return;
            }
            
            // Remove any existing click handlers
            link.onclick = null;
            
            // Add our click handler with capture phase
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const targetHref = this.getAttribute('href');
                
                // Force navigation
                window.location.href = targetHref;
                
                return false;
            }, true);
        });
        
        // ========================================
        // CLOSE SIDEBAR ON LINK CLICK (MOBILE)
        // ========================================
        document.querySelectorAll('#sidenav-main a.nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1200) {
                    // Cerrar inmediatamente
                    body.classList.remove('g-sidenav-show');
                    body.classList.remove('g-sidenav-pinned');
                    body.classList.add('g-sidenav-hidden');
                }
            }, { passive: true });
        });
        
        // ========================================
        // CLOSE SIDEBAR ON OVERLAY CLICK (MOBILE)
        // ========================================
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 1200 && body.classList.contains('g-sidenav-show')) {
                // Si el click fue fuera del sidebar y no fue en el botón hamburguesa
                if (sidenav && !sidenav.contains(e.target)) {
                    if (iconNavbarSidenav && !iconNavbarSidenav.contains(e.target)) {
                        body.classList.remove('g-sidenav-show');
                        body.classList.remove('g-sidenav-pinned');
                        body.classList.add('g-sidenav-hidden');
                    }
                }
            }
        });
        
        // ========================================
        // PREVENT SCROLL WHEN MENU IS OPEN
        // ========================================
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (body.classList.contains('g-sidenav-show') && window.innerWidth < 1200) {
                        body.style.overflow = 'hidden';
                    } else {
                        body.style.overflow = '';
                    }
                }
            });
        });
        
        observer.observe(body, { attributes: true });
        
        // ========================================
        // RESPONSIVE BEHAVIOR ON RESIZE
        // ========================================
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1200) {
                // Desktop: show sidebar by default
                body.classList.add('g-sidenav-pinned');
                body.classList.remove('g-sidenav-hidden');
            } else {
                // Mobile: hide sidebar by default
                if (!body.classList.contains('g-sidenav-show')) {
                    body.classList.add('g-sidenav-hidden');
                    body.classList.remove('g-sidenav-pinned');
                }
            }
        });
        
        // Initial state based on screen size
        function setInitialState() {
            if (window.innerWidth < 1200) {
                body.classList.add('g-sidenav-hidden');
                body.classList.remove('g-sidenav-show');
                body.classList.remove('g-sidenav-pinned');
            } else {
                body.classList.remove('g-sidenav-hidden');
                body.classList.add('g-sidenav-pinned');
            }
        }
        
        setInitialState();
        
        // Re-check on page visibility change (when switching tabs/apps)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setInitialState();
            }
        });
        
        // Force initial state on any page navigation
        window.addEventListener('pageshow', function() {
            setInitialState();
        });
    });
})();
