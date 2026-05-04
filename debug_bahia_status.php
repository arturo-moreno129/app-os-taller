<?php
require_once __DIR__ . '/core/Database.php';

$db = Database::connect();
if (!$db) {
    echo "DB connection failed\n";
    exit(1);
}

$query = 'SELECT id_bahia, nombre_bahia, estatus, fecha_ingreso, hora_ingreso, cliente, motivo FROM bahias ORDER BY id_bahia DESC LIMIT 20';
$result = mysqli_query($db, $query);
if (!$result) {
    echo "Query failed: " . mysqli_error($db) . "\n";
    exit(1);
}

while ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
}
