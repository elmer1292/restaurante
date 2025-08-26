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

// Inicialización de modelos y obtención de datos de la mesa
$ventaModel = new VentaModel();
$productModel = new ProductModel();
$mesaModel = new MesaModel();
$mesa = $mesaModel->getTableById($idMesa);
$comanda = $ventaModel->getVentaActivaByMesa($idMesa);
// ...estructura visual mejorada...
echo '<div class="row">';
    // Menú de productos por categorías (izquierda)
    echo '<div class="col-md-4">';
    include '../../views/shared/menu_productos.php';
    echo '</div>';

    // Sección resumen de comanda y productos agregados (derecha)
    echo '<div class="col-md-8">';
    echo '<h2>Detalle de Mesa #' . htmlspecialchars($mesa['Numero_Mesa']) . '</h2>';
    echo '<p>Capacidad: ' . htmlspecialchars($mesa['Capacidad']) . ' personas</p>';
    echo '<p>Estado: ' . ($mesa['Estado'] ? 'Ocupada' : 'Libre') . '</p>';

    echo '<div id="comanda-resumen">';
    // Aquí se mostrará el resumen de la comanda actual y los productos agregados vía JS
    echo '</div>';

    echo '<div id="productos-agregados" class="mt-4">';
    echo '<h4>Productos a agregar</h4>';
    echo '<ul id="lista-productos-agregados" class="list-group mb-3"></ul>';
    echo '<button class="btn btn-success" onclick="enviarProductosComanda()">Enviar pedido</button>';
    echo '</div>';

    echo '</div>';
echo '</div>';

require_once '../../views/shared/footer.php';
?>
<script>
// Productos agregados temporalmente antes de enviar
let productosAgregados = [];

function agregarProductoMenu(id, nombre, precio) {
    // Si ya existe, suma cantidad
    let prod = productosAgregados.find(p => p.id === id);
    if (prod) {
        prod.cantidad++;
    } else {
        productosAgregados.push({id, nombre, precio, cantidad: 1});
    }
    renderProductosAgregados();
}

function renderProductosAgregados() {
    const lista = document.getElementById('lista-productos-agregados');
    lista.innerHTML = '';
    let total = 0;
    productosAgregados.forEach((p, idx) => {
        total += p.precio * p.cantidad;
        lista.innerHTML += `<li class="list-group-item d-flex justify-content-between align-items-center">
            <span>${p.nombre} x${p.cantidad}</span>
            <span>$${(p.precio * p.cantidad).toFixed(2)}</span>
            <button class="btn btn-sm btn-danger ms-2" onclick="eliminarProductoAgregado(${idx})">Eliminar</button>
        </li>`;
    });
    lista.innerHTML += `<li class="list-group-item fw-bold d-flex justify-content-between align-items-center">
        <span>Total</span><span>$${total.toFixed(2)}</span>
    </li>`;
}

function eliminarProductoAgregado(idx) {
    productosAgregados.splice(idx, 1);
    renderProductosAgregados();
}

function enviarProductosComanda() {
    if (productosAgregados.length === 0) {
        alert('Agrega al menos un producto.');
        return;
    }
    // Aquí iría el AJAX para enviar los productos al backend
    // Por ahora solo resetea la lista
    alert('Pedido enviado (simulado).');
    productosAgregados = [];
    renderProductosAgregados();
}
</script>
?>
