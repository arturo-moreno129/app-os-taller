<?php
if (!defined('APP_INIT')) {
    http_response_code(403);
    exit('Acceso denegado.');
}
/** @var string $errorMessage */
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio de Sesion</title>
  <link rel="shortcut icon" href="assets/acrivera_logo.png" type="image/x-icon">
  <link rel="stylesheet" href="style.css" />
</head>

<body class="login-page">
  <main class="login-card">
    <div class="shield">🔐</div>
    <h1>Iniciar sesion</h1>
    <p class="intro">Ingresa tu usuario y tu contrasena para acceder de forma segura al sistema.</p>

    <?php if ($errorMessage !== ''): ?>
      <div class="error-message"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form class="login-form" id="loginForm" method="post" action="index.php?action=login">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
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
