// ============================================
// DARK MODE FUNCTIONALITY
// ============================================

// Función para aplicar el modo oscuro
function applyDarkMode(isDark) {
    const html = document.documentElement;
    const body = document.body;
    const darkModeIcon = document.getElementById('darkModeIcon');
    
    if (isDark) {
        html.setAttribute('data-theme', 'dark');
        body.classList.add('dark-mode');
        if (darkModeIcon) {
            darkModeIcon.className = 'bi bi-sun-fill';
        }
        localStorage.setItem('darkMode', 'enabled');
    } else {
        html.removeAttribute('data-theme');
        body.classList.remove('dark-mode');
        if (darkModeIcon) {
            darkModeIcon.className = 'bi bi-moon-fill';
        }
        localStorage.setItem('darkMode', 'disabled');
    }
}

// Función para toggle del modo oscuro
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark' || document.body.classList.contains('dark-mode');
    applyDarkMode(!isDark);
}

// Cargar preferencia guardada al iniciar
function loadDarkModePreference() {
    const savedMode = localStorage.getItem('darkMode');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Si hay preferencia guardada, usarla; si no, usar preferencia del sistema
    if (savedMode === 'enabled' || (!savedMode && prefersDark)) {
        applyDarkMode(true);
    } else {
        applyDarkMode(false);
    }
}

// Escuchar cambios en la preferencia del sistema
if (window.matchMedia) {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', (e) => {
        // Solo aplicar si no hay preferencia guardada
        if (!localStorage.getItem('darkMode')) {
            applyDarkMode(e.matches);
        }
    });
}

// Sidebar Toggle - Versión responsive con overlay
document.addEventListener('DOMContentLoaded', function() {
    
    // Cargar modo oscuro al iniciar
    loadDarkModePreference();
    
    // Dark Mode Toggle
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleDarkMode();
        });
    }
    
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        if (sidebar) {
            const isActive = sidebar.classList.contains('active');
            sidebar.classList.toggle('active');
            
            // Mostrar/ocultar overlay solo en móvil/tablet
            if (sidebarOverlay && window.innerWidth <= 1024) {
                if (!isActive) {
                    sidebarOverlay.classList.add('active');
                } else {
                    sidebarOverlay.classList.remove('active');
                }
            }
        }
    }

    // Cerrar sidebar al hacer clic en overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            if (sidebar && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        });
    }

    // Cerrar sidebar al hacer clic en enlaces de navegación (móvil)
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 1024 && sidebar && sidebar.classList.contains('active')) {
                setTimeout(() => {
                    sidebar.classList.remove('active');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.remove('active');
                    }
                }, 100);
            }
        });
    });

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Modal functionality
    const modals = document.querySelectorAll('.modal');
    const openModalButtons = document.querySelectorAll('[data-modal]');
    const closeModalButtons = document.querySelectorAll('.modal-close');

    openModalButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
            }
        });
    });

    closeModalButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('active');
            }
        });
    });

    // Close modal on outside click
    modals.forEach(modal => {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.remove('active');
            }
        });
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            modals.forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
});

// Utility function to open modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

// Utility function to close modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}
