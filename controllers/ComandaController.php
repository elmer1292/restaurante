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
    $productosNuevos = isset($input['productos']) && is_array($input['productos']) ? $input['productos'] : null;
        if (!$idMesa) {
            echo json_encode(['success' => false, 'error' => 'Mesa no especificada']);
            exit;
        }
        // Si se reciben productos nuevos, filtrar por tipo (barra/cocina) según is_food del producto en la BD
        if ($productosNuevos) {
            // Cargar productos desde BD para validar is_food
            require_once dirname(__DIR__, 1) . '/models/ProductModel.php';
            $productModel = new ProductModel();
            $allProducts = $productModel->getAllProducts();
            $byId = [];
            $byName = [];
            foreach ($allProducts as $prod) {
                if (isset($prod['ID_Producto'])) $byId[(int)$prod['ID_Producto']] = $prod;
                if (isset($prod['Nombre_Producto'])) $byName[strtolower(trim($prod['Nombre_Producto']))] = $prod;
            }

            $comanda = [];
            foreach ($productosNuevos as $p) {
                $match = null;
                // Preferir id si llega
                if (isset($p['id']) && intval($p['id']) > 0 && isset($byId[intval($p['id'])])) {
                    $match = $byId[intval($p['id'])];
                } elseif (isset($p['Nombre_Producto']) && isset($byName[strtolower(trim($p['Nombre_Producto']))])) {
                    $match = $byName[strtolower(trim($p['Nombre_Producto']))];
                }
                // Determinar is_food: preferir valor de BD si hay match, sino fallback a p['categoria'] o p['tipo']
                $isFood = null;
                if ($match && isset($match['is_food'])) {
                    $isFood = ($match['is_food'] == 1 || $match['is_food'] === '1') ? 1 : 0;
                } elseif (isset($p['categoria'])) {
                    $cat = trim($p['categoria']);
                    $catLower = strtolower($cat);
                    $barCats = ['bebidas','licores','cockteles','cervezas'];
                    $isFood = in_array($catLower, $barCats) ? 0 : 1;
                }
                // Decide inclusion according to requested tipo
                if ($tipo === 'barra') {
                    // want barra: include when isFood===0 or fallback category indicates barra
                    if ($isFood === 0) $comanda[] = array_merge($p, ['Tipo_Producto' => ($match['Tipo_Producto'] ?? 'Bebida')]);
                } else {
                    if ($isFood === 1 || $isFood === null) {
                        // include cocina if is_food==1 or unknown (default to cocina)
                        $comanda[] = array_merge($p, ['Tipo_Producto' => ($match['Tipo_Producto'] ?? 'Comida')]);
                    }
                }
            }

            // Si no hay productos válidos, responder éxito sin imprimir
            if (empty($comanda)) {
                echo json_encode(['success' => true]);
                exit;
            }

            // Obtener datos de la mesa y la venta para imprimir encabezado
            require_once dirname(__DIR__, 1) . '/models/MesaModel.php';
            require_once dirname(__DIR__, 1) . '/models/VentaModel.php';
            $mesaModel = new MesaModel();
            $ventaModel = new VentaModel();
            $mesa = $mesaModel->getTableById($idMesa);
            $venta = $ventaModel->getVentaActivaByMesa($idMesa);
            $numeroMesa = $mesa && isset($mesa['Numero_Mesa']) ? $mesa['Numero_Mesa'] : $idMesa;
            $fechaHora = $venta && isset($venta['Fecha_Hora']) ? $venta['Fecha_Hora'] : date('Y-m-d H:i:s');
            // Agregar estos datos al primer producto para el formato de impresión
            $comanda[0]['Numero_Mesa'] = $numeroMesa;
            $comanda[0]['Fecha_Hora'] = $fechaHora;
        } else {
            // Obtener detalles de la comanda como antes
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
        }
        // Formato especial para barra
        $contenido = "\n";
        if ($tipo === 'barra') {
            $contenido .= "====== COMANDA BARRA ======\n";
        } else {
            $contenido .= "====== COMANDA COCINA ======\n";
        }
        $contenido .= "Usuario: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Desconocido') . "\n";
        // Si prefieres el nombre completo, usa la línea siguiente:
        // $contenido .= "Usuario: " . (isset($_SESSION['nombre_completo']) ? $_SESSION['nombre_completo'] : 'Desconocido') . "\n";
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
                    // Preparación (si existe)
                    $prep = '';
                    if (isset($item['preparacion'])) $prep = $item['preparacion'];
                    elseif (isset($item['Preparacion'])) $prep = $item['Preparacion'];
                    $prep = trim((string)$prep);
                    if ($prep !== '') $contenido .= "     > " . $prep . "\n";
                }
            }
            if (count($otros) > 0) {
                foreach ($otros as $item) {
                    $contenido .= "  - " . $item['Cantidad'] . "x " . $item['Nombre_Producto'] . "\n";
                    $prep = '';
                    if (isset($item['preparacion'])) $prep = $item['preparacion'];
                    elseif (isset($item['Preparacion'])) $prep = $item['Preparacion'];
                    $prep = trim((string)$prep);
                    if ($prep !== '') $contenido .= "     > " . $prep . "\n";
                }
            }
        } else 
        {
            foreach ($comanda as $item) {
                $linea = $item['Cantidad'] . "x " . strtoupper($item['Nombre_Producto']) . "\n";
                $contenido .= $linea;
                $prep = '';
                if (isset($item['preparacion'])) $prep = $item['preparacion'];
                elseif (isset($item['Preparacion'])) $prep = $item['Preparacion'];
                $prep = trim((string)$prep);
                if ($prep !== '') {
                    $contenido .= "   > " . $prep . "\n";
                }
            }
        }
        $contenido .= "--------------------------\n";
        $contenido .= "\n";
        //guarda el contenido de $contenido en un archivo .txt en la carpeta tickets_cocina o tickets_barra en la raiz segun sea el caso
        /*$rutaArchivo = $tipo === 'cocina' ? 'tickets_cocina/comanda_' . $idMesa . '.txt' : 'tickets_barra/comanda_' . $idMesa . '.txt';
        file_put_contents($rutaArchivo, $contenido); */
        // Guardar una copia del contenido en tools/prints para depuración (no reemplaza la impresión)
        /*$printsDir = __DIR__ . '/../tools/prints';
        if (!is_dir($printsDir)) {
            @mkdir($printsDir, 0755, true);
        }
        $fileName = $printsDir . '/comanda_' . $tipo . '_mesa_' . $idMesa . '_' . date('Ymd_His') . '.txt';
        @file_put_contents($fileName, $contenido);*/

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
