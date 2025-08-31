<?php

require_once 'BaseController.php';

require_once dirname(__DIR__, 1) . '/helpers/ImpresoraHelper.php';

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
    public function imprimirComanda() {
        Session::init();
        Session::checkRole(['Administrador', 'Mesero', 'Cajero']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $idMesa = isset($input['id_mesa']) ? (int)$input['id_mesa'] : 0;
        $tipo = isset($input['tipo']) ? $input['tipo'] : 'cocina';
        if (!$idMesa) {
            echo json_encode(['success' => false, 'error' => 'Mesa no especificada']);
            exit;
        }
        // Obtener detalles de la comanda
        require_once dirname(__DIR__, 1) . '/models/VentaModel.php';
        $ventaModel = new VentaModel();
        $comanda = [];
        if ($tipo === 'cocina') {
            $comandas = $ventaModel->getVentasPendientesCocina();
        } else {
            $comandas = $ventaModel->getVentasPendientesBarra();
        }
        foreach ($comandas as $c) {
            if ($c['ID_Mesa'] == $idMesa) {
                $comanda[] = $c;
            }
        }
        if (empty($comanda)) {
            echo json_encode(['success' => false, 'error' => 'No hay productos en la comanda']);
            exit;
        }
        // Formato especial para barra
        $contenido = "\n";
        if ($tipo === 'barra') {
            $contenido .= "====== COMANDA BARRA ======\n";
        } else {
            $contenido .= "====== COMANDA COCINA ======\n";
        }
        $contenido .= "Mesa: " . $comanda[0]['Numero_Mesa'] . "\n";
        $contenido .= "Hora: " . $comanda[0]['Fecha_Hora'] . "\n";
        $contenido .= "--------------------------\n";
        // Agrupar productos por tipo si es barra
        if ($tipo === 'barra') {
            $bebidas = [];
            $otros = [];
            foreach ($comanda as $item) {
                if (isset($item['Tipo_Producto']) && strtolower($item['Tipo_Producto']) === 'bebida') {
                    $bebidas[] = $item;
                } else {
                    $otros[] = $item;
                }
            }
            if (count($bebidas) > 0) {
                $contenido .= "BEBIDAS:\n";
                foreach ($bebidas as $item) {
                    $contenido .= "  - " . $item['Cantidad'] . "x " . $item['Nombre_Producto'] . "\n";
                }
            }
            if (count($otros) > 0) {
                $contenido .= "OTROS:\n";
                foreach ($otros as $item) {
                    $contenido .= "  - " . $item['Cantidad'] . "x " . $item['Nombre_Producto'] . "\n";
                }
            }
        } else {
            foreach ($comanda as $item) {
                $contenido .= $item['Cantidad'] . "x " . $item['Nombre_Producto'] . "\n";
            }
        }
        $contenido .= "--------------------------\n";
        $contenido .= "\n";
        // Imprimir usando el helper
        $clave = $tipo === 'cocina' ? 'impresora_cocina' : 'impresora_barra';
        $ok = ImpresoraHelper::imprimir($clave, $contenido);
        if ($ok) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo imprimir. Verifica la configuración de la impresora.']);
        }
        exit;
    }
}
