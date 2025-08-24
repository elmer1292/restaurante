<?php
require_once 'config/Session.php';
require_once 'models/VentaModel.php';
require_once 'models/MesaModel.php';

Session::init();
Session::checkRole(['Administrador', 'Mesero', 'Cajero']);

$ventaModel = new VentaModel();
$mesaModel = new MesaModel();
$comandas = $ventaModel->getVentasPendientesCocina(); // Puedes combinar con barra si lo deseas

echo '<h2>Comandas Activas</h2>';
if (empty($comandas)) {
    echo '<div class="alert alert-info">No hay comandas activas.</div>';
} else {
    echo '<table class="table">';
    echo '<thead><tr><th>Mesa</th><th>Fecha</th><th>Acciones</th></tr></thead><tbody>';
    foreach ($comandas as $comanda) {
        $mesa = $mesaModel->getTableById($comanda['ID_Mesa']);
        echo '<tr>';
        echo '<td>' . htmlspecialchars($mesa['Numero_Mesa']) . '</td>';
        echo '<td>' . htmlspecialchars($comanda['Fecha_Hora']) . '</td>';
        echo '<td>';
        echo '<a href="/restaurante/views/mesas/ordenes.php?id_mesa=' . $comanda['ID_Mesa'] . '" class="btn btn-info btn-sm">Ver Detalle</a>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

echo '<hr>';
echo '<div class="row">';
echo '<div class="col-md-6">';
include 'barra.php';
echo '</div>';
echo '<div class="col-md-6">';
include 'cocina.php';
echo '</div>';
echo '</div>';
?>