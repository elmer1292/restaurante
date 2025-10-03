<?php
require_once 'BaseController.php';


class VentaController extends BaseController {
    public function index() {
        $this->render('views/ventas/index.php');
    }

    public function registrarPago() {
    require_once __DIR__ . '/../config/Session.php';
    require_once __DIR__ . '/../models/VentaModel.php';
    require_once __DIR__ . '/../models/MovimientoModel.php';
        Session::init();
        Session::checkRole(['Administrador', 'Cajero']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }
        require_once __DIR__ . '/../helpers/Csrf.php';
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
            exit;
        }
        $idVenta = isset($_POST['id_venta']) ? (int)$_POST['id_venta'] : 0;
        $metodoPago = isset($_POST['metodo_pago']) ? substr(trim($_POST['metodo_pago']), 0, 200) : '';
        $monto = isset($_POST['monto']) ? (float)$_POST['monto'] : 0;
        if (!$idVenta || !$metodoPago || $monto <= 0) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            exit;
        }
        $ventaModel = new VentaModel();
        $movimientoModel = new MovimientoModel();
        $conn = (new Database())->connect();
        try {
            $stmt = $conn->prepare('SELECT Total FROM ventas WHERE ID_Venta = ?');
            $stmt->execute([$idVenta]);
            $venta = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$venta) throw new Exception('Venta no encontrada');
            $total = (float)$venta['Total'];
            $metodoPagoLower = strtolower($metodoPago);
            // Permitir exceso solo si el método es efectivo
            if ($monto > $total) {
                if (strpos($metodoPagoLower, 'efectivo') !== false) {
                    $cambio = $monto - $total;
                } else {
                    throw new Exception('El monto pagado excede el total de la venta');
                }
            }
            if ($monto < $total) throw new Exception('El monto pagado es menor al total de la venta');
            // Registrar el pago y marcar como pagada
            $stmtUp = $conn->prepare('UPDATE ventas SET Metodo_Pago = ?, Estado = "Pagada" WHERE ID_Venta = ?');
            $stmtUp->execute([$metodoPago, $idVenta]);
            // Liberar la mesa
            $stmtMesa = $conn->prepare("UPDATE mesas SET Estado = 0 WHERE ID_Mesa = (SELECT ID_Mesa FROM ventas WHERE ID_Venta = ?)");
            $stmtMesa->execute([$idVenta]);

            // Registrar movimiento de caja
            $idUsuario = null;
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id'];
            } elseif (isset($_SESSION['user']['ID_usuario'])) {
                $idUsuario = $_SESSION['user']['ID_usuario'];
            }
            $descripcion = 'Pago de venta ID ' . $idVenta . ' (' . $metodoPago . ')';
            $movimientoModel->registrarMovimiento('Ingreso', $total, $descripcion, $idUsuario, $idVenta);
            // Impresión de ticket removida temporalmente para evitar bloqueos y asegurar actualización inmediata de ventas
            $response = ['success' => true];
            if (isset($cambio)) {
                $response['cambio'] = $cambio;
            }
            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    // ticket
    public function ticket() {
        // El método ticket solo debe preparar datos o delegar a la vista según el router
        // Si usas un sistema de vistas, deberías hacer algo como:
        $this->render('views/ventas/ticket.php');
    }
}
