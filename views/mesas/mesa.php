<?php
// Solo HTML y variables recibidas del controlador
if (isset($mensaje)) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($mensaje) . '</div>';
    return;
}
?>
<div class="row g-4">
    <!-- Modal Liberar Mesa SIEMPRE en el DOM -->
<div class="modal fade" id="modalLiberarMesa" tabindex="-1" aria-labelledby="modalLiberarMesaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalLiberarMesaLabel">Liberar Mesa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formLiberarMesaModal" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="id_mesa" value="<?php echo htmlspecialchars($idMesa); ?>">
                    <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($comanda['ID_Venta']); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <div class="mb-3">
                        <label for="motivoLiberacion" class="form-label">Motivo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="motivoLiberacion" name="motivo" maxlength="100" required placeholder="Ej: Mesa desocupada, error, etc.">
                    </div>
                    <div class="mb-3">
                        <label for="descripcionLiberacion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionLiberacion" name="descripcion" rows="2" maxlength="255" placeholder="Detalles adicionales (opcional)"></textarea>
                    </div>
                    <div id="liberarMesaError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Liberar Mesa</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal para ingresar cantidad de producto -->
<div class="modal fade" id="modalCantidadProducto" tabindex="-1" aria-labelledby="modalCantidadProductoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCantidadProductoLabel">Cantidad de producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formCantidadProducto">
                    <div class="mb-3">
                        <label for="inputCantidadProducto" class="form-label">¿Cuántos desea agregar?</label>
                        <input type="number" class="form-control" id="inputCantidadProducto" min="1" value="1" required>
                    </div>
                    <div class="mb-3" id="preparacionContainer" style="display:none;">
                        <label for="inputPreparacion" class="form-label">Preparación especial (opcional)</label>
                        <textarea class="form-control" id="inputPreparacion" rows="2" maxlength="200" placeholder="Ej: Sin cebolla, término medio, etc."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Agregar</button>
                </form>
            </div>
        </div>
    </div>
</div>
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
                                                <?php if ($userRole === 'Administrador' || $userRole === 'Cajero'): ?>
                                                <button type="button" class="btn btn-danger w-100" style="margin-bottom: 1em;" data-bs-toggle="modal" data-bs-target="#modalLiberarMesa">Liberar Mesa</button>
                                                <?php endif; ?>
