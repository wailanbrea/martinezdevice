// ============================================
// SISTEMA DE TUTORIAL GUIADO
// ============================================

// Configuración de pasos del tutorial por ruta
const tutorialSteps = {
    '/dashboard': [
        {
            element: '.stats-grid',
            intro: 'Aquí puedes ver un resumen de estadísticas importantes. Haz clic en cualquier tarjeta para ver más detalles.',
            position: 'bottom'
        },
        {
            element: '.chart-container',
            intro: 'Este gráfico muestra la distribución de equipos por estado de reparación.',
            position: 'top'
        },
        {
            element: '.table-container',
            intro: 'Tabla de últimas entradas de equipos al sistema.',
            position: 'top'
        }
    ],
    '/clientes': [
        {
            element: '.page-actions',
            intro: 'Usa este botón para agregar un nuevo cliente al sistema.',
            position: 'bottom'
        },
        {
            element: '.table-container',
            intro: 'Aquí se muestra la lista de todos los clientes. Puedes ver, editar o eliminar clientes usando los botones de acción.',
            position: 'top'
        }
    ],
    '/equipos': [
        {
            element: '.page-actions',
            intro: 'Haz clic aquí para registrar una nueva entrada de equipo.',
            position: 'bottom'
        },
        {
            element: '#filtersContainer, .card-header',
            intro: 'Usa los filtros de búsqueda para encontrar equipos específicos por estado, cliente, tipo o fecha.',
            position: 'bottom'
        },
        {
            element: '.table-container',
            intro: 'Lista de todas las entradas registradas. Puedes ver detalles, editar o imprimir el código QR.',
            position: 'top'
        }
    ],
    '/reparaciones': [
        {
            element: '.page-actions',
            intro: 'Crea una nueva reparación para registrar el diagnóstico y las piezas reemplazadas.',
            position: 'bottom'
        },
        {
            element: '#filtersContainer, .card-header',
            intro: 'Filtra el historial de reparaciones por estado, técnico, cliente, fecha o costo.',
            position: 'bottom'
        },
        {
            element: '.table-container',
            intro: 'Historial completo de todas las reparaciones. Aquí puedes ver el estado, técnico asignado y costo total.',
            position: 'top'
        }
    ],
    '/facturas': [
        {
            element: '.page-actions',
            intro: 'Crea una nueva factura para un servicio completado.',
            position: 'bottom'
        },
        {
            element: '.table-container',
            intro: 'Lista de todas las facturas. Puedes ver detalles, editar o imprimir cada factura.',
            position: 'top'
        }
    ],
    '/contabilidad': [
        {
            element: '.stats-grid',
            intro: 'Resumen financiero con ingresos del mes, ingresos de hoy, facturas pendientes y facturas del mes.',
            position: 'bottom'
        },
        {
            element: '.chart-container',
            intro: 'Gráficos que muestran la distribución de ingresos por período y método de pago.',
            position: 'top'
        }
    ],
    '/usuarios': [
        {
            element: '.page-actions',
            intro: 'Agrega nuevos usuarios al sistema y asígnales roles y permisos.',
            position: 'bottom'
        },
        {
            element: '.table-container',
            intro: 'Lista de todos los usuarios. Aquí puedes gestionar sus roles y permisos.',
            position: 'top'
        }
    ]
};

// Obtener la ruta actual
function getCurrentRoute() {
    const path = window.location.pathname;
    // Remover el ID si está en la URL (ej: /equipos/1/edit -> /equipos)
    const routeWithoutId = path.split('/').slice(0, 2).join('/');
    return routeWithoutId || '/dashboard';
}

// Verificar si el tutorial ya se mostró para esta ruta
function hasSeenTutorial(route) {
    const tutorialKey = `tutorial_${route.replace(/\//g, '_')}`;
    return localStorage.getItem(tutorialKey) === 'true';
}

// Marcar que el tutorial se ha visto para esta ruta
function markTutorialAsSeen(route) {
    const tutorialKey = `tutorial_${route.replace(/\//g, '_')}`;
    localStorage.setItem(tutorialKey, 'true');
}

