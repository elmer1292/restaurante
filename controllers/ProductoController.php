<?php

require_once 'BaseController.php';

class ProductoController extends BaseController {
    public function index() {
        // Aquí se manejará la lógica para la gestión de productos
        // Por ahora, simplemente cargamos la vista.
        $this->render('views/productos/index.php');
    }
}
