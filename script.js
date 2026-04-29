function updateClock() {
    const now = new Date();
    document.getElementById('time').textContent = now.toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    document.getElementById('date').textContent = now.toLocaleDateString('es-MX');
}

updateClock();
setInterval(updateClock, 1000);

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