// Obtener los pasos del tutorial para la ruta actual
function getTutorialSteps(route) {
    const steps = tutorialSteps[route] || [];
    
    // Si hay pasos específicos, agregar pasos del sidebar también
    if (steps.length > 0) {
        return [
            {
                element: '.sidebar-nav',
                intro: 'Menú principal de navegación. Aquí puedes acceder a todas las secciones del sistema.',
                position: 'right'
            },
            ...steps,
            {
                element: '#tutorialBtn',
                intro: '¿Necesitas ayuda? Haz clic en este botón en cualquier momento para volver a ver el tutorial.',
                position: 'bottom'
            }
        ];
    }
    
    // Si no hay pasos específicos, solo mostrar el tutorial del sidebar
    return [
        {
            element: '.sidebar-nav',
            intro: 'Menú principal de navegación. Aquí puedes acceder a todas las secciones del sistema.',
            position: 'right'
        },
        {
            element: '#tutorialBtn',
            intro: '¿Necesitas ayuda? Haz clic en este botón en cualquier momento para volver a ver el tutorial.',
            position: 'bottom'
        }
    ];
}

// Iniciar el tutorial
function startTutorial(force = false) {
    // Verificar que no estamos en la página de login
    const currentPath = window.location.pathname;
    if (currentPath === '/login' || currentPath.startsWith('/login')) {
        return; // No ejecutar tutorial en la página de login
    }
    
    // Verificar que introJs está disponible
    if (typeof introJs === 'undefined') {
        console.warn('Intro.js no está cargado');
        return;
    }
    
    const route = getCurrentRoute();
    
    // Si no es forzado y ya se vio el tutorial, no iniciar
    if (!force && hasSeenTutorial(route)) {
        return;
    }
    
    const steps = getTutorialSteps(route);
    
    if (steps.length === 0) {
        return;
    }
    
    // Verificar que los elementos de los pasos existen antes de iniciar
    const validSteps = steps.filter(step => {
        if (!step.element) return false;
        const element = document.querySelector(step.element);
        return element !== null;
    });
    
    if (validSteps.length === 0) {
        return; // No iniciar si no hay elementos válidos
    }
    
    // Configurar Intro.js
    try {
        introJs().setOptions({
            steps: validSteps,
            showProgress: true,
            showBullets: true,
            exitOnOverlayClick: false,
            exitOnEsc: true,
            prevLabel: 'Anterior',
            nextLabel: 'Siguiente',
            skipLabel: 'Omitir',
            doneLabel: 'Finalizar',
            tooltipClass: 'customTooltip',
            highlightClass: 'customHighlight',
            buttonClass: 'introjs-button'
        }).oncomplete(function() {
            // Marcar como visto cuando se completa
            markTutorialAsSeen(route);
        }).onexit(function() {
            // Marcar como visto cuando se sale
            markTutorialAsSeen(route);
        }).start();
    } catch (error) {
        console.error('Error al iniciar el tutorial:', error);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que no estamos en la página de login
    const currentPath = window.location.pathname;
    if (currentPath === '/login' || currentPath.startsWith('/login')) {
        return; // No ejecutar tutorial en la página de login
    }
    
    // Verificar que los elementos necesarios existen antes de iniciar
    const sidebar = document.querySelector('.sidebar-nav');
    const tutorialBtn = document.getElementById('tutorialBtn');
    
    // Si no hay sidebar, probablemente no estamos en una página con layout completo
    if (!sidebar) {
        return; // No ejecutar si no hay sidebar
    }
    
    // Esperar un poco para que los elementos se rendericen
    setTimeout(function() {
        // Verificar nuevamente que los elementos existen
        if (!document.querySelector('.sidebar-nav')) {
            return; // Salir si aún no existen los elementos
        }
        
        // Iniciar tutorial automáticamente si es la primera vez
        startTutorial(false);
        
        // Botón para reactivar el tutorial
        if (tutorialBtn) {
            tutorialBtn.addEventListener('click', function(e) {
                e.preventDefault();
                startTutorial(true); // Forzar inicio
            });
        }
    }, 500);
});

// Reiniciar tutorial para todas las rutas (útil para testing)
function resetAllTutorials() {
    Object.keys(tutorialSteps).forEach(route => {
        const tutorialKey = `tutorial_${route.replace(/\//g, '_')}`;
        localStorage.removeItem(tutorialKey);
    });
    // También limpiar el tutorial general
    localStorage.removeItem('tutorial_sidebar');
    console.log('Tutoriales reiniciados. Recarga la página para ver el tutorial.');
}

// Exponer función globalmente para debugging
window.resetTutorials = resetAllTutorials;

