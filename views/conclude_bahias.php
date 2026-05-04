<?php
if (!defined('APP_INIT')) {
    http_response_code(403);
    exit('Acceso denegado.');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Concluir Bahías</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="shortcut icon" href="assets/acrivera_logo.png" type="image/x-icon">
</head>

<body>
  <?php include __DIR__ . '/../header.php'; ?>

  <div class="dashboard">
    <div class="session-header">
      <div class="session-user">
        <span class="session-label">Módulo</span>
        <strong>Concluir bahías</strong>
      </div>
      <a class="logout-button" href="index.php?action=logout">Cerrar sesión</a>
    </div>

    <div class="top-bar">
      <div class="title-box">
        <button class="menu-toggle" id="menuToggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="sideMenu">
          <span></span>
          <span></span>
          <span></span>
        </button>
        <div class="icon">✅</div>
        <div class="title">
          <h1>Módulo de Conclusión</h1>
          <p>Completa los servicios sin mostrar botones en el dashboard.</p>
        </div>
      </div>
    </div>

    <?php if (!empty($successMessage)): ?>
      <div class="success-message"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
      <div class="error-message"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="conclude-module">
      <?php if (empty($bays)): ?>
        <div class="info-message">No hay bahías en curso para concluir en este momento.</div>
      <?php else: ?>
        <table class="conclude-table">
          <thead>
            <tr>
              <th>Bahía</th>
              <th>OS</th>
              <th>Cliente</th>
              <th>Método</th>
              <th>Técnico</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Estatus</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bays as $bay): ?>
              <tr>
                <td><?= htmlspecialchars($bay['nombre_bahia'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($bay['os'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($bay['cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($bay['motivo'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($bay['tecnico'] ?? 'Sin asignar', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($bay['fecha_ingreso'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($bay['hora_ingreso'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($bay['estatus'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <form method="post" action="index.php?action=concluir_bahia" class="conclude-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="nombre_bahia" value="<?= htmlspecialchars($bay['nombre_bahia'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="button button--small">Concluir</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>

  <?php include __DIR__ . '/../footer.php'; ?>
</body>

</html>
