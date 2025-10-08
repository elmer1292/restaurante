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
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $porPagina = 20;
        $offset = ($pagina - 1) * $porPagina;
        $movimientos = $movimientoModel->obtenerMovimientos($tipo, $fechaDesde, $fechaHasta, $porPagina, $offset);
        $totalMovimientos = $movimientoModel->contarMovimientos($tipo, $fechaDesde, $fechaHasta);
        $totalPaginas = ceil($totalMovimientos / $porPagina);
        $saldo = $movimientoModel->obtenerSaldoFiltrado($tipo, $fechaDesde, $fechaHasta);
        $mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
        unset($_SESSION['mensaje']);
        $this->render('views/movimientos/index.php', compact('movimientos', 'saldo', 'mensaje', 'tipo', 'fechaDesde', 'fechaHasta', 'pagina', 'totalPaginas'));
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
