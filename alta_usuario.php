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
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(145deg, #08111f, #10233f 55%, #0c1728);
      color: #f8fbff;
      padding: 24px;
    }

    .page {
      max-width: 1140px;
      margin: 0 auto;
    }

    .panel {
      display: grid;
      gap: 18px;
    }

    .panel-header {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 20px;
    }

    .panel-header h1 {
      font-size: 28px;
      letter-spacing: 0.04em;
    }

    .panel-header .user-label {
      color: #a5b4fc;
      font-size: 14px;
    }

    .form-card,
    .info-card {
      background: rgba(7, 16, 29, 0.94);
      border: 1px solid rgba(148, 197, 255, 0.18);
      border-radius: 24px;
      padding: 28px;
      box-shadow: 0 28px 70px rgba(0, 0, 0, 0.3);
    }

    .form-card h2,
    .info-card h2 {
      margin-bottom: 18px;
      font-size: 22px;
    }

    .message {
      margin-bottom: 18px;
      padding: 14px 17px;
      border-radius: 16px;
      font-size: 14px;
    }

    .message.success {
      background: rgba(34, 197, 94, 0.16);
      border: 1px solid rgba(34, 197, 94, 0.35);
      color: #d1fae5;
    }

    .message.error {
      background: rgba(248, 113, 113, 0.16);
      border: 1px solid rgba(248, 113, 113, 0.35);
      color: #fecaca;
    }

    .form-grid {
      display: grid;
      gap: 18px;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .field {
      display: grid;
      gap: 8px;
    }

    .field label {
      color: #cbd5e1;
      font-size: 14px;
      font-weight: 600;
    }

    .field input,
    .field select {
      width: 100%;
      border: 1px solid rgba(148, 197, 255, 0.18);
      border-radius: 14px;
      padding: 12px 14px;
      background: rgba(255, 255, 255, 0.06);
      color: #f8fbff;
      font-size: 15px;
      outline: none;
      transition: border-color .25s ease, box-shadow .25s ease;
    }

    .field input:focus,
    .field select:focus {
      border-color: rgba(56, 189, 248, 0.7);
      box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.16);
      background: rgba(255, 255, 255, 0.08);
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
  </style>
</head>

<body>
  <div class="page">
    <div class="panel-header">
      <div>
        <h1>Alta de nuevo usuario</h1>
        <p class="user-label">Usuario conectado: <?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></p>
      </div>
    </div>

    <?php if ($successMessage !== ''): ?>
      <div class="message success"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($errorMessage !== ''): ?>
      <div class="message error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="form-card">
      <h2>Datos del usuario</h2>
      <form method="post" action="alta_usuario.php">
        <div class="form-grid">
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
        </div>

        <div class="submit-row">
          <button type="submit" class="submit-button">Guardar usuario</button>
        </div>
      </form>
    </div>

    <div class="info-card">
      <h2>Notas</h2>
      <p>Los campos marcados con * son obligatorios. El usuario se crea con estatus activo por defecto.</p>
    </div>
  </div>
</body>

</html>
