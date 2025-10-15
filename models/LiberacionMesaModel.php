<?php
require_once dirname(__DIR__, 1) . '/config/database.php';

class LiberacionMesaModel {
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Registra una liberación de mesa
     * @param int $idMesa
     * @param int $idUsuario
     * @param string $motivo
     * @param string $descripcion
     * @return bool
     */
    public function registrarLiberacion($idMesa, $idUsuario, $motivo, $descripcion) {
        $sql = "INSERT INTO liberaciones_mesa (ID_Mesa, ID_Usuario, Motivo, Descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$idMesa, $idUsuario, $motivo, $descripcion]);
    }
}
