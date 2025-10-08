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
        // Unificar barra y cocina por mesa
        $comandasUnificadas = [];
        foreach ($comandasBarra as $item) {
            $idMesa = $item['ID_Mesa'];
            if (!isset($comandasUnificadas[$idMesa])) {
                $comandasUnificadas[$idMesa] = [
                    'numero_mesa' => $item['Numero_Mesa'],
                    'fecha_hora' => $item['Fecha_Hora'],
                    'items' => []
                ];
            }
            $item['Categoria'] = 1; // Forzar barra
            $comandasUnificadas[$idMesa]['items'][] = $item;
        }
        foreach ($comandasCocina as $item) {
            $idMesa = $item['ID_Mesa'];
            if (!isset($comandasUnificadas[$idMesa])) {
                $comandasUnificadas[$idMesa] = [
                    'numero_mesa' => $item['Numero_Mesa'],
                    'fecha_hora' => $item['Fecha_Hora'],
                    'items' => []
                ];
            }
            $item['Categoria'] = isset($item['Categoria']) ? $item['Categoria'] : 0; // Forzar cocina
            $comandasUnificadas[$idMesa]['items'][] = $item;
        }
        // Ordenar items por fecha_hora si lo deseas
        foreach ($comandasUnificadas as &$comanda) {
            usort($comanda['items'], function($a, $b) {
                return strtotime($a['Fecha_Hora']) <=> strtotime($b['Fecha_Hora']);
            });
        }
        unset($comanda);
        $this->render('views/comandas/index.php', compact('comandasUnificadas', 'mesaModel'));
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
            // No es error, solo no hay productos de este tipo para imprimir
            echo json_encode(['success' => true]);
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
        //guarda el contenido de $contenido en un archivo .txt en la carpeta tickets_cocina o tickets_barra en la raiz segun sea el caso
        /*$rutaArchivo = $tipo === 'cocina' ? 'tickets_cocina/comanda_' . $idMesa . '.txt' : 'tickets_barra/comanda_' . $idMesa . '.txt';
        file_put_contents($rutaArchivo, $contenido); */
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
