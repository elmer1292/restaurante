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
            // Cargar mapa de categorías desde BD para decidir is_food dinámicamente
            $categories = $productModel->getAllCategories();
            $catMap = [];
            foreach ($categories as $c) {
                if (isset($c['Nombre_Categoria'])) {
                    $catMap[strtolower(trim($c['Nombre_Categoria']))] = isset($c['is_food']) ? (int)$c['is_food'] : 1;
                }
            }

            // Build separate lists for barra and cocina, normalizing fields
            $comandaBarra = [];
            $comandaCocina = [];
            foreach ($productosNuevos as $p) {
                $match = null;
                // Preferir id si llega
                if (isset($p['id']) && intval($p['id']) > 0 && isset($byId[intval($p['id'])])) {
                    $match = $byId[intval($p['id'])];
                } elseif (isset($p['Nombre_Producto']) && isset($byName[strtolower(trim($p['Nombre_Producto']))])) {
                    $match = $byName[strtolower(trim($p['Nombre_Producto']))];
                } elseif (isset($p['nombre']) && isset($byName[strtolower(trim($p['nombre']))])) {
                    $match = $byName[strtolower(trim($p['nombre']))];
                }

                // Determine is_food: prefer product's DB value, then client flag, then category map
                $isFood = null;
                if ($match && isset($match['is_food'])) {
                    $isFood = ($match['is_food'] == 1 || $match['is_food'] === '1') ? 1 : 0;
                } elseif (isset($p['is_food']) || isset($p['isFood'])) {
                    $raw = isset($p['is_food']) ? $p['is_food'] : $p['isFood'];
                    $isFood = ($raw == 1 || $raw === '1' || $raw === true || $raw === 'true') ? 1 : 0;
                } elseif (isset($p['categoria'])) {
                    $catLower = strtolower(trim($p['categoria']));
                    if (isset($catMap[$catLower])) $isFood = $catMap[$catLower];
                    else $isFood = null;
                }

                // Normalize fields
                $nombre = '';
                if ($match && isset($match['Nombre_Producto'])) $nombre = $match['Nombre_Producto'];
                elseif (isset($p['Nombre_Producto'])) $nombre = $p['Nombre_Producto'];
                elseif (isset($p['nombre'])) $nombre = $p['nombre'];
                elseif (isset($p['Nombre'])) $nombre = $p['Nombre'];

                $cantidad = 1;
                if (isset($p['Cantidad'])) $cantidad = (int)$p['Cantidad'];
                elseif (isset($p['cantidad'])) $cantidad = (int)$p['cantidad'];
                elseif (isset($p['qty'])) $cantidad = (int)$p['qty'];

                $preparacion = '';
                if (isset($p['preparacion'])) $preparacion = $p['preparacion'];
                elseif (isset($p['Preparacion'])) $preparacion = $p['Preparacion'];

                $entry = [
                    'ID_Producto' => $match['ID_Producto'] ?? (isset($p['id']) ? (int)$p['id'] : null),
                    'Nombre_Producto' => $nombre,
                    'Cantidad' => $cantidad,
                    'preparacion' => $preparacion,
                    'Tipo_Producto' => $match['Tipo_Producto'] ?? null,
                    'original' => $p
                ];

                if ($isFood === 0) {
                    $comandaBarra[] = $entry;
                } elseif ($isFood === 1) {
                    $comandaCocina[] = $entry;
                } else {
                    // unknown -> default to cocina
                    $comandaCocina[] = $entry;
                }
            }

            // If both lists empty, nothing to print
            if (empty($comandaBarra) && empty($comandaCocina)) {
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
            if (!empty($comandaBarra)) { $comandaBarra[0]['Numero_Mesa'] = $numeroMesa; $comandaBarra[0]['Fecha_Hora'] = $fechaHora; }
            if (!empty($comandaCocina)) { $comandaCocina[0]['Numero_Mesa'] = $numeroMesa; $comandaCocina[0]['Fecha_Hora'] = $fechaHora; }
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
        // Ahora construiremos y guardaremos/mandaremos a imprimir separadamente para barra y cocina según corresponda.
        $printsDir = __DIR__ . '/../tools/prints';
        if (!is_dir($printsDir)) {@mkdir($printsDir, 0755, true);} 
        $results = [];

        $doBuildAndPrint = function($tipoLocal, $lista) use ($idMesa, $printsDir, $numeroMesa, $fechaHora) {
            if (empty($lista)) return ['ok' => true, 'file' => null, 'tipo' => $tipoLocal];
            $contenido = "\n";
            $contenido .= $tipoLocal === 'barra' ? "====== COMANDA BARRA ======\n" : "====== COMANDA COCINA ======\n";
            $contenido .= "Usuario: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Desconocido') . "\n";
            $contenido .= "Mesa: " . ($lista[0]['Numero_Mesa'] ?? $idMesa) . "\n";
            $contenido .= "Hora: " . ($lista[0]['Fecha_Hora'] ?? date('Y-m-d H:i:s')) . "\n";
            $contenido .= "--------------------------\n";
            if ($tipoLocal === 'barra') {
                $bebidas = [];
                $otros = [];
                foreach ($lista as $item) {
                    if (isset($item['Tipo_Producto']) && strtolower($item['Tipo_Producto']) === 'bebida') $bebidas[] = $item;
                    else $otros[] = $item;
                }
                if (count($bebidas) > 0) {
                    $contenido .= "BEBIDAS:\n";
                    foreach ($bebidas as $item) {
                        $contenido .= "  - " . ($item['Cantidad'] ?? 1) . "x " . ($item['Nombre_Producto'] ?? '') . "\n";
                        $prep = trim((string)($item['preparacion'] ?? ''));
                        if ($prep !== '') $contenido .= "     > " . $prep . "\n";
                    }
                }
                if (count($otros) > 0) {
                    foreach ($otros as $item) {
                        $contenido .= "  - " . ($item['Cantidad'] ?? 1) . "x " . ($item['Nombre_Producto'] ?? '') . "\n";
                        $prep = trim((string)($item['preparacion'] ?? ''));
                        if ($prep !== '') $contenido .= "     > " . $prep . "\n";
                    }
                }
            } else {
                foreach ($lista as $item) {
                    $contenido .= ($item['Cantidad'] ?? 1) . "x " . strtoupper(($item['Nombre_Producto'] ?? '')) . "\n";
                    $prep = trim((string)($item['preparacion'] ?? ''));
                    if ($prep !== '') $contenido .= "   > " . $prep . "\n";
                }
            }
            $contenido .= "--------------------------\n\n";
            $timestamp = date('Ymd_His');
            $fileName = $printsDir . '/comanda_' . $tipoLocal . '_mesa_' . $idMesa . '_' . $timestamp . '.txt';
            @file_put_contents($fileName, $contenido);
            $clave = $tipoLocal === 'cocina' ? 'impresora_cocina' : 'impresora_barra';
            $ok = ImpresoraHelper::imprimir($clave, $contenido);
            return ['ok' => $ok, 'file' => $fileName, 'tipo' => $tipoLocal];
        };

        // Decide what to print based on requested tipo
        if ($tipo === 'ambos') {
            $resBarra = $doBuildAndPrint('barra', $comandaBarra);
            $resCocina = $doBuildAndPrint('cocina', $comandaCocina);
            $results[] = $resBarra;
            $results[] = $resCocina;
        } elseif ($tipo === 'barra') {
            $results[] = $doBuildAndPrint('barra', $comandaBarra);
        } else {
            $results[] = $doBuildAndPrint('cocina', $comandaCocina);
        }

        // Aggregate result
        $allOk = true;
        foreach ($results as $r) if (!$r['ok']) $allOk = false;
        echo json_encode(['success' => $allOk, 'results' => $results]);
        exit;
    }
}
