<?php    
require_once 'BaseController.php';
require_once __DIR__ . '/../models/VentaModel.php';


class ComandaAjaxController extends BaseController {
    public function liberarMesa() {
    require_once __DIR__ . '/../helpers/Csrf.php';
    require_once __DIR__ . '/../helpers/Validator.php';
    require_once __DIR__ . '/../models/MesaModel.php';
    require_once __DIR__ . '/../models/VentaModel.php';
    require_once __DIR__ . '/../models/LiberacionMesaModel.php';
    require_once __DIR__ . '/../config/Session.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $idMesa = Validator::int(Validator::get($_POST, 'id_mesa'));
            $idVenta = Validator::int(Validator::get($_POST, 'id_venta'));
            $motivo = Validator::sanitizeString(Validator::get($_POST, 'motivo', ''));
            $descripcion = Validator::sanitizeString(Validator::get($_POST, 'descripcion', ''));
            $idUsuario = null;
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id'];
            } elseif (isset($_SESSION['user']['ID_usuario'])) {
                $idUsuario = $_SESSION['user']['ID_usuario'];
            }
            if ($idMesa === null || $idVenta === null || !$motivo || !$idUsuario) {
                echo json_encode(['success' => false, 'error' => 'Datos insuficientes']);
                exit;
            }
            $ventaModel = new VentaModel();
            $liberacionModel = new LiberacionMesaModel();
            $detalles = $ventaModel->getSaleDetails($idVenta);
            $conn = (new Database())->connect();
            try {
                $conn->beginTransaction();
                // Registrar liberación
                $liberacionModel->registrarLiberacion($idMesa, $idUsuario, $motivo, $descripcion);
                if (empty($detalles)) {
                    // Eliminar la venta si no tiene detalles
                    $ventaModel->deleteSale($idVenta);
                }
                // Liberar la mesa usando la MISMA conexión
                $stmt = $conn->prepare('CALL sp_UpdateTableStatus(?, ?)');
                $stmt->execute([$idMesa, 0]);
                $conn->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $conn->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        exit;
    }

    public function agregarProductos() {
        require_once __DIR__ . '/../helpers/Csrf.php';
        require_once __DIR__ . '/../helpers/Validator.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
            if (!Csrf::validateToken($csrfToken)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                exit;
            }
            $idMesa = Validator::int(Validator::get($_POST, 'id_mesa'));
            $productos = Validator::get($_POST, 'productos');
            $productos = is_string($productos) ? json_decode($productos, true) : $productos;
            if ($idMesa === null || empty($productos)) {
                echo json_encode(['success' => false, 'error' => 'Datos insuficientes']);
                exit;
            }
            $ventaModel = new VentaModel();
            require_once __DIR__ . '/../models/ProductModel.php';
            $productModel = new ProductModel();
            $comanda = $ventaModel->getVentaActivaByMesa($idMesa);
            if (!$comanda) {
                echo json_encode(['success' => false, 'error' => 'No hay comanda activa']);
                exit;
            }
            $idVenta = $comanda['ID_Venta'];

            // Ya no se imprime aquí. Solo se guarda. La impresión se maneja por imprimirComanda.

            $conn = (new Database())->connect();
            try {
                $conn->beginTransaction();
                foreach ($productos as $p) {
                    $pid = Validator::int(Validator::get($p, 'id'));
                    $cantidad = Validator::int(Validator::get($p, 'cantidad'));
                    $nombre = Validator::sanitizeString(Validator::get($p, 'nombre', ''));
                    $preparacion = Validator::sanitizeString(Validator::get($p, 'preparacion', null));
                    if ($pid === null || $cantidad === null) {
                        throw new Exception('Producto inválido: ' . $nombre);
                    }
                    // Consultar el precio real del producto
                    $productoDB = $productModel->getAllProducts();
                    $precioReal = null;
                    foreach ($productoDB as $prod) {
                        if ($prod['ID_Producto'] == $pid) {
                            $precioReal = $prod['Precio_Venta'];
                            break;
                        }
                    }
                    if ($precioReal === null) {
                        throw new Exception('No se encontró el producto en la base de datos: ' . $nombre);
                    }
                    $res = $ventaModel->addSaleDetail($idVenta, $pid, $cantidad, $precioReal, $preparacion);
                    if (!$res) {
                        throw new Exception('Error al agregar producto: ' . $nombre);
                    }
                }
                $ventaModel->actualizarTotal($idVenta);
                $conn->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $conn->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }
        echo json_encode(['success' => false, 'error' => 'Método no permitido']);
        exit;
    }
        public function crearComanda() {
            require_once __DIR__ . '/../helpers/Csrf.php';
            require_once __DIR__ . '/../helpers/Validator.php';
            require_once __DIR__ . '/../models/MesaModel.php';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $csrfToken = Validator::get($_POST, 'csrf_token', Validator::get($_SERVER, 'HTTP_X_CSRF_TOKEN', ''));
                if (!Csrf::validateToken($csrfToken)) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
                    exit;
                }
                $idMesa = Validator::int(Validator::get($_POST, 'id_mesa'));
                $productos = Validator::get($_POST, 'productos', []);
                $productos = is_string($productos) ? json_decode($productos, true) : $productos;
                if ($idMesa === null) {
                    error_log('ID de mesa no recibido');
                    echo json_encode(['success' => false, 'error' => 'ID de mesa requerido']);
                    exit;
                }
                $ventaModel = new VentaModel();
                $mesaModel = new MesaModel();
                // Verificar que la mesa existe y está libre
                $mesa = $mesaModel->getTableById($idMesa);
                if (!$mesa) {
                    error_log('Mesa no encontrada: ' . $idMesa);
                    echo json_encode(['success' => false, 'error' => 'Mesa no encontrada']);
                    exit;
                }
                if ($mesa['Estado'] != 0) {
                    error_log('La mesa no está libre: ' . print_r($mesa, true));
                    echo json_encode(['success' => false, 'error' => 'La mesa no está libre']);
                    exit;
                }
                try {
                    $conn = (new Database())->connect();
                    $conn->beginTransaction();
                    // Crear la venta (comanda)
                    $idEmpleado = isset($_SESSION['empleado_id']) ? $_SESSION['empleado_id'] : null;
                    $idVenta = $ventaModel->createSale(1, $idMesa, 'Efectivo', $idEmpleado); // Cliente por defecto: 1
                    // Agregar productos si existen
                    if (!empty($productos)) {
                        foreach ($productos as $p) {
                            $pid = Validator::int(Validator::get($p, 'id'));
                            $cantidad = Validator::int(Validator::get($p, 'cantidad'));
                            $precio = Validator::float(Validator::get($p, 'precio'));
                            $nombre = Validator::sanitizeString(Validator::get($p, 'nombre', ''));
                        }
                        $ventaModel->actualizarTotal($idVenta);
                    }
                    // Marcar la mesa como ocupada
                    $mesaModel->updateTableStatus($idMesa, 1); // 1 = ocupada
                    $conn->commit();
                    echo json_encode(['success' => true, 'id_venta' => $idVenta]);
                } catch (Exception $e) {
                    if (isset($conn)) $conn->rollBack();
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
                exit;
            }
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }
}
