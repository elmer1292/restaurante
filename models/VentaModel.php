<?php
require_once __DIR__ . '/../config/database.php';

class VentaModel {
    public function actualizarCantidadDetalle($idDetalle, $nuevaCantidad) {
        try {
            $stmt = $this->conn->prepare('UPDATE detalle_venta SET Cantidad = ? WHERE ID_Detalle = ?');
            return $stmt->execute([$nuevaCantidad, $idDetalle]);
        } catch (PDOException $e) {
            error_log('Error en actualizarCantidadDetalle: ' . $e->getMessage());
            return false;
        }
    }
    public function eliminarDetalle($idDetalle) {
        try {
            $stmt = $this->conn->prepare('DELETE FROM detalle_venta WHERE ID_Detalle = ?');
            return $stmt->execute([$idDetalle]);
        } catch (PDOException $e) {
            error_log('Error en eliminarDetalle: ' . $e->getMessage());
            return false;
        }
    }
    // Método actualizarEstadoDetalle eliminado
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Crea una nueva venta y retorna el ID generado.
     * @param int $idCliente
     * @param int $idMesa
     * @param string $metodoPago
     * @param int $idEmpleado
     * @return int|false ID de la venta o false en error
     */
    public function createSale($idCliente, $idMesa, $metodoPago, $idEmpleado) {
        try {
            // Log de los valores recibidos para depuración
            error_log('createSale params: idCliente=' . var_export($idCliente, true) . ', idMesa=' . var_export($idMesa, true) . ', metodoPago=' . var_export($metodoPago, true) . ', idEmpleado=' . var_export($idEmpleado, true));
            $stmt = $this->conn->prepare('CALL sp_CreateSale(?, ?, ?, ?)');
            $stmt->execute([$idCliente, $idMesa, $metodoPago, $idEmpleado]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log('createSale result: ' . var_export($result, true));
            if ($result && isset($result['ID_Venta'])) {
                return $result['ID_Venta'];
            } else {
                error_log('No se obtuvo ID_Venta al crear la venta.');
                return false;
            }
        } catch (PDOException $e) {
            error_log('Error en createSale: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Agrega un detalle de producto a una venta.
     * @param int $idVenta
     * @param int $idProducto
     * @param int $cantidad
     * @param float $precioVenta
     * @return bool
     */
    public function addSaleDetail($idVenta, $idProducto, $cantidad, $precioVenta) {
        try {
            $stmt = $this->conn->prepare('CALL sp_AddSaleDetail(?, ?, ?, ?)');
            return $stmt->execute([$idVenta, $idProducto, $cantidad, $precioVenta]);
        } catch (PDOException $e) {
            error_log('Error en addSaleDetail: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los detalles de una venta con JOINs a productos y categorías.
     * @param int $idVenta
     * @return array|false
     */
    public function getSaleDetails($idVenta) {
        try {
            $stmt = $this->conn->prepare(
                'SELECT dv.*, p.Nombre_Producto, p.Precio_Venta, c.Nombre_Categoria
                 FROM detalle_venta dv
                 INNER JOIN productos p ON dv.ID_Producto = p.ID_Producto
                 INNER JOIN categorias c ON p.ID_Categoria = c.ID_Categoria
                 WHERE dv.ID_Venta = ?'
            );
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
                WHERE c.Nombre_Categoria = "Buffet" OR c.Nombre_Categoria = "Carnes" OR c.Nombre_Categoria = "Sopas" 
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
    
    public function getVentaActivaByMesa($idMesa) {
        try {
            $stmt = $this->conn->prepare(
                'SELECT * FROM ventas 
                WHERE ID_Mesa = ?
                ORDER BY Fecha_Hora DESC LIMIT 1'
            );
            $stmt->execute([$idMesa]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getVentaActivaByMesa: ' . $e->getMessage());
            return false;
        }
    }
}