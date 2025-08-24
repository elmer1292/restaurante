<?php

require_once 'BaseController.php';

class DashboardController extends BaseController {
    public function index() {
        // Aquí puedes cargar datos necesarios para el dashboard
        $this->render('views/dashboard.php');
    }
}
