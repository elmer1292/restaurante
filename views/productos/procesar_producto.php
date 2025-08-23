<?php
require_once '../../config/Session.php';
require_once '../../models/ProductModel.php';

Session::init();
Session::checkRole(['Administrador']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productModel = new ProductModel();
    $mensaje = '';

    // Obtener y validar datos del formulario
    $nombre = trim($_POST['nombre']);
    $categoria = (int)$_POST['categoria'];
    $precioCosto = (float)$_POST['precio_costo'];
    $precioVenta = (float)$_POST['precio_venta'];
    $stock = (int)$_POST['stock'];

    // Validaciones básicas
    if (empty($nombre) || $precioCosto <= 0 || $precioVenta <= 0 || $stock < 0) {
        $_SESSION['mensaje'] = 'Por favor, complete todos los campos correctamente.';
        header('Location: index.php');
        exit();
    }

    // Verificar si es una actualización o nuevo registro
    if (!empty($_POST['id_producto'])) {
        // Actualización
        $idProducto = (int)$_POST['id_producto'];
        if ($productModel->updateProduct($idProducto, $nombre, $precioCosto, $precioVenta, $categoria, $stock)) {
            $mensaje = 'Producto actualizado exitosamente.';
        } else {
            $mensaje = 'Error al actualizar el producto.';
        }
    } else {
        // Nuevo registro
        if ($productModel->addProduct($nombre, $precioCosto, $precioVenta, $categoria, $stock)) {
            $mensaje = 'Producto agregado exitosamente.';
        } else {
            $mensaje = 'Error al agregar el producto.';
        }
    }

    $_SESSION['mensaje'] = $mensaje;
}

header('Location: index.php');
exit();