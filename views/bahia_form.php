<?php /** @var string $successMessage */ ?>
<?php /** @var string $errorMessage */ ?>
<?php /** @var array $formValues */ ?>
<?php /** @var array $tecnicos */ ?>
<?php /** @var array $ultimosRegistros */ ?>
<?php /** @var string $nombreUsuario */ ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alta de Bahias</title>
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

        <form method="post" action="index.php?action=alta_bahias" class="form-grid">
          <div class="field">
            <label for="nombre_bahia">Nombre de la bahia</label>
            <select id="nombre_bahia" name="nombre_bahia" required>
              <option value="">Selecciona una bahia</option>
              <?php foreach ($bahiasDisponibles = ['BAHIA 1', 'BAHIA 2', 'BAHIA 3', 'BAHIA 4'] as $bahiaDisponible): ?>
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
              <?php foreach (['Disponible', 'En operación', 'En mantenimiento'] as $statusOption): ?>
                <option value="<?= htmlspecialchars($statusOption, ENT_QUOTES, 'UTF-8') ?>" <?= $formValues['estatus'] === $statusOption ? 'selected' : '' ?>><?= htmlspecialchars($statusOption, ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
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
            <a class="action-link secondary" href="index.php?action=dashboard">Cancelar</a>
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

  <?php include __DIR__ . '/../footer.php'; ?>
</body>

</html>
