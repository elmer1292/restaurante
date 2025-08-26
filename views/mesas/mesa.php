<?php
// Vista principal de mesa
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
$mostrarCrearComanda = (!$comanda && $mesa && $mesa['Estado'] == 0);
?>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><h5 class="mb-0">Menú de Productos</h5></div>
            <div class="card-body">
                <?php if ($mostrarCrearComanda) { ?>
                    <div class="alert alert-info">La mesa está libre. Debe crear una comanda para poder agregar productos.</div>
                    <form id="formCrearComanda" autocomplete="off">
                        <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($idMesa); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::getToken()); ?>">
                        <button type="submit" class="btn btn-primary w-100">Crear Comanda</button>
                    </form>
                <?php } else { ?>
                    <?php include '../../views/shared/menu_productos.php'; ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><h5 class="mb-0">Agregar Productos</h5></div>
            <div class="card-body">
                <ul id="lista-productos-agregados" class="list-group mb-3"></ul>
                <?php if ($comanda): ?>
                <button class="btn btn-success w-100" onclick="enviarProductosComanda()">Enviar pedido</button>
                <?php else: ?>
                <button class="btn btn-success w-100" disabled>Enviar pedido</button>
                <?php endif; ?>
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white"><h5 class="mb-0">Productos en la Mesa</h5></div>
            <div class="card-body">
                <h2>Detalle de Mesa #<?php echo htmlspecialchars($mesa['Numero_Mesa']); ?></h2>
                <p>Capacidad: <?php echo htmlspecialchars($mesa['Capacidad']); ?> personas</p>
                <?php if ($comanda) {
                    $detalles = $ventaModel->getSaleDetails($comanda['ID_Venta']);
                    if (!$detalles || count($detalles) === 0) { ?>
                        <form id="formLiberarMesa" method="post" style="margin-bottom: 1em;">
                            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($idMesa); ?>">
                            <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($comanda['ID_Venta']); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::getToken()); ?>">
                            <button type="submit" class="btn btn-danger w-100">Liberar Mesa</button>
                        </form>
                    <?php } else { ?>
                        <ul class="list-group mb-3">
                            <?php $total = 0;
                            foreach ($detalles as $detalle) {
                                $total += $detalle['Subtotal']; ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold me-2"><?php echo htmlspecialchars($detalle['Nombre_Producto']); ?></span>
                                        <span class="badge bg-secondary me-2">x<?php echo $detalle['Cantidad']; ?></span>
                                        <span class="text-success fw-bold">$<?php echo number_format($detalle['Subtotal'], 2); ?></span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-warning" onclick="eliminarProductoComanda(<?php echo $detalle['ID_Detalle']; ?>)">Eliminar</button>
                                    </div>
                                </li>
                            <?php } ?>
                            <li class="list-group-item fw-bold d-flex justify-content-between align-items-center bg-light">
                                <span>Total</span><span class="text-primary">$<?php echo number_format($total, 2); ?></span>
                            </li>
                        </ul>
                    <?php }
                } ?>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../views/shared/footer.php'; ?>
<script>
document.getElementById('formCrearComanda')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const idMesa = this.id_mesa.value;
    fetch('/restaurante/comanda/crear', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'same-origin',
        body: new URLSearchParams({
            id_mesa: idMesa,
            productos: JSON.stringify([]),
            csrf_token: this.csrf_token.value
        })
    })
    .then(response => response.json())
    .then(data => {
        window.location.reload();
    });
});
let productosAgregados = [];
function renderListaProductosAgregados() {
    const lista = document.getElementById('lista-productos-agregados');
    lista.innerHTML = '';
    for (let i = 0; i < productosAgregados.length; i++) {
        const prod = productosAgregados[i];
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = '<div><span class="fw-bold">' + prod.nombre + '</span> <span class="badge bg-secondary ms-2">x' + prod.cantidad + '</span></div>' +
            '<button class="btn btn-sm btn-outline-danger ms-2" onclick="eliminarProductoAgregado(' + i + ')">Eliminar</button>';
        lista.appendChild(li);
    }
}
function agregarProducto(nombre, cantidad, id) {
    productosAgregados.push({ nombre, cantidad, id });
    renderListaProductosAgregados();
}
function eliminarProductoAgregado(idx) {
    productosAgregados.splice(idx, 1);
    renderListaProductosAgregados();
}
function enviarProductosComanda() {
    if (productosAgregados.length === 0) return;
    let idMesaInput = document.querySelector('input[name="id_mesa"]');
    let csrfTokenInput = document.querySelector('input[name="csrf_token"]');
    // Si los inputs no existen, usar variables PHP
    let idMesa = idMesaInput ? idMesaInput.value : <?php echo json_encode($idMesa); ?>;
    let csrfToken = csrfTokenInput ? csrfTokenInput.value : <?php echo json_encode(Csrf::getToken()); ?>;
    fetch('/restaurante/comanda/agregarProductos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'same-origin',
        body: new URLSearchParams({
            id_mesa: idMesa,
            productos: JSON.stringify(productosAgregados),
            csrf_token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        window.location.reload();
    });
}
function eliminarProductoComanda(idDetalle) {
    let cantidad = prompt('¿Cuántos deseas eliminar? (deja vacío para eliminar todos)');
    if (cantidad === null) return;
    cantidad = cantidad.trim();
    let body = 'id_detalle=' + idDetalle;
    if (cantidad !== '') {
        body += '&cantidad=' + encodeURIComponent(cantidad);
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
document.getElementById('formLiberarMesa')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const idMesa = this.id_mesa.value;
    const idVenta = this.id_venta.value;
    const csrfToken = this.csrf_token.value;
    fetch('/restaurante/comanda/liberar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'same-origin',
        body: new URLSearchParams({
            id_mesa: idMesa,
            id_venta: idVenta,
            csrf_token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/restaurante/mesas';
        } else {
            alert(data.error || 'Error al liberar la mesa');
        }
    });
});



renderListaProductosAgregados();

// Permite agregar productos desde el menú (usado en menu_productos.php)
function agregarProductoMenu(id, nombre, precio) {
    // Buscar si ya existe el producto en la lista
    let idx = productosAgregados.findIndex(p => p.id === id);
    if (idx !== -1) {
        productosAgregados[idx].cantidad += 1;
    } else {
        productosAgregados.push({ id, nombre, cantidad: 1, precio });
    }
    renderListaProductosAgregados();
}

// Accesibilidad robusta: mueve el foco al botón 'Nueva Mesa' al cerrar el modal
function focusNuevaMesaBtn() {
    const btn = document.querySelector('[data-bs-toggle="modal"]');
    if (btn) btn.focus();
}

const mesaModal = document.getElementById('mesaModal');
if (mesaModal) {
    mesaModal.addEventListener('hidden.bs.modal', focusNuevaMesaBtn);

    // Fallback: detecta cierre manual del modal
    const observer = new MutationObserver(() => {
        if (mesaModal.style.display === 'none' || mesaModal.getAttribute('aria-hidden') === 'true') {
            focusNuevaMesaBtn();
        }
    });
    observer.observe(mesaModal, { attributes: true, attributeFilter: ['style', 'aria-hidden'] });
}
</script>
