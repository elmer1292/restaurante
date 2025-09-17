<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/MovimientoModel.php';
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../helpers/Csrf.php';

class CajaController extends BaseController {
    public function apertura() {
        Session::init();
        Session::checkRole(['Administrador', 'Cajero']);
        $mensaje = '';
        $errores = [];
        $csrf_token = Csrf::generateToken();
        $movimientoModel = new MovimientoModel();
        $cajaAbierta = $movimientoModel->cajaAbierta();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                $errores[] = 'CSRF token inválido';
            }
            $monto = isset($_POST['monto']) ? (float)$_POST['monto'] : 0;
            if ($monto <= 0) {
                $errores[] = 'El monto debe ser mayor a cero';
            }
            if (!$errores && !$cajaAbierta) {
                $idUsuario = $_SESSION['user_id'] ?? ($_SESSION['user']['ID_usuario'] ?? null);
                $ok = $movimientoModel->registrarMovimiento('Apertura', $monto, 'Apertura de caja', $idUsuario);
                if ($ok) {
                    $mensaje = 'Caja abierta correctamente';
                    $cajaAbierta = true;
                } else {
                    $errores[] = 'Error al registrar apertura';
                }
            }
        }
        $this->render('views/caja/apertura.php', compact('mensaje', 'errores', 'csrf_token', 'cajaAbierta'));
    }

    public function cierre() {
        Session::init();
        Session::checkRole(['Administrador', 'Cajero']);
        $mensaje = '';
        $errores = [];
        $csrf_token = Csrf::generateToken();
        $movimientoModel = new MovimientoModel();
        $cajaAbierta = $movimientoModel->cajaAbierta();
        $saldo = $movimientoModel->obtenerSaldoCajaAbierta();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                $errores[] = 'CSRF token inválido';
            }
            if (!$cajaAbierta) {
                $errores[] = 'No hay caja abierta para cerrar';
            }
            if (!$errores) {
                $idUsuario = $_SESSION['user_id'] ?? ($_SESSION['user']['ID_usuario'] ?? null);
                $ok = $movimientoModel->registrarMovimiento('Cierre', $saldo, 'Cierre de caja', $idUsuario);
                if ($ok) {
                    $mensaje = 'Caja cerrada correctamente';
                    $cajaAbierta = false;
                } else {
                    $errores[] = 'Error al registrar cierre';
                }
            }
        }
        $this->render('views/caja/cierre.php', compact('mensaje', 'errores', 'csrf_token', 'cajaAbierta', 'saldo'));
    }
}
