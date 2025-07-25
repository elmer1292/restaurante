<?php
require_once __DIR__ . '/../config/database.php';

class VentaModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function createSale($idCliente, $idMesa, $metodoPago, $idEmpleado) {
        try {
            $stmt = $this->conn->prepare('CALL sp_CreateSale(?, ?, ?, ?)');
            $stmt->execute([$idCliente, $idMesa, $metodoPago, $idEmpleado]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['ID_Venta'];
        } catch (PDOException $e) {
            error_log('Error en createSale: ' . $e->getMessage());
            return false;
        }
    }

    public function addSaleDetail($idVenta, $idProducto, $cantidad, $precioVenta) {
        try {
            $stmt = $this->conn->prepare('CALL sp_AddSaleDetail(?, ?, ?, ?)');
            return $stmt->execute([$idVenta, $idProducto, $cantidad, $precioVenta]);
        } catch (PDOException $e) {
            error_log('Error en addSaleDetail: ' . $e->getMessage());
            return false;
        }
    }

    public function getSaleDetails($idVenta) {
        try {
            $stmt = $this->conn->prepare('CALL sp_GetSaleDetails(?)');
            $stmt->execute([$idVenta]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getSaleDetails: ' . $e->getMessage());
            return false;
        }
    }

    public function getDailySales($fecha) {
        try {
            $stmt = $this->conn->prepare('CALL sp_GetDailySales(?)');
            $stmt->execute([$fecha]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getDailySales: ' . $e->getMessage());
            return false;
        }
    }

    public function getVentaById($idVenta) {
        try {
            $stmt = $this->conn->prepare(
                'SELECT v.*, c.Nombre_Cliente, e.Nombre_Completo as Empleado, m.Numero_Mesa 
                FROM ventas v 
                INNER JOIN clientes c ON v.ID_Cliente = c.ID_Cliente 
                INNER JOIN empleados e ON v.ID_Empleado = e.ID_Empleado 
                LEFT JOIN mesas m ON v.ID_Mesa = m.ID_Mesa 
                WHERE v.ID_Venta = ?'
            );
            $stmt->execute([$idVenta]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getVentaById: ' . $e->getMessage());
            return false;
        }
    }

    public function getVentasPendientesCocina() {
        try {
            $stmt = $this->conn->prepare(
                'SELECT dv.*, p.Nombre_Producto, v.ID_Mesa, m.Numero_Mesa, v.Fecha_Hora 
                FROM detalle_venta dv 
                INNER JOIN productos p ON dv.ID_Producto = p.ID_Producto 
                INNER JOIN ventas v ON dv.ID_Venta = v.ID_Venta 
                INNER JOIN mesas m ON v.ID_Mesa = m.ID_Mesa 
                INNER JOIN categorias c ON p.ID_Categoria = c.ID_Categoria 
                WHERE c.Nombre_Categoria = "Comidas" OR c.Nombre_Categoria = "Carnes" 
                ORDER BY v.Fecha_Hora DESC'
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getVentasPendientesCocina: ' . $e->getMessage());
            return false;
        }
    }

    public function getVentasPendientesBarra() {
        try {
            $stmt = $this->conn->prepare(
                'SELECT dv.*, p.Nombre_Producto, v.ID_Mesa, m.Numero_Mesa, v.Fecha_Hora 
                FROM detalle_venta dv 
                INNER JOIN productos p ON dv.ID_Producto = p.ID_Producto 
                INNER JOIN ventas v ON dv.ID_Venta = v.ID_Venta 
                INNER JOIN mesas m ON v.ID_Mesa = m.ID_Mesa 
                INNER JOIN categorias c ON p.ID_Categoria = c.ID_Categoria 
                WHERE c.Nombre_Categoria = "Bebidas" 
                ORDER BY v.Fecha_Hora DESC'
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getVentasPendientesBarra: ' . $e->getMessage());
            return false;
        }
    }

    public function actualizarTotal($idVenta) {
        try {
            $stmt = $this->conn->prepare(
                'UPDATE ventas v 
                SET Total = (SELECT SUM(Subtotal) FROM detalle_venta WHERE ID_Venta = v.ID_Venta) 
                WHERE ID_Venta = ?'
            );
            return $stmt->execute([$idVenta]);
        } catch (PDOException $e) {
            error_log('Error en actualizarTotal: ' . $e->getMessage());
            return false;
        }
    }
}