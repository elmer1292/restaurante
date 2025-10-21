<?php
require_once __DIR__ . '/../config/database.php';

class MovimientoModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Registra un movimiento en la base de datos
     * @param string $tipo Ej: 'Ingreso', 'Egreso', 'Apertura', 'Cierre'
     * @param float $monto
     * @param string $descripcion
     * @param int|null $idUsuario
     * @param int|null $idVenta
     * @return bool
     */
    public function registrarMovimiento($tipo, $monto, $descripcion = null, $idUsuario = null, $idVenta = null) {
        try {
            $stmt = $this->conn->prepare('INSERT INTO movimientos (Tipo, Monto, Descripcion, ID_Usuario, ID_Venta) VALUES (?, ?, ?, ?, ?)');
            return $stmt->execute([$tipo, $monto, $descripcion, $idUsuario, $idVenta]);
        } catch (PDOException $e) {
            error_log('Error en registrarMovimiento: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los movimientos, con filtros opcionales
     * @param string|null $tipo
     * @param string|null $fechaDesde formato 'Y-m-d'
     * @param string|null $fechaHasta formato 'Y-m-d'
     * @return array|false
     */
    public function obtenerMovimientos($tipo = null, $fechaDesde = null, $fechaHasta = null, $limit = null, $offset = null) {
        try {
            $sql = 'SELECT m.*, u.Nombre_Usuario, v.ID_Venta FROM movimientos m
                    LEFT JOIN usuarios u ON m.ID_Usuario = u.ID_usuario
                    LEFT JOIN ventas v ON m.ID_Venta = v.ID_Venta WHERE 1=1';
            $params = [];
            if ($tipo) {
                $sql .= ' AND m.Tipo = ?';
                $params[] = $tipo;
            }
            if ($fechaDesde) {
                $sql .= ' AND m.Fecha_Hora >= ?';
                $params[] = $fechaDesde . ' 00:00:00';
            }
            if ($fechaHasta) {
                $sql .= ' AND m.Fecha_Hora <= ?';
                $params[] = $fechaHasta . ' 23:59:59';
            }
            $sql .= ' ORDER BY m.Fecha_Hora DESC';
            if ($limit !== null && $offset !== null) {
                $limit = (int)$limit;
                $offset = (int)$offset;
                $sql .= " LIMIT $limit OFFSET $offset";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en obtenerMovimientos: ' . $e->getMessage());
            return false;
        }
    }

    public function contarMovimientos($tipo = null, $fechaDesde = null, $fechaHasta = null) {
        try {
            $sql = 'SELECT COUNT(*) as total FROM movimientos m WHERE 1=1';
            $params = [];
            if ($tipo) {
                $sql .= ' AND m.Tipo = ?';
                $params[] = $tipo;
            }
            if ($fechaDesde) {
                $sql .= ' AND m.Fecha_Hora >= ?';
                $params[] = $fechaDesde . ' 00:00:00';
            }
            if ($fechaHasta) {
                $sql .= ' AND m.Fecha_Hora <= ?';
                $params[] = $fechaHasta . ' 23:59:59';
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['total'] : 0;
        } catch (PDOException $e) {
            error_log('Error en contarMovimientos: ' . $e->getMessage());
            return 0;
        }
    }

    // Suma de ingresos no ventas: tipo Ingreso y ID_Venta IS NULL
    public function obtenerIngresosNoVentas($fecha = null) {
        try {
            $sql = "SELECT SUM(Monto) as total FROM movimientos WHERE Tipo = 'Ingreso' AND (ID_Venta IS NULL OR ID_Venta = 0)";
            $params = [];
            if ($fecha) {
                $sql .= ' AND DATE(Fecha_Hora) = ?';
                $params[] = $fecha;
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? floatval($row['total']) : 0.0;
        } catch (PDOException $e) {
            error_log('Error en obtenerIngresosNoVentas: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Obtiene el saldo actual (suma de ingresos - egresos)
     * @return float
     */
    public function obtenerSaldoActual() {
        try {
            $stmt = $this->conn->query("SELECT SUM(CASE WHEN Tipo='Ingreso' THEN Monto WHEN Tipo='Apertura' THEN Monto WHEN Tipo='Cierre' THEN 0 ELSE -Monto END) as saldo FROM movimientos");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? floatval($row['saldo']) : 0.0;
        } catch (PDOException $e) {
            error_log('Error en obtenerSaldoActual: ' . $e->getMessage());
            return 0.0;
        }
    }
    /**
     * Verifica si la caja está abierta (hay apertura sin cierre posterior)
     * @return bool
     */
    public function cajaAbierta() {
        $stmt = $this->conn->query("SELECT MAX(CASE WHEN Tipo='Apertura' THEN Fecha_Hora END) as apertura, MAX(CASE WHEN Tipo='Cierre' THEN Fecha_Hora END) as cierre FROM movimientos");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !$row['apertura']) return false;
        if (!$row['cierre']) return true;
        return strtotime($row['apertura']) > strtotime($row['cierre']);
    }

    /**
     * Obtiene el saldo desde la última apertura de caja
     * @return float
     */
    public function obtenerSaldoCajaAbierta() {
        $stmt = $this->conn->query("SELECT MAX(ID_Movimiento) as id_apertura FROM movimientos WHERE Tipo='Apertura'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !$row['id_apertura']) return 0.0;
        $idApertura = (int)$row['id_apertura'];
        $stmt2 = $this->conn->prepare("SELECT SUM(CASE WHEN Tipo IN ('Ingreso','Apertura') THEN Monto WHEN Tipo='Cierre' THEN 0 ELSE -Monto END) as saldo FROM movimientos WHERE ID_Movimiento >= ?");
        $stmt2->execute([$idApertura]);
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        return $row2 ? floatval($row2['saldo']) : 0.0;
    }

    /**
     * Obtiene el saldo según los filtros aplicados (tipo, fecha)
     * @param string|null $tipo
     * @param string|null $fechaDesde
     * @param string|null $fechaHasta
     * @return float
     */
    public function obtenerSaldoFiltrado($tipo = null, $fechaDesde = null, $fechaHasta = null) {
        try {
            $sql = "SELECT SUM(CASE WHEN m.Tipo IN ('Ingreso','Apertura') THEN m.Monto WHEN m.Tipo='Cierre' THEN 0 ELSE -m.Monto END) as saldo FROM movimientos m WHERE 1=1";
            $params = [];
            if ($tipo) {
                $sql .= ' AND m.Tipo = ?';
                $params[] = $tipo;
            }
            if ($fechaDesde) {
                $sql .= ' AND m.Fecha_Hora >= ?';
                $params[] = $fechaDesde . ' 00:00:00';
            }
            if ($fechaHasta) {
                $sql .= ' AND m.Fecha_Hora <= ?';
                $params[] = $fechaHasta . ' 23:59:59';
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? floatval($row['saldo']) : 0.0;
        } catch (PDOException $e) {
            error_log('Error en obtenerSaldoFiltrado: ' . $e->getMessage());
            return 0.0;
        }
    }
}
