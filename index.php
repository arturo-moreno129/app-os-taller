<?php
session_start();

if (isset($_SESSION['ususario'])) {
  header('Location: main.php');
  exit();
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = trim($_POST['usuario'] ?? '');
  $pass = $_POST['password'] ?? '';

  if ($user === '' || $pass === '') {
    $errorMessage = 'Ingresa tu usuario y tu contrasena.';
  } else {
    $con = include __DIR__ . '/conexion.php';

    if (defined('NO_DB_ACCESS') || !$con) {
      $errorMessage = 'No fue posible conectar con la base de datos.';
    } else {
      $query = "SELECT * FROM usuarios WHERE usuario = ? AND estatus = 'Activo' LIMIT 1";
      $stmt = mysqli_prepare($con, $query);

      if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if ($row && password_verify($pass, $row['contrasena'])) {
          $_SESSION['id_usuario'] = $row['id_usuario'] ?? null;
          $_SESSION['ususario'] = $row['usuario'] ?? '';
          $_SESSION['nombre'] = $row['nombre'] ?? '';
          $_SESSION['apellidoP'] = $row['apellidoP'] ?? '';
          $_SESSION['apellidoM'] = $row['apellidoM'] ?? '';
          $_SESSION['sexo'] = $row['sexo'] ?? '';
          $_SESSION['puesto'] = $row['puesto'] ?? '';
          $_SESSION['departamento'] = $row['departamento'] ?? '';
          $_SESSION['rol'] = $row['rol'] ?? '';

          header('Location: main.php');
          exit();
        }

        $errorMessage = 'Usuario o contrasena incorrectos.';
      } else {
        $errorMessage = 'No se pudo validar el inicio de sesion.';
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio de Sesion</title>
  <link rel="shortcut icon" href="assets\acrivera_logo.png" type="image/x-icon">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      min-height: 100vh;
      display: grid;
      place-items: center;
      overflow: hidden;
      background:
        radial-gradient(circle at top, rgba(56, 189, 248, 0.20), transparent 35%),
        linear-gradient(145deg, #08111f, #10233f 55%, #0c1728);
      color: #f8fbff;
      padding: 24px;
    }

    .login-card {
      width: min(92vw, 430px);
      padding: 38px 30px;
      border-radius: 28px;
      background: rgba(7, 16, 29, 0.86);
      border: 1px solid rgba(148, 197, 255, 0.18);
      box-shadow: 0 28px 70px rgba(0, 0, 0, 0.42);
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .login-card::before {
      content: "";
      position: absolute;
      inset: -120px auto auto -80px;
      width: 220px;
      height: 220px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.22), transparent 70%);
      pointer-events: none;
    }

    .shield {
      width: 82px;
      height: 82px;
      margin: 0 auto 22px;
      border-radius: 24px;
      display: grid;
      place-items: center;
      font-size: 38px;
      background: linear-gradient(145deg, #2563eb, #0ea5e9);
      box-shadow: 0 16px 35px rgba(37, 99, 235, 0.32);
      animation: floatIcon 2.2s ease-in-out infinite;
    }

    h1 {
      font-size: 30px;
      margin-bottom: 10px;
      letter-spacing: 0.03em;
    }

    .intro {
      color: #c9dcff;
      font-size: 15px;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .error-message {
      margin-bottom: 18px;
      padding: 12px 14px;
      border-radius: 14px;
      background: rgba(239, 68, 68, 0.14);
      border: 1px solid rgba(248, 113, 113, 0.35);
      color: #fecaca;
      font-size: 14px;
      text-align: left;
    }

    .login-form {
      display: grid;
      gap: 16px;
      text-align: left;
      margin-bottom: 24px;
    }

    .field {
      display: grid;
      gap: 8px;
    }

    .field label {
      color: #dbeafe;
      font-size: 14px;
      font-weight: 600;
    }

    .field input {
      width: 100%;
      border: 1px solid rgba(148, 197, 255, 0.18);
      border-radius: 14px;
      padding: 14px 16px;
      background: rgba(255, 255, 255, 0.06);
      color: #f8fbff;
      font-size: 15px;
      outline: none;
      transition: border-color .25s ease, box-shadow .25s ease, background .25s ease;
    }

    .field input::placeholder {
      color: #98acd0;
    }

    .field input:focus {
      border-color: rgba(56, 189, 248, 0.7);
      box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.16);
      background: rgba(255, 255, 255, 0.08);
    }

    .login-button {
      border: none;
      border-radius: 16px;
      padding: 15px 18px;
      background: linear-gradient(135deg, #2563eb, #0ea5e9);
      color: #ffffff;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
      box-shadow: 0 16px 28px rgba(37, 99, 235, 0.25);
    }

    .login-button:hover {
      transform: translateY(-1px);
      box-shadow: 0 20px 34px rgba(37, 99, 235, 0.3);
    }

    .login-button:active {
      transform: translateY(0);
    }

    .login-button:disabled {
      cursor: wait;
      opacity: 0.8;
    }

    .loading-panel {
      display: none;
      margin-top: 6px;
    }

    .loading-panel.is-visible {
      display: block;
    }

    .spinner {
      width: 84px;
      height: 84px;
      margin: 0 auto 22px;
      border-radius: 50%;
      border: 6px solid rgba(255, 255, 255, 0.14);
      border-top-color: #38bdf8;
      border-right-color: #60a5fa;
      animation: spin 1s linear infinite;
    }

    .progress-track {
      width: 100%;
      height: 10px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      overflow: hidden;
      margin-bottom: 16px;
    }

    .progress-bar {
      width: 0;
      height: 100%;
      border-radius: inherit;
      background: linear-gradient(90deg, #38bdf8, #60a5fa, #22c55e);
      box-shadow: 0 0 20px rgba(56, 189, 248, 0.38);
      animation: progressLoad 3.2s ease-in-out infinite;
    }

    .status {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      color: #dbeafe;
      font-size: 14px;
    }

    .status-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.45);
      animation: pulseDot 1.5s infinite;
    }

    .steps {
      margin-top: 22px;
      display: grid;
      gap: 10px;
      text-align: left;
    }

    .step {
      padding: 12px 14px;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.05);
      color: #dce9ff;
      font-size: 14px;
      border: 1px solid rgba(255, 255, 255, 0.06);
      opacity: 0.45;
      transform: translateX(-10px);
      transition: opacity .35s ease, transform .35s ease, border-color .35s ease;
    }

    .step.active {
      opacity: 1;
      transform: translateX(0);
      border-color: rgba(96, 165, 250, 0.45);
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    @keyframes progressLoad {
      0% {
        width: 8%;
      }

      35% {
        width: 48%;
      }

      70% {
        width: 82%;
      }

      100% {
        width: 100%;
      }
    }

    @keyframes pulseDot {
      0% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.45);
      }

      70% {
        box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
      }
    }

    @keyframes floatIcon {
      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-6px);
      }
    }

    @media (max-width: 520px) {
      .login-card {
        padding: 30px 22px;
      }

      h1 {
        font-size: 25px;
      }
    }
  </style>
