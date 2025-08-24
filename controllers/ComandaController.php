<?php

require_once 'BaseController.php';

class ComandaController extends BaseController {
    public function index() {
        // Aquí se manejará la lógica para la gestión de comandas
        // Por ahora, simplemente cargamos la vista.
        $this->render('views/comandas/index.php');
    }
}
