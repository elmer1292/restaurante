<?php
require_once dirname(__DIR__, 2) . '/config/Session.php';
Session::init();
$userRole = Session::get('user_role');
$nombreCompleto = Session::get('nombre_completo');
$currentPage = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$currentPage = str_replace('restaurante/', '', $currentPage);
$baseUrl = '/restaurante';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../../helpers/Csrf.php'; ?>
    <meta name="csrf-token" content="<?= Csrf::getToken() ?>">
    <?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
    <script>window.csrfToken = '<?= Csrf::getToken() ?>';
    const BASE_URL = '<?= BASE_URL ?>';
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestBar</title>
    <link href="<?php echo BASE_URL; ?>assets/bootstrap/bootstrap-5.3.0-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/icons/bootstrap-icons-1.7.2/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <header class="navbar navbar-dark bg-dark px-3 mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h4 class="text-white mb-0"><?php 
                    require_once dirname(__DIR__, 2) . '/models/ConfigModel.php';
                    $configModel = new ConfigModel();
                    $nombreApp = $configModel->get('nombre_app');
                    echo htmlspecialchars($nombreApp ?: 'RestBar'); ?></h4>
            </div>
            <div class="text-end">
                <span class="fw-bold text-white"><?php echo htmlspecialchars($nombreCompleto ?? ''); ?></span><br>
                <span class="badge bg-secondary"> <?php echo htmlspecialchars($userRole ?? ''); ?> </span>
            </div>
        </div>
    </header>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
