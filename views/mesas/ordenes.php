<?php
require_once '../../config/Session.php';
require_once '../../models/VentaModel.php';
require_once '../../models/ProductModel.php';
require_once '../../models/MesaModel.php';
require_once '../../views/shared/header.php';
require_once '../../views/shared/sidebar.php';
Session::init();
Session::checkRole(['Administrador', 'Mesero', 'Cajero']);

$idMesa = $_GET['id_mesa'] ?? null;
if (!$idMesa) {
    echo '<div class="alert alert-danger">Mesa no especificada.</div>';
    exit;
}

$ventaModel = new VentaModel();
$productModel = new ProductModel();
$mesaModel = new MesaModel();
$mesa = $mesaModel->getTableById($idMesa);
$comanda = $ventaModel->getVentaActivaByMesa($idMesa);

echo '<h2>Detalle de Mesa #' . htmlspecialchars($mesa['Numero_Mesa']) . '</h2>';
echo '<p>Capacidad: ' . htmlspecialchars($mesa['Capacidad']) . ' personas</p>';
echo '<p>Estado: ' . ($mesa['Estado'] ? 'Ocupada' : 'Libre') . '</p>';

if ($comanda) {
    echo '<h4>Productos en la comanda</h4>';
    $detalles = $ventaModel->getSaleDetails($comanda['ID_Venta']);
    if ($detalles) {
        echo '<ul>';
        foreach ($detalles as $detalle) {
            echo '<li>' . htmlspecialchars($detalle['Nombre_Producto']) . ' x' . $detalle['Cantidad'] . ' - $' . number_format($detalle['Precio_Venta'], 2);
            if ($comanda['Estado'] == 'Pendiente') {
                echo ' <form method="post" style="display:inline;"><input type="hidden" name="eliminar_producto" value="' . $detalle['ID_Producto'] . '"><button type="submit" class="btn btn-sm btn-danger">Eliminar</button></form>';
            }
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="alert alert-info">No hay productos en la comanda.</div>';
    }

    // Agregar productos si la comanda está abierta
    if ($comanda['Estado'] == 'Pendiente') {
        $productos = $productModel->getAllProducts();
        echo '<h4>Agregar producto</h4>';
        echo '<form method="post">';
        echo '<select name="id_producto">';
        foreach ($productos as $producto) {
            echo '<option value="' . $producto['ID_Producto'] . '">' . htmlspecialchars($producto['Nombre_Producto']) . '</option>';
        }
        echo '</select>';
        echo '<input type="number" name="cantidad" min="1" value="1">';
        echo '<button type="submit" name="agregar_producto" class="btn btn-success">Agregar</button>';
        echo '</form>';
    } else {
        echo '<div class="alert alert-info">La comanda está cerrada o en preparación. No se pueden agregar productos.</div>';
    }
} else {
    echo '<div class="alert alert-warning">La mesa está libre. Agrega un producto para crear una nueva comanda.</div>';
    $productos = $productModel->getAllProducts();
    echo '<form method="post">';
    echo '<select name="id_producto">';
    foreach ($productos as $producto) {
        echo '<option value="' . $producto['ID_Producto'] . '">' . htmlspecialchars($producto['Nombre_Producto']) . '</option>';
    }
    echo '</select>';
    echo '<input type="number" name="cantidad" min="1" value="1">';
    echo '<button type="submit" name="crear_comanda" class="btn btn-primary">Agregar y crear comanda</button>';
    echo '</form>';
}
require_once '../../views/shared/footer.php';
?>
