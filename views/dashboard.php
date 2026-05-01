<?php
if (!defined('APP_INIT')) {
    http_response_code(403);
    exit('Acceso denegado.');
}
/** @var string $nombreUsuario */
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
  <?php include __DIR__ . '/../header.php'; ?>

  <div class="dashboard">
    <div class="session-header">
      <div class="session-user">
        <span class="session-label">Sesion activa</span>
        <strong><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></strong>
      </div>

      <a class="logout-button" href="index.php?action=logout">Cerrar sesion</a>
    </div>

    <div class="top-bar">
      <div class="title-box">
        <button class="menu-toggle" id="menuToggle" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="sideMenu">
          <span></span>
          <span></span>
          <span></span>
        </button>
        <div class="icon">🔧🚗</div>
        <div class="title">
          <h1>MONITOREO DE UNIDADES EN TALLER</h1>
          <p>● Información en tiempo real</p>
        </div>
      </div>

      <div class="clock">
        <div class="time" id="time">10:24:10 AM</div>
        <div class="date" id="date">21/05/2024</div>
      </div>
    </div>

    <?php
      $bayColors = [
          'BAHIA 1' => 'blue',
          'BAHIA 2' => 'green',
          'BAHIA 3' => 'orange',
          'BAHIA 4' => 'purple',
      ];
    ?>

    <div class="bays" id="dashboardBays">
      <?php include __DIR__ . '/dashboard-bays.php'; ?>
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
        <div class="avatar avatar--green">👤</div>
        <div>
          <h3 class="person-title person-title--green">Gerente</h3>
          <p>Ruben Velez Perez</p>
        </div>
      </div>

      <div class="person">
        <div class="avatar avatar--purple">👤</div>
        <div>
          <h3 class="person-title person-title--purple">Dir. Post Venta</h3>
          <p>Manuel A. Urtiz Leon</p>
        </div>
      </div>
    </div>
  </div>
  <?php include __DIR__ . '/../footer.php'; ?>
</body>

</html>
