<?php
require_once dirname(__DIR__, 1) . '/config/database.php';

class ConfigModel {
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    // Obtener todas las configuraciones
    public function getAll() {
        $stmt = $this->conn->query('SELECT clave, valor FROM config');
        $result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $result;
    }
    // Obtener una configuración por clave
    public function get($clave) {
        $stmt = $this->conn->prepare('SELECT valor FROM config WHERE clave = ?');
        $stmt->execute([$clave]);
        return $stmt->fetchColumn();
    }
    // Actualizar una configuración por clave
    public function set($clave, $valor) {
        $stmt = $this->conn->prepare('UPDATE config SET valor = ? WHERE clave = ?');
        return $stmt->execute([$valor, $clave]);
    }
}
