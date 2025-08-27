<?php

require_once 'BaseController.php';

class ComandaController extends BaseController {
    public function index() {
        Session::init();
        Session::checkRole(['Administrador', 'Mesero', 'Cajero']);
        $ventaModel = new VentaModel();
        $mesaModel = new MesaModel();
        $comandasCocina = $ventaModel->getVentasPendientesCocina();
        $comandasBarra = $ventaModel->getVentasPendientesBarra();
        // Agrupar comandas de barra por mesa
        $comandasPorMesa = [];
        foreach ($comandasBarra as $comanda) {
            $idMesa = $comanda['ID_Mesa'];
            if (!isset($comandasPorMesa[$idMesa])) {
                $comandasPorMesa[$idMesa] = [
                    'numero_mesa' => $comanda['Numero_Mesa'],
                    'fecha_hora' => $comanda['Fecha_Hora'],
                    'items' => []
                ];
            }
            $comandasPorMesa[$idMesa]['items'][] = $comanda;
        }
        $this->render('views/comandas/index.php', compact('comandasCocina', 'comandasPorMesa', 'mesaModel'));
    }
}
