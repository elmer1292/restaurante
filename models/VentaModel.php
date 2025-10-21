<?php
require_once __DIR__ . '/../config/database.php';

class VentaModel {
    /**
     * Divide la cuenta en parciales y asigna productos a cada parcial
     * @param int $idVenta
     * @param array $parciales Ej: [1 => [id_detalle => cantidad, ...], ...]
     * @return bool
     */
    public function dividirCuenta($idVenta, $parciales) {
        try {
            // 1. Crear parciales en tabla parciales_venta
            $idsParciales = [];
            foreach ($parciales as $num => $productos) {
                $stmt = $this->conn->prepare('INSERT INTO parciales_venta (ID_Venta, nombre_cliente) VALUES (?, ?)');
                $stmt->execute([$idVenta, 'Parcial ' . $num]);
                $idsParciales[$num] = $this->conn->lastInsertId();
            }
            // 2. Asignar productos a parciales (actualizar detalle_venta)
            foreach ($parciales as $num => $productos) {
                $idParcial = $idsParciales[$num];
                foreach ($productos as $idDetalle => $cantidad) {
                    if ($cantidad > 0) {
                        // Actualizar el detalle con el ID_Parcial y la cantidad
                        $stmt = $this->conn->prepare('UPDATE detalle_venta SET ID_Parcial = ?, Cantidad = ? WHERE ID_Detalle = ?');
                        $stmt->execute([$idParcial, $cantidad, $idDetalle]);
                    }
                }
            }
            return true;
        } catch (PDOException $e) {
            error_log('Error en dividirCuenta: ' . $e->getMessage());
            return false;
        }
    }
    public function actualizarCantidadDetalle($idDetalle, $nuevaCantidad) {
        try {
            // Obtener el precio actual del detalle (Precio_Venta)
            $stmt = $this->conn->prepare('SELECT Precio_Venta FROM detalle_venta WHERE ID_Detalle = ? LIMIT 1');
            $stmt->execute([$idDetalle]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                error_log('actualizarCantidadDetalle: detalle no encontrado ID_Detalle=' . $idDetalle);
                return false;
            }
            $precio = (float)$row['Precio_Venta'];
            $subtotal = $precio * (float)$nuevaCantidad;
            $stmt2 = $this->conn->prepare('UPDATE detalle_venta SET Cantidad = ?, Subtotal = ? WHERE ID_Detalle = ?');
            return $stmt2->execute([$nuevaCantidad, $subtotal, $idDetalle]);
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
            // Log temporal para depuración
            error_log('VentaModel::createSale - mesaId: ' . print_r($idMesa, true));
            error_log('VentaModel::createSale - empleadoId: ' . print_r($idEmpleado, true));
            //            error_log('VentaModel::createSale - productos: ' . print_r($productos, true));
            $stmt = $this->conn->prepare('CALL sp_CreateSale(?, ?, ?, ?)');
            $stmt->execute([$idCliente, $idMesa, $metodoPago, $idEmpleado]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            //            error_log('VentaModel::createSale - productosJson: ' . $productosJson);
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
    public function addSaleDetail($idVenta, $idProducto, $cantidad, $precioVenta, $preparacion = null) {
        try {
            // Si hay preparación, insertar directo, si no, usar el SP para compatibilidad
            if ($preparacion !== null && $preparacion !== '') {
                $stmt = $this->conn->prepare('SELECT ID_Detalle FROM detalle_venta WHERE ID_Venta = ? AND ID_Producto = ? AND (preparacion IS NULL OR preparacion = ?) LIMIT 1');
                $stmt->execute([$idVenta, $idProducto, $preparacion]);
                $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($detalle && isset($detalle['ID_Detalle'])) {
                    // Actualizar cantidad y subtotal si ya existe con la misma preparación
                    $stmt = $this->conn->prepare('UPDATE detalle_venta SET Cantidad = Cantidad + ?, Subtotal = (Cantidad + ?) * ? WHERE ID_Detalle = ?');
                    return $stmt->execute([$cantidad, $cantidad, $precioVenta, $detalle['ID_Detalle']]);
                } else {
                    $stmt = $this->conn->prepare('INSERT INTO detalle_venta (ID_Venta, ID_Producto, Cantidad, Precio_Venta, Subtotal, preparacion) VALUES (?, ?, ?, ?, ?, ?)');
                    return $stmt->execute([$idVenta, $idProducto, $cantidad, $precioVenta, $cantidad * $precioVenta, $preparacion]);
                }
            } else {
                // Sin preparación, usar SP existente
                $stmt = $this->conn->prepare('CALL sp_AddSaleDetail(?, ?, ?, ?)');
                return $stmt->execute([$idVenta, $idProducto, $cantidad, $precioVenta]);
            }
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
                'SELECT v.*, c.Nombre_Cliente, e.Nombre_Completo as Empleado, e.ID_Usuario, m.Numero_Mesa 
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
                                'SELECT dv.*, dv.preparacion, p.Nombre_Producto, v.ID_Mesa, m.Numero_Mesa, v.Fecha_Hora 
                                FROM detalle_venta dv 
                                INNER JOIN productos p ON dv.ID_Producto = p.ID_Producto 
                                INNER JOIN ventas v ON dv.ID_Venta = v.ID_Venta 
                                INNER JOIN mesas m ON v.ID_Mesa = m.ID_Mesa 
                                INNER JOIN categorias c ON p.ID_Categoria = c.ID_Categoria 
                                WHERE c.Nombre_Categoria != "Bebidas" 
                                    AND v.Estado = "Pendiente" 
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
                                    AND v.Estado = "Pendiente" 
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
    
    /**
     * Crea una venta marcada como delivery.
     * @param int|null $idCliente
     * @param int|null $idEmpleado
     * @param string $metodoPago
     * @return int|false
     */
    public function createDeliverySale($idCliente = null, $idEmpleado = null, $metodoPago = 'Delivery') {
        try {
            // Reusar createSale (sp) pasando null para mesa
            return $this->createSale($idCliente, null, $metodoPago, $idEmpleado);
        } catch (Exception $e) {
            error_log('Error en createDeliverySale: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Alias para agregar detalle a la venta (compatibilidad)
     */
    public function addDetalleVenta($idVenta, $idProducto, $cantidad, $precioVenta, $preparacion = null) {
        return $this->addSaleDetail($idVenta, $idProducto, $cantidad, $precioVenta, $preparacion);
    }

    /**
     * Obtener ventas pendientes de tipo delivery
     */
    public function getVentasDeliveryPendientes() {
        try {
            $stmt = $this->conn->prepare('SELECT v.*, c.Nombre_Cliente FROM ventas v LEFT JOIN clientes c ON v.ID_Cliente = c.ID_Cliente WHERE v.es_delivery = 1 AND v.Estado = "Pendiente" ORDER BY v.Fecha_Hora DESC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getVentasDeliveryPendientes: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza el estado de una venta (ej: Pendiente, Preparado, Entregado, Pagado)
     */
    public function updateEstado($idVenta, $estado) {
        try {
            $stmt = $this->conn->prepare('UPDATE ventas SET Estado = ? WHERE ID_Venta = ?');
            return $stmt->execute([$estado, $idVenta]);
        } catch (PDOException $e) {
            error_log('Error en updateEstado: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Devuelve venta con sus detalles (compatibilidad con la vista)
     */
    public function getVentaConDetalles($idVenta) {
        $venta = $this->getVentaById($idVenta);
        if (!$venta) return false;
        $venta['detalles'] = $this->getSaleDetails($idVenta);
        return $venta;
    }
    
    public function getVentaActivaByMesa($idMesa) {
        try {
            $stmt = $this->conn->prepare(
                'SELECT * FROM ventas 
                WHERE ID_Mesa = ? AND Estado = "Pendiente"
                ORDER BY Fecha_Hora DESC LIMIT 1'
            );
            $stmt->execute([$idMesa]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getVentaActivaByMesa: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteSale($idVenta) {

    // Eliminar detalles primero
    $stmtDetalles = $this->conn->prepare("DELETE FROM detalle_venta WHERE ID_Venta = ?");
    $stmtDetalles->execute([$idVenta]);
    // Eliminar la venta principal
    $stmtVenta = $this->conn->prepare("DELETE FROM ventas WHERE ID_Venta = ?");
    return $stmtVenta->execute([$idVenta]);
    }
}