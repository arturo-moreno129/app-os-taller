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

$con = include __DIR__ . '/conexion.php';
$dbReady = !defined('NO_DB_ACCESS') && $con;
$successMessage = '';
$errorMessage = '';

$formValues = [
  'usuario' => '',
  'contrasena' => '',
  'nombre' => '',
  'apellidoP' => '',
  'apellidoM' => '',
  'sexo' => 'Masculino',
  'puesto' => '',
  'departamento' => '',
  'rol' => 'Usuario'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($formValues as $field => $value) {
    $formValues[$field] = trim($_POST[$field] ?? '');
  }

  if (!$dbReady) {
    $errorMessage = 'No fue posible conectar con la base de datos.';
  } elseif ($formValues['usuario'] === '' || $formValues['contrasena'] === '' || $formValues['nombre'] === '' || $formValues['apellidoP'] === '' || $formValues['puesto'] === '' || $formValues['departamento'] === '' || $formValues['rol'] === '') {
    $errorMessage = 'Completa todos los campos obligatorios.';
  } else {
    $checkUser = mysqli_prepare($con, "SELECT id_usuario FROM usuarios WHERE usuario = ? LIMIT 1");

    if ($checkUser) {
      mysqli_stmt_bind_param($checkUser, 's', $formValues['usuario']);
      mysqli_stmt_execute($checkUser);
      mysqli_stmt_store_result($checkUser);
      $userExists = mysqli_stmt_num_rows($checkUser) > 0;
      mysqli_stmt_close($checkUser);

      if ($userExists) {
        $errorMessage = 'El nombre de usuario ya existe. Elige otro diferente.';
      } else {
        $hash = password_hash($formValues['contrasena'], PASSWORD_DEFAULT);

        $insertUser = mysqli_prepare($con, "INSERT INTO usuarios (usuario, contrasena, nombre, apellidoP, apellidoM, sexo, puesto, departamento, rol, estatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Activo')");

        if ($insertUser) {
          mysqli_stmt_bind_param(
            $insertUser,
            'sssssssss',
            $formValues['usuario'],
            $hash,
            $formValues['nombre'],
            $formValues['apellidoP'],
            $formValues['apellidoM'],
            $formValues['sexo'],
            $formValues['puesto'],
            $formValues['departamento'],
            $formValues['rol']
          );

          if (mysqli_stmt_execute($insertUser)) {
            $successMessage = 'Usuario creado correctamente.';
            $formValues = [
              'usuario' => '',
              'contrasena' => '',
              'nombre' => '',
              'apellidoP' => '',
              'apellidoM' => '',
              'sexo' => 'Masculino',
              'puesto' => '',
              'departamento' => '',
              'rol' => 'Usuario'
            ];
          } else {
            $errorMessage = 'No se pudo guardar el usuario en la base de datos.';
          }

          mysqli_stmt_close($insertUser);
        } else {
          $errorMessage = 'No se pudo preparar la inserción del usuario.';
        }
      }
    } else {
      $errorMessage = 'No se pudo verificar si el usuario existe.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Alta de Usuario</title>
  <link rel="shortcut icon" href="assets/acrivera_logo.png" type="image/x-icon">
  <link rel="stylesheet" href="style.css" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    body {
      min-height: 100vh;
      background:
        radial-gradient(circle at top, rgba(30, 111, 255, 0.16), transparent 32%),
        linear-gradient(180deg, #08111f, #0a1728 55%, #07111d);
      color: #f8fbff;
      padding: 20px;
    }

    .page {
      max-width: 1380px;
      margin: 0 auto;
      display: grid;
      gap: 18px;
    }

    .top-card,
    .panel,
    .list-card {
      background: rgba(8, 21, 35, 0.96);
      border: 1px solid #18314d;
      border-radius: 18px;
      box-shadow: 0 18px 40px rgba(0, 0, 0, 0.28);
    }

    .top-card {
      padding: 18px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
    }

    .top-card__user span,
    .section-eyebrow {
      display: block;
      color: #7ec4ff;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .08em;
      margin-bottom: 6px;
    }

    .top-card__user strong {
      font-size: 28px;
    }

    .actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .action-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 18px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 700;
      color: #fff;
      border: 1px solid transparent;
    }

    .action-link.secondary {
      background: #0f2137;
      border-color: #18314d;
    }

    .action-link.primary {
      background: linear-gradient(135deg, #2563eb, #0ea5e9);
    }

    .layout {
      display: grid;
      grid-template-columns: minmax(0, 1.35fr) minmax(320px, .95fr);
      gap: 18px;
      align-items: start;
    }

    .panel {
      padding: 24px;
    }

    .panel-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 14px;
      margin-bottom: 20px;
    }

    .panel-header h1,
    .list-card h2 {
      font-size: 30px;
      margin-bottom: 6px;
    }

    .panel-header p,
    .list-card p,
    .success-message,
    .error-message,
    .schema-note {
      color: #c8dbff;
      line-height: 1.55;
    }

    .clock-box {
      min-width: 170px;
      padding: 14px 18px;
      border-radius: 14px;
      background: #0d1d30;
      border: 1px solid #204164;
      text-align: right;
    }

    .clock-box .time {
      font-size: 30px;
      font-weight: 700;
    }

    .clock-box .date {
      font-size: 15px;
      color: #d8e8ff;
    }

    .success-message,
    .error-message,
    .schema-note {
      margin-bottom: 18px;
      padding: 14px 16px;
      border-radius: 14px;
    }

    .success-message {
      background: rgba(34, 197, 94, 0.12);
      border: 1px solid rgba(34, 197, 94, 0.28);
    }

    .error-message {
      background: rgba(239, 68, 68, 0.12);
      border: 1px solid rgba(239, 68, 68, 0.28);
      color: #ffd1d1;
    }

    .schema-note {
      background: rgba(59, 130, 246, 0.12);
      border: 1px solid rgba(59, 130, 246, 0.28);
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
    }

    .field {
      display: grid;
      gap: 8px;
    }

    .field.full {
      grid-column: 1 / -1;
    }

    .field label {
      font-size: 14px;
      font-weight: 700;
      color: #dbeafe;
    }

    .field input,
    .field select,
    .field textarea {
      width: 100%;
      border: 1px solid #24435f;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.05);
      color: #fff;
      padding: 14px 15px;
      font-size: 15px;
      outline: none;
    }

    .field select {
      appearance: none;
      background-color: #0f2137;
      color: #ffffff;
    }

    .field select option {
      background: #0f2137;
      color: #ffffff;
    }

    .field textarea {
      min-height: 110px;
      resize: vertical;
    }

    .field input:focus,
    .field select:focus,
    .field textarea:focus {
      border-color: #4aa3ff;
      box-shadow: 0 0 0 4px rgba(74, 163, 255, 0.12);
    }

    .helper-text {
      font-size: 13px;
      color: #9db8dd;
    }

    .form-actions {
      grid-column: 1 / -1;
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      margin-top: 8px;
    }

    .button {
      border: 0;
      border-radius: 14px;
      padding: 14px 20px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
    }

    .button.secondary {
      background: #0f2137;
      color: #fff;
      border: 1px solid #204164;
    }

    .button.primary {
      background: linear-gradient(135deg, #16a34a, #22c55e);
      color: #fff;
    }

    .submit-row {
      display: flex;
      justify-content: flex-end;
    }

    .submit-button {
      border: none;
      border-radius: 16px;
      padding: 14px 22px;
      background: linear-gradient(135deg, #2563eb, #0ea5e9);
      color: #ffffff;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: transform .2s ease, box-shadow .2s ease;
    }

    .submit-button:hover {
      transform: translateY(-1px);
      box-shadow: 0 18px 30px rgba(37, 99, 235, 0.25);
    }

    .info-card p {
      color: #cbd5e1;
      line-height: 1.7;
    }

    .list-card {
      padding: 24px;
    }

    .list-card p {
      margin-bottom: 18px;
    }

    @media (max-width: 1100px) {
      .layout {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 720px) {
      .top-card,
      .panel-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .form-grid {
        grid-template-columns: 1fr;
      }

      .form-actions {
        justify-content: stretch;
        flex-direction: column;
      }

      .button,
      .action-link,
      .clock-box {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <?php include __DIR__ . '/header.php'; ?>
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
        <a class="action-link secondary" href="main.php">Volver al tablero</a>
        <a class="action-link primary" href="logout.php">Cerrar sesion</a>
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

        <form method="post" action="alta_usuario.php" class="form-grid">
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
            <a class="action-link secondary" href="main.php">Cancelar</a>
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
  <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>
