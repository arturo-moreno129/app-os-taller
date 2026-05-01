<?php
if (!defined('APP_INIT')) {
    http_response_code(403);
    exit('Acceso denegado.');
}

foreach ($bays as $bay):
    $isFree = $bay['estado'] === 'Libre';
    $statusText = $isFree ? 'Disponible' : 'Ocupado en taller';
    $availableText = $isFree ? '🚗 Espacio libre para próxima unidad' : '🚫 Ocupado, no disponible';
    $bayClass = $isFree ? 'bay-free' : 'bay-busy';
    $headerColor = $bayColors[$bay['nombre_bahia']] ?? 'blue';
?>
    <div class="bay <?= $bayClass ?>">
      <div class="bay-header <?= $headerColor ?>">
        <span class="bay-title">
          <img class="bay-header-car" src="assets/icon_carro.png" alt="Icono de bahia">
          <?= htmlspecialchars($bay['nombre_bahia'], ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="badge <?= $bayClass ?>"><?= htmlspecialchars($bay['estado'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="bay-overview">
        <span class="status-indicator" aria-hidden="true"></span>
        <img class="bay-car-image" src="assets/icon_carro.png" alt="Unidad en bahia">
        <p class="bay-status-text"><?= htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <div class="card">
        <div class="os"><?= htmlspecialchars($bay['os'], ENT_QUOTES, 'UTF-8') ?></div>
        <div class="info">
          <?= $bay['fecha_ingreso'] !== '' ? '📅 ' . htmlspecialchars($bay['fecha_ingreso'], ENT_QUOTES, 'UTF-8') . ' &nbsp;&nbsp; 🕒 ' . htmlspecialchars($bay['hora_ingreso'], ENT_QUOTES, 'UTF-8') : 'Sin hora registrada' ?><br>
          👤 Cliente: <?= htmlspecialchars($bay['cliente'], ENT_QUOTES, 'UTF-8') ?><br>
          <?= $bay['motivo'] !== '' ? '📝 Motivo: ' . htmlspecialchars($bay['motivo'], ENT_QUOTES, 'UTF-8') . '<br>' : '' ?>
          🔧 Técnico: <?= htmlspecialchars($bay['tecnico'], ENT_QUOTES, 'UTF-8') ?>
        </div>
      </div>
      <div class="available"><?= $availableText ?></div>
    </div>
<?php endforeach; ?>
