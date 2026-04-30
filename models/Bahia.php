<?php
require_once __DIR__ . '/../core/Database.php';

class Bahia
{
    private ?mysqli $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(array $data): array
    {
        if (!$this->db) {
            return [false, 'No fue posible conectar con la base de datos.'];
        }

        mysqli_begin_transaction($this->db);

        try {
            $insertBahia = mysqli_prepare(
                $this->db,
                "INSERT INTO bahias (nombre_bahia, os, fecha_ingreso, hora_ingreso, cliente, motivo, estatus, creado_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$insertBahia) {
                throw new Exception('No se pudo preparar el registro de bahía.');
            }

            mysqli_stmt_bind_param(
                $insertBahia,
                'sssssssi',
                $data['nombre_bahia'],
                $data['os'],
                $data['fecha'],
                $data['hora'],
                $data['cliente'],
                $data['motivo'],
                $data['estatus'],
                $data['creado_por']
            );

            if (!mysqli_stmt_execute($insertBahia)) {
                throw new Exception('No se pudo guardar la bahía.');
            }

            $idBahia = mysqli_insert_id($this->db);
            mysqli_stmt_close($insertBahia);

            $insertAsignacion = mysqli_prepare(
                $this->db,
                "INSERT INTO bahia_tecnico (id_bahia, id_tecnico, activo, asignado_por) VALUES (?, ?, 1, ?)"
            );

            if (!$insertAsignacion) {
                throw new Exception('No se pudo preparar la asignación del técnico.');
            }

            mysqli_stmt_bind_param($insertAsignacion, 'iii', $idBahia, $data['id_tecnico'], $data['creado_por']);

            if (!mysqli_stmt_execute($insertAsignacion)) {
                throw new Exception('No se pudo asignar el técnico a la bahía.');
            }

            mysqli_stmt_close($insertAsignacion);

            $nuevoEstatusTecnico = $data['estatus'] === 'En mantenimiento' ? 'Libre' : 'En operación';
            $updateTecnico = mysqli_prepare($this->db, "UPDATE tecnicos SET estatus = ? WHERE id_tecnico = ?");

            if (!$updateTecnico) {
                throw new Exception('No se pudo preparar la actualización del técnico.');
            }

            mysqli_stmt_bind_param($updateTecnico, 'si', $nuevoEstatusTecnico, $data['id_tecnico']);

            if (!mysqli_stmt_execute($updateTecnico)) {
                throw new Exception('No se pudo actualizar el estatus del técnico.');
            }

            mysqli_stmt_close($updateTecnico);
            mysqli_commit($this->db);

            return [true, 'Registro guardado correctamente en la base de datos.'];
        } catch (Exception $exception) {
            mysqli_rollback($this->db);
            return [false, $exception->getMessage()];
        }
    }

    public function getRecent(int $limit = 5): array
    {
        if (!$this->db) {
            return [];
        }

        $query = "
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
            LIMIT ?
        ";

        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $records = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $records[] = $row;
            }
        }

        mysqli_stmt_close($stmt);
        return $records;
    }
}
