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
?>
    <div class="row g-4">
        <!-- Menú de productos por categorías (izquierda) -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Menú de Productos</h5></div>
                <div class="card-body">
                    <?php include '../../views/shared/menu_productos.php'; ?>
                </div>
            </div>
        </div>

        <!-- Columna central: Agregar productos arriba de productos en la mesa -->
        <div class="col-md-8">
            <!-- Agregar productos -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Agregar Productos</h5></div>
                <div class="card-body">
                    <ul id="lista-productos-agregados" class="list-group mb-3"></ul>
                    <button class="btn btn-success w-100" onclick="enviarProductosComanda()">Enviar pedido</button>
                </div>
            </div>
            <!-- Productos actuales de la mesa -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Productos en la Mesa</h5></div>
                <div class="card-body">
                    <h2>Detalle de Mesa #<?= htmlspecialchars($mesa['Numero_Mesa']) ?></h2>
                    <p>Capacidad: <?= htmlspecialchars($mesa['Capacidad']) ?> personas</p>
                    <p>Estado: <?= $mesa['Estado'] ? 'Ocupada' : 'Libre' ?></p>
                    <?php if ($comanda): ?>
                        <?php $detalles = $ventaModel->getSaleDetails($comanda['ID_Venta']); ?>
                        <?php if ($detalles && count($detalles) > 0): ?>
                            <ul class="list-group mb-3">
                                <?php $total = 0; foreach ($detalles as $detalle): $total += $detalle['Subtotal']; ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold me-2"><?= htmlspecialchars($detalle['Nombre_Producto']) ?></span>
                                            <span class="badge bg-secondary me-2">x<?= $detalle['Cantidad'] ?></span>
                                            <span class="badge bg-info me-2"><?= htmlspecialchars($detalle['Estado']) ?></span>
                                            <span class="text-success fw-bold">$<?= number_format($detalle['Subtotal'], 2) ?></span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <?php if ($detalle['Estado'] !== 'entregado'): ?>
                                                <button type="button" class="btn btn-sm btn-success" onclick="actualizarEstadoDetalle(<?= $detalle['ID_Detalle'] ?>, 'entregado')">Entregar</button>
                                            <?php endif; ?>
                                            <?php if ($detalle['Estado'] !== 'cancelado'): ?>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="actualizarEstadoDetalle(<?= $detalle['ID_Detalle'] ?>, 'cancelado')">Cancelar</button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="eliminarProductoComanda(<?= $detalle['ID_Detalle'] ?>)">Eliminar</button>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                                <li class="list-group-item fw-bold d-flex justify-content-between align-items-center bg-light">
                                    <span>Total</span><span class="text-primary">$<?= number_format($total, 2) ?></span>
                                </li>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-info">No hay productos en la comanda actual.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php
require_once '../../views/shared/footer.php';
?>
<script>
// Productos agregados temporalmente antes de enviar
let productosAgregados = [];

function agregarProductoMenu(id, nombre, precio) {
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
        lista.innerHTML += `<li class='list-group-item d-flex justify-content-between align-items-center'>
            <span>${p.nombre} <span class='badge bg-secondary ms-2'>x${p.cantidad}</span></span>
            <span>$${(p.precio * p.cantidad).toFixed(2)}</span>
            <button class='btn btn-sm btn-outline-danger ms-2' onclick='eliminarProductoAgregado(${idx})'>Eliminar</button>
        </li>`;
    });
    if (productosAgregados.length > 0) {
        lista.innerHTML += `<li class='list-group-item text-end fw-bold'>Total: $${total.toFixed(2)}</li>`;
    }
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
    const idMesa = <?php echo json_encode($idMesa); ?>;
    fetch('/restaurante/comanda/agregarProductos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_mesa=${idMesa}&productos=${encodeURIComponent(JSON.stringify(productosAgregados))}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pedido enviado correctamente.');
            productosAgregados = [];
            renderProductosAgregados();
            location.reload();
        } else {
            alert('Error al enviar pedido: ' + (data.error || ''));
        }
    })
    .catch(() => {
        alert('Error de comunicación con el servidor.');
    });
}

function actualizarEstadoDetalle(idDetalle, estado) {
    fetch('/restaurante/detalleventa/actualizarEstado', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_detalle=${idDetalle}&estado=${estado}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al actualizar estado');
        }
    })
    .catch(() => {
        alert('Error de comunicación con el servidor.');
    });
}

function eliminarProductoComanda(idDetalle) {
    let cantidad = prompt('¿Cuántos deseas eliminar? (deja vacío para eliminar todos)');
    if (cantidad === null) return;
    cantidad = cantidad.trim();
    let body = `id_detalle=${idDetalle}`;
    if (cantidad !== '') {
        body += `&cantidad=${encodeURIComponent(cantidad)}`;
    }
    fetch('/restaurante/detalleventa/eliminarProducto', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al eliminar producto');
        }
    })
    .catch(() => {
        alert('Error de comunicación con el servidor.');
    });
}

// Inicializa render
renderProductosAgregados();
</script>
