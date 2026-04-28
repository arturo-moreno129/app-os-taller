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
  <link rel="shortcut icon" href="assets/acrivera_logo.png" type="image/x-icon">
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
          <p>● Informacion en tiempo real</p>
        </div>
      </div>

      <div class="clock">
        <div class="time" id="time">10:24:10 AM</div>
        <div class="date" id="date">21/05/2024</div>
      </div>
    </div>

    <div class="bays">
      <div class="bay bay-busy">
        <div class="bay-header blue">
          <span class="bay-title">
            <img class="bay-header-car" src="assets/car_icon_path3.png" alt="Icono de bahia">
            BAHIA 1
          </span>
          <span class="badge">2 UNIDADES</span>
        </div>
        <div class="bay-overview">
          <span class="status-indicator" aria-hidden="true"></span>
          <img class="bay-car-image" src="assets/icon_carro.png" alt="Unidad en bahia">
          <p class="bay-status-text">En operación</p>
        </div>
        <div class="card">
          <div class="os">OS-12459</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 09:40<br>👤 Cliente: Maria Gonzalez<br>📝 Motivo: Diagnostico de falla<br>🔧 Tecnico: PREVIAS</div>
        </div>
        <div class="available">🚗 Disponible para proxima unidad</div>
      </div>

      <div class="bay bay-busy">
        <div class="bay-header green">
          <span class="bay-title">
            <img class="bay-header-car" src="assets/car_icon_path3.png" alt="Icono de bahia">
            BAHIA 2
          </span>
          <span class="badge">2 UNIDADES</span>
        </div>
        <div class="bay-overview">
          <span class="status-indicator" aria-hidden="true"></span>
          <img class="bay-car-image" src="assets/icon_carro.png" alt="Unidad en bahia">
          <p class="bay-status-text">En operación</p>
        </div>
        <div class="card">
          <div class="os">OS-12461</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 10:05<br>👤 Cliente: Laura Sanchez<br>📝 Motivo: Cambio de aceite<br>🔧 Tecnico: Yair Hernandez Serrano</div>
        </div>
        <div class="available">🚗 Disponible para proxima unidad</div>
      </div>

      <div class="bay bay-busy">
        <div class="bay-header orange">
          <span class="bay-title">
            <img class="bay-header-car" src="assets/car_icon_path3.png" alt="Icono de bahia">
            BAHIA 3
          </span>
          <span class="badge">2 UNIDADES</span>
        </div>
        <div class="bay-overview">
          <span class="status-indicator" aria-hidden="true"></span>
          <img class="bay-car-image" src="assets/icon_carro.png" alt="Unidad en bahia">
          <p class="bay-status-text">En operación</p>
        </div>
        <div class="card">
          <div class="os">OS-12463</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 10:15<br>👤 Cliente: Veronica Lopez<br>📝 Motivo: Alineacion y balanceo<br>🔧 Tecnico: Alan Daniel Pluma Melendez</div>
        </div>
        <div class="available">🚗 Disponible para proxima unidad</div>
      </div>

      <div class="bay bay-free">
        <div class="bay-header purple">
          <span class="bay-title">
            <img class="bay-header-car" src="assets/car_icon_path3.png" alt="Icono de bahia">
            BAHIA 4
          </span>
          <span class="badge">1 UNIDAD</span>
        </div>
        <div class="bay-overview">
          <span class="status-indicator" aria-hidden="true"></span>
          <img class="bay-car-image" src="assets/icon_carro.png" alt="Unidad en bahia">
          <p class="bay-status-text">Disponible</p>
        </div>
        <div class="card">
          <div class="os">OS-12464</div>
          <div class="info">📅 21/05/2024 &nbsp;&nbsp; 🕒 09:20<br>👤 Cliente: Carlos Molina<br>📝 Motivo: Electrico - Revision<br>🔧 Tecnico: Diego Herrera</div>
        </div>
        <div class="available">🚗 Disponible para proxima unidad</div>
      </div>
    </div>

    <div class="footer">
      <div class="person">
        <div class="avatar">👤</div>
        <div>
          <h3>Jefe de Taller</h3>
          <p>Abraham Lopez Pintor</p>
        </div>
      </div>

      <div class="person">
        <div class="avatar" style="background:#16a34a;">👤</div>
        <div>
          <h3 style="color:#6ee7a8;">Gerente</h3>
          <p>Ruben Velez Perez</p>
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
