<?php
require_once 'config/Session.php';
require_once 'models/VentaModel.php';
require_once 'models/MesaModel.php';

Session::init();
Session::checkRole(['Administrador', 'Mesero', 'Cajero']);

$ventaModel = new VentaModel();
$mesaModel = new MesaModel();
$comandas = $ventaModel->getVentasPendientesCocina(); // Puedes combinar con barra si lo deseas

echo '<div class="row">';
echo '<div class="col-md-6">';
include 'barra.php';
echo '</div>';
echo '<div class="col-md-6">';
include 'cocina.php';
echo '</div>';
echo '</div>';
?>