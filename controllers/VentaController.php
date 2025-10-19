<?php
require_once 'BaseController.php';


class VentaController extends BaseController {
    public function index() {
        $this->render('views/ventas/index.php');
    }

    public function registrarPago() {
        require_once __DIR__ . '/../config/Session.php';
        require_once __DIR__ . '/../models/VentaModel.php';
        require_once __DIR__ . '/../models/MovimientoModel.php';
        Session::init();
        Session::checkRole(['Administrador', 'Cajero']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }
        require_once __DIR__ . '/../helpers/Csrf.php';
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            echo json_encode(['success' => false, 'error' => 'CSRF token inválido']);
            exit;
        }
        $idVenta = isset($_POST['id_venta']) ? (int)$_POST['id_venta'] : 0;
        $metodoPago = isset($_POST['metodo_pago']) ? substr(trim($_POST['metodo_pago']), 0, 200) : '';
    $monto = isset($_POST['monto']) ? (float)$_POST['monto'] : 0;
    $incluirServicioFlag = isset($_POST['incluir_servicio']) ? ($_POST['incluir_servicio'] === '1' || $_POST['incluir_servicio'] === 'true' ? true : false) : true;
        // Parse payment methods server-side. Prefer structured JSON 'metodos_json' if present (safer), else fallback to comma-separated 'metodo_pago'.
        $totalPagado = 0.0;
        $hasEfectivo = false;
        $metodosStructured = [];
        if (!empty($_POST['metodos_json'])) {
            $raw = $_POST['metodos_json'];
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                foreach ($decoded as $entry) {
                    $mname = isset($entry['metodo']) ? trim($entry['metodo']) : '';
                    $montoEntry = isset($entry['monto']) ? floatval($entry['monto']) : 0.0;
                    $metodosStructured[] = ['metodo' => $mname, 'monto' => $montoEntry];
                    $totalPagado += $montoEntry;
                    if (stripos($mname, 'efectivo') !== false) $hasEfectivo = true;
                }
            }
        } else {
            // Fallback to legacy parsing
            $metodosArr = array_filter(array_map('trim', explode(',', $metodoPago)));
            foreach ($metodosArr as $m) {
                $parts = explode(':', $m);
                if (isset($parts[1])) {
                    $amt = floatval(trim($parts[1]));
                    $totalPagado += $amt;
                }
                if (stripos($m, 'efectivo') !== false) {
                    $hasEfectivo = true;
                }
            }
        }
        if (!$idVenta || !$metodoPago || $totalPagado <= 0) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            exit;
        }
    $ventaModel = new VentaModel();
    $movimientoModel = new MovimientoModel();
    require_once __DIR__ . '/../models/PagoModel.php';
    $pagoModel = new PagoModel();
    $conn = (new Database())->connect();
        try {
            // Asegurar que el campo Total en la tabla ventas esté actualizado con la suma de detalle_venta
            $ventaModel->actualizarTotal($idVenta);
            $stmt = $conn->prepare('SELECT Total, Servicio FROM ventas WHERE ID_Venta = ?');
            $stmt->execute([$idVenta]);
            $venta = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$venta) throw new Exception('Venta no encontrada');
            $total = (float)$venta['Total'];
            // Use Servicio persisted in DB (do not trust client-side value)
            $servicio = isset($venta['Servicio']) ? (float)$venta['Servicio'] : 0.0;
            // If the cashier chose NOT to include service, persist that change so prints/reports reflect it
            if (!$incluirServicioFlag && $servicio > 0) {
                try {
                    $stmtUpd = $conn->prepare('UPDATE ventas SET Servicio = 0 WHERE ID_Venta = ?');
                    $stmtUpd->execute([$idVenta]);
                    // reflect change locally for calculations
                    $servicio = 0.0;
                } catch (Exception $e) {
                    // Non-fatal: log and continue (we'll still calculate without charging service this operation)
                    error_log('No se pudo actualizar Servicio a 0 para venta ' . $idVenta . ': ' . $e->getMessage());
                }
            }
            // determinar si en esta operacion se cobrará el servicio (checkbox del cajero)
            $servicioCobradoEnEstaOperacion = $incluirServicioFlag ? $servicio : 0.0;
            $totalFactura = $total + $servicioCobradoEnEstaOperacion;
            // Validar con el total pagado extraido de metodo_pago
            if ($totalPagado > $totalFactura) {
                if ($hasEfectivo) {
                    $cambio = $totalPagado - $totalFactura;
                } else {
                    throw new Exception('El monto pagado excede el total de la venta');
                }
            }
            if ($totalPagado < $totalFactura) throw new Exception('El monto pagado es menor al total de la venta');
            // Registrar el pago y marcar como pagada (no sobrescribimos Servicio desde el cliente)
            $stmtUp = $conn->prepare('UPDATE ventas SET Metodo_Pago = ?, Estado = "Pagada" WHERE ID_Venta = ?');
            $stmtUp->execute([$metodoPago, $idVenta]);
            // Liberar la mesa
            $stmtMesa = $conn->prepare("UPDATE mesas SET Estado = 0 WHERE ID_Mesa = (SELECT ID_Mesa FROM ventas WHERE ID_Venta = ?)");
            $stmtMesa->execute([$idVenta]);

            // Registrar movimiento de caja
            $idUsuario = null;
            if (isset($_SESSION['user_id'])) {
                $idUsuario = $_SESSION['user_id'];
            } elseif (isset($_SESSION['user']['ID_usuario'])) {
                $idUsuario = $_SESSION['user']['ID_usuario'];
            }
            $descripcion = 'Pago de venta ID ' . $idVenta . ' (' . $metodoPago . ')';
            // Registrar el ingreso correspondiente al total final (incluye servicio). Si hay sobrepago, el cambio se maneja aparte.
            // Registrar ingreso por el monto efectivamente cobrado en esta operación (respeta incluir_servicio)
            $movimientoModel->registrarMovimiento('Ingreso', $totalFactura, $descripcion, $idUsuario, $idVenta);

            // Registrar cada metodo de pago en la tabla pagos
            try {
                if (!empty($metodosStructured)) {
                    foreach ($metodosStructured as $entry) {
                        $mname = $entry['metodo'] ?? '';
                        $montoEntry = floatval($entry['monto'] ?? 0);
                        if ($montoEntry > 0) {
                            $pagoModel->registrarPago($idVenta, $mname, $montoEntry, false);
                        }
                    }
                } else {
                    // Legacy: parse metodoPago string like "Efectivo:100,Card:50"
                    $metodosArr = array_filter(array_map('trim', explode(',', $metodoPago)));
                    foreach ($metodosArr as $m) {
                        $parts = explode(':', $m);
                        $mname = trim($parts[0]);
                        $montoEntry = isset($parts[1]) ? floatval(trim($parts[1])) : 0.0;
                        if ($montoEntry > 0) {
                            $pagoModel->registrarPago($idVenta, $mname, $montoEntry, false);
                        }
                    }
                }
            } catch (Exception $e) {
                // Non-fatal: log and continue
                error_log('Error registrando pagos: ' . $e->getMessage());
            }

            // Llamar a imprimir_ticket.php en segundo plano
            // Forzar la ruta de php.exe para evitar problemas con PHP_BINARY bajo Apache
            $phpPath = 'C:/xampp/php/php.exe';
            $scriptPath = realpath(__DIR__ . '/../imprimir_ticket.php');
            if ($scriptPath) {
                $cmd = "$phpPath \"$scriptPath\" id=$idVenta > NUL 2>&1 &";
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // Windows: usar start para background
                    $cmd = 'start /B ' . $cmd;
                }
                exec($cmd);
            }

            // Si hay cambio y se pagó en efectivo, registrar cambio como pago con Es_Cambio=1 (monto positivo siendo entregado como cambio)
            if (isset($cambio) && $cambio > 0 && $hasEfectivo) {
                try {
                    $pagoModel->registrarPago($idVenta, 'Cambio', $cambio, true);
                } catch (Exception $e) {
                    error_log('Error al registrar cambio: ' . $e->getMessage());
                }
            }

            $response = ['success' => true];
            if (isset($cambio)) $response['cambio'] = $cambio;
            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    // ticket
    public function ticket() {
        // El método ticket solo debe preparar datos o delegar a la vista según el router
        // Si usas un sistema de vistas, deberías hacer algo como:
        $this->render('views/ventas/ticket.php');
    }

    public function ventas_dia() {
        // Mostrar ventas filtradas por fecha (por defecto: hoy)
        require_once __DIR__ . '/../models/VentaModel.php';
        require_once __DIR__ . '/../models/ConfigModel.php';
        $fecha = isset($_GET['fecha']) ? trim($_GET['fecha']) : date('Y-m-d');
        $ventaModel = new VentaModel();
        $conn = (new Database())->connect();
        try {
            $stmt = $conn->prepare('SELECT v.*, m.Numero_Mesa FROM ventas v LEFT JOIN mesas m ON v.ID_Mesa = m.ID_Mesa WHERE DATE(v.Fecha_Hora) = ? ORDER BY v.Fecha_Hora DESC');
            $stmt->execute([$fecha]);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $ventas = [];
        }
        $this->render('views/ventas/ventas_dia.php', compact('ventas', 'fecha'));
    }
}
