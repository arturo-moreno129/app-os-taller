<?php /** @var string $successMessage */ ?>
<?php /** @var string $errorMessage */ ?>
<?php /** @var array $formValues */ ?>
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
  <title>Alta de Usuario</title>
  <link rel="shortcut icon" href="assets/acrivera_logo.png" type="image/x-icon">
  <link rel="stylesheet" href="style.css" />
</head>

<body class="module-page">
  <?php include __DIR__ . '/../header.php'; ?>
  <div class="page">
    <div class="top-card">
      <button class="menu-toggle" id="menuToggle" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="sideMenu">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <div class="top-card__user">
        <span>Sesion activa</span>
        <strong><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></strong>
      </div>

      <div class="actions">
        <a class="action-link secondary" href="index.php?action=dashboard">Volver al tablero</a>
        <a class="action-link primary" href="index.php?action=logout">Cerrar sesion</a>
      </div>
    </div>

    <div class="layout">
      <section class="panel">
        <div class="panel-header">
          <div>
            <span class="section-eyebrow">Registro</span>
            <h1>Alta de usuario</h1>
            <p>Captura los datos para crear un nuevo usuario en el sistema.</p>
          </div>

          <div class="clock-box">
            <div id="time" class="time"></div>
            <div id="date" class="date"></div>
          </div>
        </div>

        <?php if ($successMessage !== ''): ?>
          <div class="success-message"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($errorMessage !== ''): ?>
          <div class="error-message"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" action="index.php?action=alta_usuario" class="form-grid">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <div class="field">
            <label for="usuario">Usuario *</label>
            <input type="text" id="usuario" name="usuario" placeholder="Nombre de usuario" value="<?= htmlspecialchars($formValues['usuario'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field">
            <label for="contrasena">Contraseña *</label>
            <input type="password" id="contrasena" name="contrasena" placeholder="Contraseña segura" required>
          </div>

          <div class="field">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre(s)" value="<?= htmlspecialchars($formValues['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field">
            <label for="apellidoP">Apellido paterno *</label>
            <input type="text" id="apellidoP" name="apellidoP" placeholder="Apellido paterno" value="<?= htmlspecialchars($formValues['apellidoP'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field">
            <label for="apellidoM">Apellido materno</label>
            <input type="text" id="apellidoM" name="apellidoM" placeholder="Apellido materno" value="<?= htmlspecialchars($formValues['apellidoM'], ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <div class="field">
            <label for="sexo">Sexo</label>
            <select id="sexo" name="sexo">
              <?php foreach (["Masculino", "Femenino", "Otro"] as $option): ?>
                <option value="<?= $option ?>" <?= $formValues['sexo'] === $option ? 'selected' : '' ?>><?= $option ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="puesto">Puesto *</label>
            <input type="text" id="puesto" name="puesto" placeholder="Puesto del usuario" value="<?= htmlspecialchars($formValues['puesto'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field">
            <label for="departamento">Departamento *</label>
            <input type="text" id="departamento" name="departamento" placeholder="Departamento" value="<?= htmlspecialchars($formValues['departamento'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field">
            <label for="rol">Rol *</label>
            <select id="rol" name="rol" required>
              <?php foreach (["Usuario", "Administrador", "Tecnico", "Supervisor"] as $option): ?>
                <option value="<?= $option ?>" <?= $formValues['rol'] === $option ? 'selected' : '' ?>><?= $option ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-actions">
            <a class="action-link secondary" href="index.php?action=dashboard">Cancelar</a>
            <button class="button primary" type="submit">Guardar usuario</button>
          </div>
        </form>
      </section>

      <aside class="list-card">
        <span class="section-eyebrow">Notas</span>
        <h2>Detalle</h2>
        <p>Los campos marcados con * son obligatorios. El usuario se crea con estatus activo por defecto.</p>
        <div class="schema-note">
          Esta pantalla usa un diseño equivalente al alta de bahias: formulario a la izquierda y tarjeta informativa a la derecha.
        </div>
      </aside>
    </div>
  </div>
  <?php include __DIR__ . '/../footer.php'; ?>
</body>

</html>
