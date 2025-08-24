<?php

require_once 'BaseController.php';

class VentaController extends BaseController {
    public function index() {
        // Aquí se manejará la lógica para la gestión de ventas
        // Por ahora, simplemente cargamos la vista.
        $this->render('views/ventas/index.php');
    }
}
