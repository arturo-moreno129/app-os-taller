const timeElement = document.getElementById('time');
const dateElement = document.getElementById('date');

function updateClock() {
    if (!timeElement || !dateElement) {
        return;
    }

    const now = new Date();
    timeElement.textContent = now.toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    dateElement.textContent = now.toLocaleDateString('es-MX');
}

if (timeElement && dateElement) {
    updateClock();
    setInterval(updateClock, 1000);
}

const dashboardBays = document.getElementById('dashboardBays');
const refreshStatus = document.getElementById('dashboardRefreshTime');

async function refreshDashboardData() {
    if (!dashboardBays) {
        return;
    }

    try {
        const response = await fetch('index.php?action=dashboard_data', {
            cache: 'no-store',
        });

        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }

        const html = await response.text();
        dashboardBays.innerHTML = html;

        if (refreshStatus) {
            refreshStatus.textContent = 'Última actualización: ' + new Date().toLocaleTimeString('es-MX', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
        }
    } catch (error) {
        console.warn('Error actualizando dashboard:', error);
    }
}

if (dashboardBays) {
    refreshDashboardData();
    setInterval(refreshDashboardData, 10000);
}

document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menuToggle') || document.querySelector('.menu-toggle');
    const sideMenu = document.getElementById('sideMenu');
    const closeMenuButton = document.getElementById('closeMenuButton');
    const menuOverlay = document.getElementById('menuOverlay');

    if (!menuToggle || !sideMenu || !closeMenuButton || !menuOverlay) {
        console.warn('Menu toggle script: elementos del menú faltantes.');
        return;
    }

    const openMenu = () => {
        sideMenu.classList.add('is-open');
        menuOverlay.classList.add('is-visible');
        document.body.classList.add('menu-open');
        sideMenu.setAttribute('aria-hidden', 'false');
        menuToggle.setAttribute('aria-expanded', 'true');
    };

    const closeMenu = () => {
        sideMenu.classList.remove('is-open');
        menuOverlay.classList.remove('is-visible');
        document.body.classList.remove('menu-open');
        sideMenu.setAttribute('aria-hidden', 'true');
        menuToggle.setAttribute('aria-expanded', 'false');
    };

    menuToggle.addEventListener('click', openMenu);
    closeMenuButton.addEventListener('click', closeMenu);
    menuOverlay.addEventListener('click', closeMenu);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });
});
