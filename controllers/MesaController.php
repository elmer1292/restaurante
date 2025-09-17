<?php
require_once 'BaseController.php';
require_once dirname(__DIR__, 1) . '/models/MesaModel.php';
require_once dirname(__DIR__, 1) . '/models/VentaModel.php';
require_once dirname(__DIR__, 1) . '/models/ProductModel.php';
require_once dirname(__DIR__, 1) . '/config/Session.php';

class MesaController extends BaseController {
    public function dividirCuenta() {
        Session::init();
        Session::checkRole(['Administrador', 'Mesero', 'Cajero']);
        $idMesa = $_GET['id_mesa'] ?? $_POST['id_mesa'] ?? null;
        $ventaModel = new VentaModel();
        $mesaModel = new MesaModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $comanda = $ventaModel->getVentaActivaByMesa($idMesa);
            if (!$comanda) {
                $mensaje = 'No hay comanda activa para esta mesa.';
                $this->render('views/mesas/dividir_cuenta.php', compact('mensaje'));
                return;
            }
            $idVenta = $comanda['ID_Venta'];
            // Procesar los parciales enviados
            $parciales = [];
            foreach ($_POST as $key => $val) {
                if (preg_match('/^parcial(\d+)$/', $key, $m)) {
                    $num = $m[1];
                    $parciales[$num] = $val; // $val es array: [id_detalle => cantidad]
                }
            }
            $ok = $ventaModel->dividirCuenta($idVenta, $parciales);
            if ($ok) {
                $_SESSION['mensaje'] = 'División guardada correctamente.';
                header('Location: ' . BASE_URL . 'mesa?id_mesa=' . $idMesa);
                exit;
            } else {
                $mensaje = 'Error al guardar la división.';
            }
        }
        // GET normal: mostrar vista
        $mesa = $mesaModel->getTableById($idMesa);
        $comanda = $ventaModel->getVentaActivaByMesa($idMesa);
        if (!$comanda) {
            $mensaje = 'No hay comanda activa para esta mesa.';
            $this->render('views/mesas/dividir_cuenta.php', compact('mensaje'));
            return;
        }
        $detalles = $ventaModel->getSaleDetails($comanda['ID_Venta']);
        $csrf_token = Csrf::getToken();
        $this->render('views/mesas/dividir_cuenta.php', compact('mesa', 'comanda', 'detalles', 'csrf_token', 'idMesa'));
    }
    public function index() {
        Session::init();
        Session::checkRole(['Administrador', 'Mesero', 'Cajero']);
        $userRole = Session::getUserRole();
        $mesaModel = new MesaModel();
        $mesas = $mesaModel->getAllTables();
        $totalMesas = $mesaModel->getTotalTables();
        $mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
        unset($_SESSION['mensaje']);
        $this->render('views/mesas/index.php', compact('mesas', 'totalMesas', 'userRole', 'mensaje'));
    }

    public function detalle() {
        Session::init();
        Session::checkRole(['Administrador', 'Mesero', 'Cajero']);
        $idMesa = $_GET['id_mesa'] ?? null;
        if (!$idMesa) {
            $mensaje = 'Mesa no especificada.';
            $this->render('views/mesas/mesa.php', compact('mensaje'));
            return;
        }
        $ventaModel = new VentaModel();
        $productModel = new ProductModel();
        $mesaModel = new MesaModel();
        $mesa = $mesaModel->getTableById($idMesa);
        $comanda = $ventaModel->getVentaActivaByMesa($idMesa);
        $mostrarCrearComanda = (!$comanda && $mesa && $mesa['Estado'] == 0);
        $detalles = $comanda ? $ventaModel->getSaleDetails($comanda['ID_Venta']) : [];
        $csrf_token = Csrf::getToken();
        $userRole = Session::getUserRole();
        $this->render('views/mesas/mesa.php', compact('mesa', 'comanda', 'mostrarCrearComanda', 'detalles', 'csrf_token', 'idMesa', 'userRole'));
    }
    
}
