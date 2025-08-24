<?php
require_once 'config/Session.php';
require_once '../../models/MesaModel.php';

Session::init();
Session::checkRole(['Administrador', 'Mesero']);

$mesaModel = new MesaModel();
$mensaje = '';

// Manejar cambio de estado (disponible para Meseros y Administradores)
if (isset($_POST['action']) && $_POST['action'] === 'toggle_estado') {
    $idMesa = (int)$_POST['id_mesa'];
    $estado = (int)$_POST['estado'];

    if ($mesaModel->updateTableStatus($idMesa, $estado)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la mesa']);
    }
    exit();
}

// Las siguientes operaciones solo están permitidas para Administradores
if (Session::get('user_role') !== 'Administrador') {
    $_SESSION['mensaje'] = 'No tiene permisos para realizar esta operación.';
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numeroMesa = (int)$_POST['numero_mesa'];
    $capacidad = (int)$_POST['capacidad'];

    // Validaciones básicas
    if ($numeroMesa <= 0 || $capacidad <= 0) {
        $_SESSION['mensaje'] = 'Por favor, ingrese valores válidos para el número de mesa y capacidad.';
        header('Location: index.php');
        exit();
    }

    // Verificar si es una actualización o nuevo registro
    if (!empty($_POST['id_mesa'])) {
        // Actualización
        $idMesa = (int)$_POST['id_mesa'];
        if ($mesaModel->updateTable($idMesa, $numeroMesa, $capacidad)) {
            $mensaje = 'Mesa actualizada exitosamente.';
        } else {
            $mensaje = 'Error al actualizar la mesa.';
        }
    } else {
        // Nueva mesa
        if ($mesaModel->addTable($numeroMesa, $capacidad)) {
            $mensaje = 'Mesa agregada exitosamente.';
        } else {
            $mensaje = 'Error al agregar la mesa.';
        }
    }

    $_SESSION['mensaje'] = $mensaje;
}

header('Location: index.php');
exit();