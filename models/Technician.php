<?php
require_once __DIR__ . '/../core/Database.php';

class Technician
{
    private ?mysqli $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findAll(): array
    {
        if (!$this->db) {
            return [];
        }

        $query = "SELECT id_tecnico, nombre, estatus, correo_corporativo FROM tecnicos ORDER BY nombre ASC";
        $result = mysqli_query($this->db, $query);
        $technicians = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $technicians[] = $row;
            }
        }

        return $technicians;
    }
}