</head>

<body>
  <main class="login-card">
    <div class="shield">🔐</div>
    <h1>Iniciar sesion</h1>
    <p class="intro">Ingresa tu usuario y tu contrasena para acceder de forma segura al sistema.</p>

    <?php if ($errorMessage !== ''): ?>
      <div class="error-message"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form class="login-form" id="loginForm" method="post" action="">
      <div class="field">
        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" placeholder="Escribe tu usuario" value="<?= htmlspecialchars($_POST['usuario'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="field">
        <label for="password">Contrasena</label>
        <input type="password" id="password" name="password" placeholder="Escribe tu contrasena" required>
      </div>

      <button class="login-button" type="submit" id="loginButton">Entrar</button>
    </form>

    <section class="loading-panel" id="loadingPanel" aria-label="Proceso de inicio de sesion">
      <div class="spinner" aria-hidden="true"></div>

      <div class="progress-track" aria-hidden="true">
        <div class="progress-bar"></div>
      </div>

      <div class="status">
        <span class="status-dot"></span>
        <span id="statusText">Conectando con el servidor...</span>
      </div>

      <section class="steps" aria-label="Pasos de inicio de sesion">
        <div class="step active">1. Validando usuario</div>
        <div class="step">2. Confirmando permisos</div>
        <div class="step">3. Cargando entorno</div>
      </section>
    </section>
  </main>

  <script>
    const messages = [
      "Conectando con el servidor...",
      "Validando credenciales...",
      "Cargando configuracion del usuario...",
      "Acceso listo."
    ];

    const loginForm = document.getElementById("loginForm");
    const loadingPanel = document.getElementById("loadingPanel");
    const loginButton = document.getElementById("loginButton");
    const statusText = document.getElementById("statusText");
    const steps = document.querySelectorAll(".step");
    let currentStep = 0;
    let intervalId = null;

    const updateLoadingState = () => {
      if (currentStep < messages.length) {
        statusText.textContent = messages[currentStep];
      }

      steps.forEach((step, index) => {
        step.classList.toggle("active", index <= currentStep && index < steps.length);
      });

      currentStep = (currentStep + 1) % messages.length;
    };

    loginForm.addEventListener("submit", (event) => {
      event.preventDefault();

      loginButton.disabled = true;
      loginButton.textContent = "Iniciando...";
      loadingPanel.classList.add("is-visible");

      currentStep = 0;
      updateLoadingState();

      if (intervalId) {
        clearInterval(intervalId);
      }

      intervalId = setInterval(updateLoadingState, 5000);

      setTimeout(() => {
        loginForm.submit();
      }, 5000);
    });
  </script>
</body>

</html>
