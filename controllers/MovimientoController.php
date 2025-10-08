<?php
require_once 'BaseController.php';
require_once dirname(__DIR__, 1) . '/models/MovimientoModel.php';
require_once dirname(__DIR__, 1) . '/config/Session.php';

class MovimientoController extends BaseController {
    public function index() {
        Session::init();
        Session::checkRole(['Administrador', 'Cajero']);
        $movimientoModel = new MovimientoModel();
        $tipo = $_GET['tipo'] ?? null;
        $fechaDesde = $_GET['fecha_desde'] ?? null;
        $fechaHasta = $_GET['fecha_hasta'] ?? null;
        require_once __DIR__ . '/../helpers/Pagination.php';
        $params = getPageParams($_GET, 50); // 50 como límite máximo por página
        $movimientos = $movimientoModel->obtenerMovimientos($tipo, $fechaDesde, $fechaHasta, $params['limit'], $params['offset']);
        $totalMovimientos = $movimientoModel->contarMovimientos($tipo, $fechaDesde, $fechaHasta);
        $totalPaginas = ($params['limit'] > 0) ? ceil($totalMovimientos / $params['limit']) : 1;
        $saldo = $movimientoModel->obtenerSaldoFiltrado($tipo, $fechaDesde, $fechaHasta);
        $mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
        unset($_SESSION['mensaje']);
        $this->render('views/movimientos/index.php', [
            'movimientos' => $movimientos,
            'saldo' => $saldo,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'pagina' => $params['page'],
            'totalPaginas' => $totalPaginas,
            'limit' => $params['limit']
        ]);
    }

    public function registrar() {
        Session::init();
        Session::checkRole(['Administrador', 'Cajero']);
        $movimientoModel = new MovimientoModel();
        $mensaje = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'] ?? '';
            $monto = floatval($_POST['monto'] ?? 0);
            $descripcion = $_POST['descripcion'] ?? '';
            $idUsuario = Session::get('ID_Usuario');
            $ok = $movimientoModel->registrarMovimiento($tipo, $monto, $descripcion, $idUsuario);
            if ($ok) {
                $_SESSION['mensaje'] = 'Movimiento registrado correctamente.';
                header('Location: ' . BASE_URL . 'movimientos');
                exit;
            } else {
                $mensaje = 'Error al registrar el movimiento.';
            }
        }
        $this->render('views/movimientos/registrar.php', compact('mensaje'));
    }
}
