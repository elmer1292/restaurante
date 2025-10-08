<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/ReporteModel.php';

class ReporteController extends BaseController {
    public function index() {
        $this->render('views/reportes/index.php');
    }

    public function ventas_dia() {
        $model = new ReporteModel();
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $ventas = $model->getVentasDiariasPorEmpleado($fecha);
        $this->render('views/reportes/ventas_dia.php', compact('ventas', 'fecha'));
    }

    public function productos_vendidos() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $model = new ReporteModel();
        $ventas = $model->getProductosVendidosPorFecha($fecha);
        $this->render('views/reportes/productos_vendidos.php', compact('ventas', 'fecha'));
    }

    public function cierre_caja() {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $model = new ReporteModel();
        $ventas = $model->getCierreCajaDiario($fecha);

        // Obtener movimientos del día
        require_once __DIR__ . '/../models/MovimientoModel.php';
        $movModel = new MovimientoModel();
        $movimientos = $movModel->obtenerMovimientos(null, $fecha, $fecha);

        $this->render('views/reportes/cierre_caja.php', compact('ventas', 'fecha', 'movimientos'));
    }
}