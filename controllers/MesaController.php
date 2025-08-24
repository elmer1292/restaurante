<?php

require_once 'BaseController.php';

class MesaController extends BaseController {
    public function index() {
        // Aquí se manejará la lógica para la gestión de mesas
        // Por ahora, simplemente cargamos la vista.
        $this->render('views/mesas/index.php');
    }
}
