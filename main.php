<?php
session_start();

if (!isset($_SESSION['ususario'])) {
  header('Location: index.php');
  exit();
}

$nombreUsuario = trim(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellidoP'] ?? ''));
if ($nombreUsuario === '') {
  $nombreUsuario = $_SESSION['ususario'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Monitoreo de Unidades en Taller</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="shortcut icon" href="assets\acrivera_logo.png" type="image/x-icon">
</head>

<body>
  <div class="dashboard">
    <div class="session-header">
      <div class="session-user">
        <span class="session-label">Sesion activa</span>
        <strong><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></strong>
      </div>

      <a class="logout-button" href="logout.php">Cerrar sesion</a>
    </div>

    <div class="top-bar">
      <div class="title-box">
        <div class="icon">🔧🚗</div>
        <div class="title">
          <h1>MONITOREO DE UNIDADES EN TALLER</h1>
          <p>● Información en tiempo real</p>
        </div>
      </div>

      <div class="clock">
        <div class="time" id="time">10:24 AM</div>
        <div class="date" id="date">21/05/2024</div>
      </div>
    </div>

    <div class="bays">
      <div class="bay">
        <div class="bay-header blue"><span class="bay-title"><span class="bay-icon" aria-hidden="true"><svg viewBox="0 0 64 64" focusable="false"><path d="M18 42a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm28 0a5 5 0 1 0 0 10 5 5 0 0 0 0-10ZM14 39h36l-2.8-14a6 6 0 0 0-5.9-4.8H22.7a6 6 0 0 0-5.9 4.8L14 39Zm41.8-1.3-3.2-15.9A10 10 0 0 0 42.8 14H21.2a10 10 0 0 0-9.8 7.8L8.2 37.7A4 4 0 0 0 12.1 42H13a9 9 0 0 0 10 8 9 9 0 0 0 18 0h10a9 9 0 0 0 10-8h.9a4 4 0 0 0 3.9-4.3ZM21 18h22l3.2 16H17.8L21 18Z" /></svg></span>BAHÍA 1</span> <span class="badge">2 UNIDADES</span></div>
        <div class="card">
          <div class="os">OS-12458</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 08:15<br>👤 Cliente: Juan Pérez<br>📝 Motivo: Servicio de 10,000 km<br>🔧 Técnico: Carlos López</div>
        </div>
        <div class="card">
          <div class="os">OS-12459</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 09:40<br>👤 Cliente: María González<br>📝 Motivo: Diagnóstico de falla<br>🔧 Técnico: PREVIAS</div>
        </div>

        <div class="available">🚗 Disponible para próxima unidad</div>
      </div>

      <div class="bay">
        <div class="bay-header green"><span class="bay-title"><span class="bay-icon" aria-hidden="true"><svg viewBox="0 0 64 64" focusable="false"><path d="M18 42a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm28 0a5 5 0 1 0 0 10 5 5 0 0 0 0-10ZM14 39h36l-2.8-14a6 6 0 0 0-5.9-4.8H22.7a6 6 0 0 0-5.9 4.8L14 39Zm41.8-1.3-3.2-15.9A10 10 0 0 0 42.8 14H21.2a10 10 0 0 0-9.8 7.8L8.2 37.7A4 4 0 0 0 12.1 42H13a9 9 0 0 0 10 8 9 9 0 0 0 18 0h10a9 9 0 0 0 10-8h.9a4 4 0 0 0 3.9-4.3ZM21 18h22l3.2 16H17.8L21 18Z" /></svg></span>BAHÍA 2</span> <span class="badge">2 UNIDADES</span></div>
        <div class="card">
          <div class="os">OS-12460</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 08:30<br>👤 Cliente: Pedro Ramírez<br>📝 Motivo: Frenos - Revisión<br>🔧 Técnico: Ana Torres</div>
        </div>
        <div class="card">
          <div class="os">OS-12461</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 10:05<br>👤 Cliente: Laura Sánchez<br>📝 Motivo: Cambio de aceite<br>🔧 Técnico: Yair Hernández Serrano</div>
        </div>
        <div class="available">🚗 Actualmente en uso</div>
      </div>

      <div class="bay">
        <div class="bay-header orange"><span class="bay-title"><span class="bay-icon" aria-hidden="true"><svg viewBox="0 0 64 64" focusable="false"><path d="M18 42a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm28 0a5 5 0 1 0 0 10 5 5 0 0 0 0-10ZM14 39h36l-2.8-14a6 6 0 0 0-5.9-4.8H22.7a6 6 0 0 0-5.9 4.8L14 39Zm41.8-1.3-3.2-15.9A10 10 0 0 0 42.8 14H21.2a10 10 0 0 0-9.8 7.8L8.2 37.7A4 4 0 0 0 12.1 42H13a9 9 0 0 0 10 8 9 9 0 0 0 18 0h10a9 9 0 0 0 10-8h.9a4 4 0 0 0 3.9-4.3ZM21 18h22l3.2 16H17.8L21 18Z" /></svg></span>BAHÍA 3</span> <span class="badge">2 UNIDADES</span></div>
        <div class="card">
          <div class="os">OS-12462</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 09:00<br>👤 Cliente: Roberto Díaz<br>📝 Motivo: Suspensión - Revisión<br>🔧 Técnico: Miguel Vela</div>
        </div>
        <div class="card">
          <div class="os">OS-12463</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 10:15<br>👤 Cliente: Verónica López<br>📝 Motivo: Alineación y balanceo<br>🔧 Técnico: Alan Daniel Pluma Meléndez</div>
        </div>
        <div class="available">🚗 Actualmente en uso</div>
      </div>

      <div class="bay">
        <div class="bay-header purple"><span class="bay-title"><span class="bay-icon" aria-hidden="true"><svg viewBox="0 0 64 64" focusable="false"><path d="M18 42a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm28 0a5 5 0 1 0 0 10 5 5 0 0 0 0-10ZM14 39h36l-2.8-14a6 6 0 0 0-5.9-4.8H22.7a6 6 0 0 0-5.9 4.8L14 39Zm41.8-1.3-3.2-15.9A10 10 0 0 0 42.8 14H21.2a10 10 0 0 0-9.8 7.8L8.2 37.7A4 4 0 0 0 12.1 42H13a9 9 0 0 0 10 8 9 9 0 0 0 18 0h10a9 9 0 0 0 10-8h.9a4 4 0 0 0 3.9-4.3ZM21 18h22l3.2 16H17.8L21 18Z" /></svg></span>BAHÍA 4</span> <span class="badge">1 UNIDAD</span></div>
        <div class="card">
          <div class="os">OS-12464</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 09:20<br>👤 Cliente: Carlos Molina<br>📝 Motivo: Eléctrico - Revisión<br>🔧 Técnico: Diego Herrera</div>
        </div>
        <div class="available">🚗 Disponible para próxima unidad</div>
      </div>
    </div>

    <div class="footer">
      <div class="person">
        <div class="avatar">👤</div>
        <div>
          <h3>Jefe de Taller</h3>
          <p>Abraham López Pintor </p>
        </div>
      </div>

      <div class="person">
        <div class="avatar" style="background:#16a34a;">👤</div>
        <div>
          <h3 style="color:#6ee7a8;">Gerente</h3>
          <p>Rubén Vélez Pérez</p>
        </div>
      </div>

      <div class="person">
        <div class="avatar" style="background:#7c3aed;">👤</div>
        <div>
          <h3 style="color:#d8b4fe;">Dir. Post Venta</h3>
          <p>Manuel A. Urtiz Leon</p>
        </div>
      </div>
    </div>
  </div>
</body>
<footer>
  <script src="script.js?v=<?= urlencode((string) filemtime(__DIR__ . '/script.js')) ?>"></script>
</footer>
</html>
