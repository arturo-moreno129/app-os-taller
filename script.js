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
