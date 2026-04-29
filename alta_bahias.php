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
$idUsuarioSesion = isset($_SESSION['id_usuario']) ? (int) $_SESSION['id_usuario'] : null;

$con = include __DIR__ . '/conexion.php';
$dbReady = !defined('NO_DB_ACCESS') && $con;
$successMessage = '';
$errorMessage = '';
$tecnicos = [];
$ultimosRegistros = [];
$bahiasDisponibles = ['BAHIA 1', 'BAHIA 2', 'BAHIA 3', 'BAHIA 4'];

$formValues = [
  'nombre_bahia' => '',
  'os' => '',
  'estatus' => 'En operación',
  'fecha' => '',
  'hora' => '',
  'cliente' => '',
  'motivo' => '',
  'id_tecnico' => ''
];

if ($dbReady) {
  $tecnicosQuery = "SELECT id_tecnico, nombre, estatus, correo_corporativo FROM tecnicos ORDER BY nombre ASC";
  $tecnicosResult = mysqli_query($con, $tecnicosQuery);

  if ($tecnicosResult) {
    while ($row = mysqli_fetch_assoc($tecnicosResult)) {
      $tecnicos[] = $row;
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($formValues as $field => $value) {
    $formValues[$field] = trim($_POST[$field] ?? '');
  }

  if (!$dbReady) {
    $errorMessage = 'No fue posible conectar con la base de datos.';
  } elseif ($formValues['nombre_bahia'] === '' || $formValues['os'] === '' || $formValues['fecha'] === '' || $formValues['hora'] === '' || $formValues['cliente'] === '' || $formValues['id_tecnico'] === '') {
    $errorMessage = 'Completa todos los campos obligatorios.';
  } else {
    mysqli_begin_transaction($con);

    try {
      $insertBahia = mysqli_prepare($con, "INSERT INTO bahias (nombre_bahia, os, fecha_ingreso, hora_ingreso, cliente, motivo, estatus, creado_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

      if (!$insertBahia) {
        throw new Exception('No se pudo preparar el registro de bahia.');
      }

      mysqli_stmt_bind_param(
        $insertBahia,
        'sssssssi',
        $formValues['nombre_bahia'],
        $formValues['os'],
        $formValues['fecha'],
        $formValues['hora'],
        $formValues['cliente'],
        $formValues['motivo'],
        $formValues['estatus'],
        $idUsuarioSesion
      );

      if (!mysqli_stmt_execute($insertBahia)) {
        throw new Exception('No se pudo guardar la bahia.');
      }

      $idBahia = mysqli_insert_id($con);
      mysqli_stmt_close($insertBahia);

      $idTecnico = (int) $formValues['id_tecnico'];

      $insertAsignacion = mysqli_prepare($con, "INSERT INTO bahia_tecnico (id_bahia, id_tecnico, activo, asignado_por) VALUES (?, ?, 1, ?)");

      if (!$insertAsignacion) {
        throw new Exception('No se pudo preparar la asignacion del tecnico.');
      }

      mysqli_stmt_bind_param($insertAsignacion, 'iii', $idBahia, $idTecnico, $idUsuarioSesion);

      if (!mysqli_stmt_execute($insertAsignacion)) {
        throw new Exception('No se pudo asignar el tecnico.');
      }

      mysqli_stmt_close($insertAsignacion);

      $nuevoEstatusTecnico = $formValues['estatus'] === 'En mantenimiento' ? 'Libre' : 'En operación';
      $updateTecnico = mysqli_prepare($con, "UPDATE tecnicos SET estatus = ? WHERE id_tecnico = ?");

      if (!$updateTecnico) {
        throw new Exception('No se pudo actualizar el estatus del tecnico.');
      }

      mysqli_stmt_bind_param($updateTecnico, 'si', $nuevoEstatusTecnico, $idTecnico);

      if (!mysqli_stmt_execute($updateTecnico)) {
        throw new Exception('No se pudo actualizar el tecnico.');
      }

      mysqli_stmt_close($updateTecnico);

      mysqli_commit($con);

      $successMessage = 'Registro guardado correctamente en la base de datos.';
      $formValues = [
        'nombre_bahia' => '',
        'os' => '',
        'estatus' => 'En operación',
        'fecha' => '',
        'hora' => '',
        'cliente' => '',
        'motivo' => '',
        'id_tecnico' => ''
      ];
    } catch (Exception $exception) {
      mysqli_rollback($con);
      $errorMessage = $exception->getMessage();
    }
  }
}

if ($dbReady) {
  $ultimosQuery = "
    SELECT
      b.id_bahia,
      b.nombre_bahia,
      b.os,
      b.fecha_ingreso,
      b.hora_ingreso,
      b.cliente,
      b.motivo,
      b.estatus,
      t.nombre AS tecnico,
      t.estatus AS estatus_tecnico,
      CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellidoP, '')) AS creador
    FROM bahias b
    LEFT JOIN bahia_tecnico bt
      ON bt.id_bahia = b.id_bahia
      AND bt.activo = 1
    LEFT JOIN tecnicos t
      ON t.id_tecnico = bt.id_tecnico
    LEFT JOIN usuarios u
      ON u.id_usuario = b.creado_por
    ORDER BY b.id_bahia DESC
    LIMIT 5
  ";

  $ultimosResult = mysqli_query($con, $ultimosQuery);

  if ($ultimosResult) {
    while ($row = mysqli_fetch_assoc($ultimosResult)) {
      $ultimosRegistros[] = $row;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alta de Bahias</title>
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

    .list-card {
      padding: 24px;
    }

    .list-card p {
      margin-bottom: 18px;
    }

    .record-list {
      display: grid;
      gap: 12px;
    }

    .record-item {
      border: 1px solid #1e3956;
      background: rgba(255, 255, 255, 0.04);
      border-radius: 16px;
      padding: 16px;
      display: grid;
      gap: 8px;
    }

    .record-item__top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
    }

    .record-item__top strong {
      font-size: 22px;
    }

    .record-badge {
      padding: 6px 10px;
      border-radius: 999px;
      background: rgba(126, 196, 255, 0.12);
      color: #bfe0ff;
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .record-meta,
    .record-caption {
      font-size: 16px;
      color: #d0e1ff;
      line-height: 1.65;
    }

    .record-caption {
      color: #9db8dd;
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
            <h1>Alta de bahias</h1>
            <p>Captura la informacion principal de cada OS y asigna el tecnico responsable.</p>
          </div>

          <div class="clock-box">
            <div class="time" id="time">10:24:10 AM</div>
            <div class="date" id="date">21/05/2024</div>
          </div>
        </div>

        <div class="schema-note">
          La tabla <code>bahias</code> ahora guarda <code>nombre_bahia</code>, el <code>estatus</code> y el usuario que crea el registro.
          La tabla <code>bahia_tecnico</code> registra el tecnico asignado y el usuario que hizo la asignacion.
        </div>

        <?php if ($successMessage !== ''): ?>
          <div class="success-message"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($errorMessage !== ''): ?>
          <div class="error-message"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" class="form-grid">
          <div class="field">
            <label for="nombre_bahia">Nombre de la bahia</label>
            <select id="nombre_bahia" name="nombre_bahia" required>
              <option value="">Selecciona una bahia</option>
              <?php foreach ($bahiasDisponibles as $bahiaDisponible): ?>
                <option value="<?= htmlspecialchars($bahiaDisponible, ENT_QUOTES, 'UTF-8') ?>" <?= $formValues['nombre_bahia'] === $bahiaDisponible ? 'selected' : '' ?>>
                  <?= htmlspecialchars($bahiaDisponible, ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field">
            <label for="os">Orden de servicio</label>
            <input type="text" id="os" name="os" placeholder="Ej. OS-12465" value="<?= htmlspecialchars($formValues['os'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field">
            <label for="estatus">Estatus de la bahia</label>
            <select id="estatus" name="estatus" required>
              <option value="Disponible" <?= $formValues['estatus'] === 'Disponible' ? 'selected' : '' ?>>Disponible</option>
              <option value="En operación" <?= $formValues['estatus'] === 'En operación' ? 'selected' : '' ?>>En operación</option>
              <option value="En mantenimiento" <?= $formValues['estatus'] === 'En mantenimiento' ? 'selected' : '' ?>>En mantenimiento</option>
            </select>
          </div>

          <div class="field">
            <label for="id_tecnico">Tecnico</label>
            <select id="id_tecnico" name="id_tecnico" required>
              <option value="">Selecciona un tecnico</option>
              <?php foreach ($tecnicos as $tecnico): ?>
                <option value="<?= (int) $tecnico['id_tecnico'] ?>" <?= $formValues['id_tecnico'] === (string) $tecnico['id_tecnico'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($tecnico['nombre'], ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($tecnico['estatus'], ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
            <span class="helper-text">Se guardara la relacion en la tabla <code>bahia_tecnico</code>.</span>
          </div>

          <div class="field">
            <label for="fecha">Fecha de ingreso</label>
            <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($formValues['fecha'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field">
            <label for="hora">Hora de ingreso</label>
            <input type="time" id="hora" name="hora" value="<?= htmlspecialchars($formValues['hora'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field full">
            <label for="cliente">Cliente</label>
            <input type="text" id="cliente" name="cliente" placeholder="Nombre del cliente" value="<?= htmlspecialchars($formValues['cliente'], ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="field full">
            <label for="motivo">Motivo</label>
            <textarea id="motivo" name="motivo" placeholder="Describe el motivo del ingreso"><?= htmlspecialchars($formValues['motivo'], ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>

          <div class="form-actions">
            <a class="action-link secondary" href="main.php">Cancelar</a>
            <button class="button primary" type="submit">Guardar registro</button>
          </div>
        </form>
      </section>

      <aside class="list-card">
        <span class="section-eyebrow">Vista previa</span>
        <h2>Ultimos registros</h2>
        <p>Aqui se muestran los ultimos registros guardados desde la base de datos con su tecnico asignado.</p>

        <div class="record-list">
          <?php if (empty($ultimosRegistros)): ?>
            <div class="record-item">
              <div class="record-caption">Todavia no hay registros almacenados.</div>
            </div>
          <?php else: ?>
            <?php foreach ($ultimosRegistros as $registro): ?>
              <div class="record-item">
                <div class="record-item__top">
                  <strong><?= htmlspecialchars($registro['nombre_bahia'], ENT_QUOTES, 'UTF-8') ?></strong>
                  <span class="record-badge"><?= htmlspecialchars($registro['estatus'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="record-meta">
                  OS: <?= htmlspecialchars($registro['os'], ENT_QUOTES, 'UTF-8') ?><br>
                  <?= htmlspecialchars($registro['cliente'], ENT_QUOTES, 'UTF-8') ?><br>
                  Tecnico: <?= htmlspecialchars($registro['tecnico'] ?? 'Sin asignar', ENT_QUOTES, 'UTF-8') ?><br>
                  Capturado por: <?= htmlspecialchars(trim($registro['creador'] ?? ''), ENT_QUOTES, 'UTF-8') !== '' ? htmlspecialchars(trim($registro['creador']), ENT_QUOTES, 'UTF-8') : 'Sin referencia' ?>
                </div>
                <div class="record-caption">
                  <?= htmlspecialchars($registro['fecha_ingreso'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($registro['hora_ingreso'], ENT_QUOTES, 'UTF-8') ?><br>
                  <?= htmlspecialchars($registro['motivo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </aside>
    </div>
  </div>

  <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>