</div>
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
                                    <?php if (!empty($detalle['preparacion'])): ?>
                                        <div class="text-muted small ms-3">Preparación: <?php echo htmlspecialchars($detalle['preparacion']); ?></div>
                                    <?php endif; ?>
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
        let html = '<div><span class="fw-bold">' + prod.nombre + '</span> <span class="badge bg-secondary ms-2">x' + prod.cantidad + '</span>';
        if (prod.preparacion && prod.preparacion.trim() !== '') {
            html += '<br><span class="text-muted small">Preparación: ' + prod.preparacion + '</span>';
        }
        html += '</div>';
        html += '<button class="btn btn-sm btn-outline-danger ms-2" onclick="eliminarProductoAgregado(' + i + ')">Eliminar</button>';
        li.innerHTML = html;
        lista.appendChild(li);
    }
}
function agregarProducto(nombre, cantidad, id, preparacion = '') {
    // Obtener la categoría del botón seleccionado
    let categoria = '';
    const btns = document.querySelectorAll('.btn-agregar-producto');
    for (let btn of btns) {
        if (btn.dataset.id == id) {
            categoria = btn.dataset.categoria || '';
            break;
        }
    }
    productosAgregados.push({ nombre, cantidad, id, preparacion, categoria });
    renderListaProductosAgregados();
}
function eliminarProductoAgregado(idx) {
    productosAgregados.splice(idx, 1);
    renderListaProductosAgregados();
}
function enviarProductosComanda() {
    if (productosAgregados.length === 0) return;
    // Agrupar productos por id y preparación
    const agrupados = [];
    for (let i = 0; i < productosAgregados.length; i++) {
        const prod = productosAgregados[i];
        // Buscar si ya existe uno igual (id y preparación)
        const idx = agrupados.findIndex(p => p.id === prod.id && (p.preparacion || '') === (prod.preparacion || ''));
        if (idx !== -1) {
            agrupados[idx].cantidad += prod.cantidad;
        } else {
            agrupados.push({ ...prod });
        }
    }
    // Separar productos para barra y cocina según categoría
    const categoriasBarra = ['Bebidas', 'Licores', 'Cockteles', 'Cervezas'];
    const productosBarra = agrupados.filter(p => categoriasBarra.includes((p.categoria || '').trim()));
    const productosCocina = agrupados.filter(p => !categoriasBarra.includes((p.categoria || '').trim()));
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
            productos: JSON.stringify(agrupados),
            csrf_token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mapear productos a la estructura esperada por el backend
            const mapProducto = p => ({
                Cantidad: p.cantidad,
                Nombre_Producto: p.nombre,
                Preparacion: p.preparacion || '',
                categoria: p.categoria || ''
            });
            let promesas = [];
            if (productosBarra.length > 0) {
                promesas.push(
                    fetch('<?php echo BASE_URL; ?>comandas/imprimirComanda', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id_mesa: idMesa, tipo: 'barra', productos: productosBarra.map(mapProducto) })
                    }).then(res => res.json())
                );
            }
            if (productosCocina.length > 0) {
                promesas.push(
                    fetch('<?php echo BASE_URL; ?>comandas/imprimirComanda', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id_mesa: idMesa, tipo: 'cocina', productos: productosCocina.map(mapProducto) })
                    }).then(res => res.json())
                );
            }
            Promise.all(promesas)
                .catch(() => {})
                .finally(() => {
                    window.location.reload();
                });
        } else {
            alert('Error al enviar productos: ' + (data.error || 'Error desconocido.'));
        }
    });
}
// Modal Bootstrap para eliminar productos del detalle
let eliminarProductoModal = null;
let eliminarProductoCallback = null;
function showEliminarProductoModal(idDetalle, cantidad) {
    if (!eliminarProductoModal) {
        const modalHtml = `
        <div class="modal fade" id="modalEliminarProducto" tabindex="-1" aria-labelledby="modalEliminarProductoLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarProductoLabel">Eliminar producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <form id="formEliminarProductoDetalle">
                  <div class="mb-3">
                    <label for="cantidadEliminar" class="form-label">¿Cuántos deseas eliminar?</label>
                    <input type="number" class="form-control" id="cantidadEliminar" min="1" max="${cantidad}" value="${cantidad}" autocomplete="off">
                    <div class="form-text">Deja vacío o pon el máximo para eliminar todos.</div>
                  </div>
                  <button type="submit" class="btn btn-danger w-100">Eliminar</button>
                </form>
              </div>
            </div>
          </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        eliminarProductoModal = new bootstrap.Modal(document.getElementById('modalEliminarProducto'));
        document.getElementById('formEliminarProductoDetalle').addEventListener('submit', function(e) {
            e.preventDefault();
            if (eliminarProductoCallback) eliminarProductoCallback();
        });
    } else {
        document.getElementById('cantidadEliminar').max = cantidad;
        document.getElementById('cantidadEliminar').value = cantidad;
    }
    eliminarProductoModal.show();
    return new Promise(resolve => {
        eliminarProductoCallback = () => {
            const cantPrompt = document.getElementById('cantidadEliminar').value.trim();
            eliminarProductoModal.hide();
            resolve(cantPrompt);
        };
    });
}
function eliminarProductoComanda(idDetalle, cantidad) {
    let csrfToken = '<?php echo htmlspecialchars($csrf_token); ?>';
    let body = 'id_detalle=' + idDetalle + '&csrf_token=' + encodeURIComponent(csrfToken);
    if (cantidad > 1) {
        showEliminarProductoModal(idDetalle, cantidad).then(cantPrompt => {
            if (cantPrompt === null) return;
            if (cantPrompt !== '' && !isNaN(cantPrompt)) {
                body += '&cantidad=' + encodeURIComponent(cantPrompt);
            }
            else {
                body += '&cantidad=' + cantidad;
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
        });
    } else {
        body += '&cantidad=1';
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
}

// Modal Liberar Mesa - submit
document.getElementById('formLiberarMesaModal')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const idMesa = form.id_mesa.value;
    const idVenta = form.id_venta.value;
    const csrfToken = form.csrf_token.value;
    const motivo = form.motivo.value.trim();
    const descripcion = form.descripcion.value.trim();
    const errorDiv = document.getElementById('liberarMesaError');
    errorDiv.classList.add('d-none');
    errorDiv.textContent = '';
    if (!motivo) {
        errorDiv.textContent = 'El motivo es obligatorio.';
        errorDiv.classList.remove('d-none');
        return;
    }
    fetch('<?php echo BASE_URL; ?>comanda/liberar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'same-origin',
        body: new URLSearchParams({
            id_mesa: idMesa,
            id_venta: idVenta,
            csrf_token: csrfToken,
            motivo: motivo,
            descripcion: descripcion
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal y recargar
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalLiberarMesa'));
            modal.hide();
            window.location.href = '<?php echo BASE_URL; ?>mesas';
        } else {
            errorDiv.textContent = data.error || 'Error al liberar la mesa';
            errorDiv.classList.remove('d-none');
        }
    })
    .catch(() => {
        errorDiv.textContent = 'Error de comunicación con el servidor.';
        errorDiv.classList.remove('d-none');
    });
});



renderListaProductosAgregados();
// Interceptar clicks en botones de productos del menú (usado en menu_productos.php)
let productoSeleccionado = null;
let productoSeleccionadoCategoria = null;
let productoSeleccionadoPrecio = null;
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-agregar-producto').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const nombre = this.getAttribute('data-nombre');
            const id = parseInt(this.getAttribute('data-id'));
            const categoria = this.getAttribute('data-categoria');
            const precio = parseFloat(this.getAttribute('data-precio'));
            productoSeleccionado = { nombre, id };
            productoSeleccionadoCategoria = categoria;
            productoSeleccionadoPrecio = precio;
            document.getElementById('inputCantidadProducto').value = 1;
            // Mostrar campo preparación.
            // Preferimos un atributo emitido por el servidor `data-is-food="1"` para clasificación dinámica.
            const btn = this; // el botón que disparó el evento
            const isFoodAttr = btn.getAttribute('data-is-food');
            if (isFoodAttr !== null) {
                if (isFoodAttr === '1' || isFoodAttr === 'true') {
                    document.getElementById('preparacionContainer').style.display = '';
                } else {
                    document.getElementById('preparacionContainer').style.display = 'none';
                    document.getElementById('inputPreparacion').value = '';
                }
            }
            const modal = new bootstrap.Modal(document.getElementById('modalCantidadProducto'));
            modal.show();
        });
    });
    // Modal cantidad submit
    document.getElementById('formCantidadProducto').addEventListener('submit', function(e) {
        e.preventDefault();
        const cantidad = parseInt(document.getElementById('inputCantidadProducto').value, 10);
        const preparacion = document.getElementById('inputPreparacion').value.trim();
        if (productoSeleccionado && cantidad > 0) {
            // Si ya existe con la misma preparación, suma la cantidad
            let idx = productosAgregados.findIndex(p => p.id === productoSeleccionado.id && (p.preparacion || '') === preparacion);
            if (idx !== -1) {
                productosAgregados[idx].cantidad += cantidad;
            } else {
                productosAgregados.push({ nombre: productoSeleccionado.nombre, cantidad, id: productoSeleccionado.id, categoria: productoSeleccionadoCategoria, precio: productoSeleccionadoPrecio, preparacion });
            }
            renderListaProductosAgregados();
            // Limpiar campo preparación y cantidad
            document.getElementById('inputPreparacion').value = '';
            document.getElementById('inputCantidadProducto').value = 1;
            // Quitar el foco del elemento activo antes de cerrar el modal
            if (document.activeElement) document.activeElement.blur();
            // Cerrar modal
            const modalEl = document.getElementById('modalCantidadProducto');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            // Agregar listener para mover el foco después del cierre
            const focusAfterClose = () => {
                setTimeout(() => {
                    const btnEnviar = document.querySelector('.btn-success.w-100:not([disabled])');
                    if (btnEnviar) btnEnviar.focus();
                    modalEl.removeEventListener('hidden.bs.modal', focusAfterClose);
                }, 30); // Espera breve para asegurar que aria-hidden ya no esté
            };
            modalEl.addEventListener('hidden.bs.modal', focusAfterClose);
            modalInstance.hide();
        }
    });
});

// Eliminado: lógica y referencias al modal de dividir cuenta y parciales
</script>
