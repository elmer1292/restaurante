<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/ReporteModel.php';

class ReporteController extends BaseController {
    public function inventario() {
        $model = new ReporteModel();
        $productos = $model->getInventario();
        if (isset($_GET['exportar']) && $_GET['exportar'] === 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="inventario_' . date('Ymd') . '.xls"');
            echo "<table border='1'>";
            echo "<tr><th>#</th><th>Producto</th><th>Categoría</th><th>Stock</th><th>Precio Costo</th><th>Precio Venta</th></tr>";
            foreach ($productos as $i => $prod) {
                echo "<tr>";
                echo "<td>" . ($i+1) . "</td>";
                echo "<td>" . htmlspecialchars($prod['Nombre_Producto']) . "</td>";
                echo "<td>" . htmlspecialchars($prod['Nombre_Categoria']) . "</td>";
                echo "<td>" . (int)$prod['Stock'] . "</td>";
                echo "<td>" . number_format($prod['Precio_Costo'], 2) . "</td>";
                echo "<td>" . number_format($prod['Precio_Venta'], 2) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            exit;
        }
        $this->render('views/reportes/inventario.php', compact('productos'));
    }
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