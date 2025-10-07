<?php
// Solo HTML y variables recibidas del controlador
if (isset($mensaje)) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($mensaje) . '</div>';
    return;
}
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
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <button type="submit" class="btn btn-primary w-100">Crear Comanda</button>
                    </form>
                <?php } else { ?>
                    <?php include dirname(__DIR__, 2) . '/views/shared/menu_productos.php'; ?>
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
                    if (!$detalles || count($detalles) === 0) { ?>
                        <form id="formLiberarMesa" method="post" style="margin-bottom: 1em;">
                            <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($idMesa); ?>">
                            <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($comanda['ID_Venta']); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
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
                                        <?php if ($detalle['Cantidad'] > 1): ?>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="eliminarProductoComanda(<?php echo $detalle['ID_Detalle']; ?>, <?php echo $detalle['Cantidad']; ?>)">Eliminar</button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="eliminarProductoComanda(<?php echo $detalle['ID_Detalle']; ?>, 1)">Eliminar</button>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php } ?>
                            <li class="list-group-item fw-bold d-flex justify-content-between align-items-center bg-light">
                                <span>Total</span><span class="text-primary">$<?php echo number_format($total, 2); ?></span>
                            </li>
                        </ul>
                        <a href="<?php echo BASE_URL; ?>mesas/dividir_cuenta?id_mesa=<?php echo $mesa['ID_Mesa']; ?>" class="btn btn-outline-primary mb-3">
                            <i class="bi bi-scissors"></i> Dividir cuenta
                        </a>
                        <a href="<?php echo BASE_URL; ?>imprimir_ticket_pre_factura.php?id_mesa=<?php echo $mesa['ID_Mesa']; ?>" class="btn btn-warning mb-3 ms-2">
                            <i class="bi bi-receipt"></i> Pre-Facturas
                        </a>
                        </ul>
                    <?php }
                } ?>
            </div>
        </div>
    </div>
</div>
<?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
<script>
document.getElementById('formCrearComanda')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const idMesa = this.id_mesa.value;
    fetch('<?php echo BASE_URL; ?>comanda/crear', {
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
    let idMesa = idMesaInput ? idMesaInput.value : <?php echo json_encode($idMesa); ?>;
    let csrfToken = csrfTokenInput ? csrfTokenInput.value : <?php echo json_encode(Csrf::getToken()); ?>;
    fetch('<?php echo BASE_URL; ?>comanda/agregarProductos', {
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
        if (data.success) {
            // Imprimir barra y cocina automáticamente
            Promise.all([
                fetch('<?php echo BASE_URL; ?>comandas/imprimirComanda', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_mesa: idMesa, tipo: 'barra' })
                }).then(res => res.json()),
                fetch('<?php echo BASE_URL; ?>comandas/imprimirComanda', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_mesa: idMesa, tipo: 'cocina' })
                }).then(res => res.json())
            ]).then(([barraRes, cocinaRes]) => {
                let msg = '';
                if (barraRes.success) {
                    msg += 'Ticket de barra enviado.\n';
                } else {
                    msg += 'Error al imprimir barra: ' + (barraRes.error || 'Error desconocido.') + '\n';
                }
                if (cocinaRes.success) {
                    msg += 'Ticket de cocina enviado.';
                } else {
                    msg += 'Error al imprimir cocina: ' + (cocinaRes.error || 'Error desconocido');
                }
                alert(msg);
                window.location.reload();
            });
        } else {
            alert('Error al enviar productos: ' + (data.error || 'Error desconocido.'));
        }
    });
}
function eliminarProductoComanda(idDetalle, cantidad) {
    let csrfToken = '<?php echo htmlspecialchars($csrf_token); ?>';
    let body = 'id_detalle=' + idDetalle + '&csrf_token=' + encodeURIComponent(csrfToken);
    if (cantidad > 1) {
        let cantPrompt = prompt('¿Cuántos deseas eliminar? (deja vacío para eliminar todos)', cantidad);
        if (cantPrompt === null) return;
        cantPrompt = cantPrompt.trim();
        if (cantPrompt !== '') {
            body += '&cantidad=' + encodeURIComponent(cantPrompt);
        }
    } else {
        body += '&cantidad=1';
    }
    fetch('<?php echo BASE_URL; ?>detalleventa/eliminarProducto', {
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
    fetch('<?php echo BASE_URL; ?>comanda/liberar', {
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
            window.location.href = '<?php echo BASE_URL; ?>mesas';
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

// Eliminado: lógica y referencias al modal de dividir cuenta y parciales
</script>
