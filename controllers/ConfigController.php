<?php
require_once 'BaseController.php';
require_once dirname(__DIR__, 1) . '/models/ConfigModel.php';
require_once dirname(__DIR__, 1) . '/helpers/Csrf.php';

class ConfigController extends BaseController {
    public function index() {
        Session::init();
        $userRole = Session::getUserRole();
        if ($userRole !== 'Administrador') {
            http_response_code(403);
            echo 'Acceso Denegado. No tiene permisos para acceder a esta sección.';
            exit;
        }
        $configModel = new ConfigModel();
        $config = $configModel->getAll();
        $mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
        unset($_SESSION['mensaje']);
        $this->render('views/config/index.php', compact('config', 'mensaje'));
    }

    public function update() {
        Session::init();
        $userRole = Session::getUserRole();
        if ($userRole !== 'Administrador') {
            http_response_code(403);
            echo 'Acceso Denegado. No tiene permisos para acceder a esta sección.';
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['mensaje'] = 'Token CSRF inválido.';
                header('Location: ' . BASE_URL . 'configuracion');
                exit;
            }
            $configModel = new ConfigModel();
            $configModel->set('nombre_app', $_POST['nombre_app'] ?? '');
            $configModel->set('usar_impresora_cocina', isset($_POST['usar_impresora_cocina']) ? '1' : '0');
            $configModel->set('impresora_cocina', $_POST['impresora_cocina'] ?? '');
            $configModel->set('usar_impresora_barra', isset($_POST['usar_impresora_barra']) ? '1' : '0');
            $configModel->set('impresora_barra', $_POST['impresora_barra'] ?? '');
            $_SESSION['mensaje'] = 'Configuración actualizada correctamente.';
            header('Location: ' . BASE_URL . 'configuracion');
            exit;
        }
    }

    public function backup() {
        Session::init();
        $userRole = Session::getUserRole();
        if ($userRole !== 'Administrador') {
            http_response_code(403);
            echo 'Acceso Denegado. No tiene permisos para acceder a esta sección.';
            exit;
        }
        // Aquí puedes implementar el respaldo usando mysqldump y forzar descarga
        // Ejemplo básico:
        $dbHost = DB_HOST;
        $dbUser = DB_USER;
        $dbPass = DB_PASS;
        $dbName = DB_NAME;
        $filename = 'backup_' . date('Ymd_His') . '.sql';
        $command = "mysqldump -h$dbHost -u$dbUser -p$dbPass $dbName > $filename";
        system($command);
        // Descargar el archivo
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename=' . $filename);
        readfile($filename);
        unlink($filename);
        exit;
    }
}
