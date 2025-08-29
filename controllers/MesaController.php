<?php

require_once 'BaseController.php';
require_once dirname(__DIR__, 1) . '/models/MesaModel.php';
require_once dirname(__DIR__, 1) . '/models/VentaModel.php';
require_once dirname(__DIR__, 1) . '/models/ProductModel.php';
require_once dirname(__DIR__, 1) . '/config/Session.php';

class MesaController extends BaseController {
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
