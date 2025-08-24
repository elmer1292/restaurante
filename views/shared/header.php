<?php
require_once 'config/Session.php';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestBar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
