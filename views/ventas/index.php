<!-- Modal de confirmación de pago (dinámico, reutilizable) -->
<div class="modal fade" id="confirmPagoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmPagoBody">
                <!-- Aquí se inserta el detalle dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelarConfirmPago">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmarPagoBtn">Confirmar Pago</button>
            </div>
        </div>
    </div>
</div>

<?php
// Iniciar sesión y verificar permisos de usuario
require_once dirname(__DIR__, 2) . '/config/Session.php';
Session::init();
Session::checkRole(['Administrador', 'Cajero']);

// Obtener ventas pendientes desde la base de datos
$ventaModel = new VentaModel();
$ventasPendientes = [];
try {
    $conn = (new \Database())->connect();
    $stmt = $conn->prepare("SELECT v.*, m.Numero_Mesa FROM ventas v INNER JOIN mesas m ON v.ID_Mesa = m.ID_Mesa WHERE v.Estado = 'Pendiente' ORDER BY v.Fecha_Hora DESC");
    $stmt->execute();
    $ventasPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $ventasPendientes = [];
}
?>


<h1 class="mb-4">Ventas Pendientes</h1>
<?php if (empty($ventasPendientes)): ?>
    <!-- Si no hay ventas pendientes, mostrar mensaje informativo -->
    <div class="alert alert-info">No hay ventas pendientes.</div>
<?php else: ?>
    <!-- Listado de ventas pendientes en formato acordeón -->
    <div class="accordion" id="ventasAccordion">
        <?php foreach ($ventasPendientes as $venta): ?>
            <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="heading<?= $venta['ID_Venta'] ?>">
                    <!-- Encabezado con datos de la venta y botón para imprimir ticket -->
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $venta['ID_Venta'] ?>">
                        Mesa <?= htmlspecialchars($venta['Numero_Mesa']) ?> | Venta #<?= $venta['ID_Venta'] ?> | Total: $<?= number_format($venta['Total'], 2) ?> | Fecha: <?= $venta['Fecha_Hora'] ?>
                    </button>
                    <!-- Botón de imprimir ticket removido temporalmente -->
                </h2>
                <div id="collapse<?= $venta['ID_Venta'] ?>" class="accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                    <div class="accordion-body">
                        <h5>Detalle de la Comanda</h5>
                        <ul class="list-group mb-3">
                            <?php 
                            // Obtener detalles de productos de la venta
                            $detalles = $ventaModel->getSaleDetails($venta['ID_Venta']);
                            if (empty($detalles)): ?>
                                <li class="list-group-item text-danger">No hay productos registrados en esta venta.</li>
                            <?php else:
                                foreach ($detalles as $detalle): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($detalle['Nombre_Producto'] ?? $detalle['ID_Producto']) ?>
                                        <span><?= $detalle['Cantidad'] ?> x $<?= number_format($detalle['Precio_Venta'], 2) ?> = <b>$<?= number_format($detalle['Cantidad'] * $detalle['Precio_Venta'], 2) ?></b></span>
                                    </li>
                            <?php endforeach; endif; ?>
                        </ul>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><b>Total:</b> $<?= number_format($venta['Total'], 2) ?></span>
                            <!-- Botón para abrir el modal de pago -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pagoModal<?= $venta['ID_Venta'] ?>">Registrar Pago</button>
                        </div>
                        <!-- Modal de pago para registrar el pago de la venta -->
                        <div class="modal fade" id="pagoModal<?= $venta['ID_Venta'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Registrar Pago - Venta #<?= $venta['ID_Venta'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form class="formPagoVenta" data-id-venta="<?= $venta['ID_Venta'] ?>">
                                        <div class="modal-body">
                                            <!-- Contenedor de métodos de pago -->
                                            <div id="metodosPagoContainer<?= $venta['ID_Venta'] ?>">
                                                <div class="row mb-2 metodo-pago-row">
                                                    <div class="col-6">
                                                        <select class="form-select metodo-pago-select" required>
                                                            <option value="Efectivo">Efectivo</option>
                                                            <option value="Transferencia">Transferencia</option>
                                                            <option value="Tarjeta">Tarjeta</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" min="1" class="form-control metodo-pago-monto" placeholder="Monto" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Botón para agregar otro método de pago -->
                                            <button type="button" class="btn btn-link" onclick="agregarMetodoPago(<?= $venta['ID_Venta'] ?>)">+ Agregar otro método</button>
                                            <div class="mt-3"><b>Saldo pendiente:</b> $<span id="saldoPendiente<?= $venta['ID_Venta'] ?>"><?= number_format($venta['Total'], 2) ?></span></div>
                                            <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-success">Registrar Pago</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
<script>
// Función para agregar dinámicamente otro método de pago al formulario
function agregarMetodoPago(idVenta) {
    const container = document.getElementById('metodosPagoContainer' + idVenta);
    if (!container) return;
    const row = document.createElement('div');
    row.className = 'row mb-2 metodo-pago-row';
    row.innerHTML = `<div class="col-5">
        <select class="form-select metodo-pago-select" required>
            <option value="Efectivo">Efectivo</option>
            <option value="Transferencia">Transferencia</option>
            <option value="Tarjeta">Tarjeta</option>
        </select>
    </div>
    <div class="col-5">
        <input type="number" min="1" class="form-control metodo-pago-monto" placeholder="Monto" required>
    </div>
    <div class="col-2 d-flex align-items-center">
        <button type="button" class="btn btn-sm btn-outline-danger eliminar-metodo-pago" title="Eliminar método">&times;</button>
    </div>`;
    container.appendChild(row);
    // Permite eliminar el método de pago agregado
    row.querySelector('.eliminar-metodo-pago').addEventListener('click', function() {
        row.remove();
    });
}

// Lógica principal para el registro de pagos
document.querySelectorAll('.formPagoVenta').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const idVenta = form.getAttribute('data-id-venta');
        const metodoSelects = form.querySelectorAll('.metodo-pago-select');
        const montoInputs = form.querySelectorAll('.metodo-pago-monto');
        let pagos = [];
        let totalPagado = 0;
        let metodos = {};
        // Recolectar métodos y montos de pago
        for (let i = 0; i < metodoSelects.length; i++) {
            const metodo = metodoSelects[i].value;
            const monto = parseFloat(montoInputs[i].value);
            if (!metodo || isNaN(monto) || monto <= 0) {
                alert('Completa todos los métodos y montos correctamente.');
                return;
            }
            pagos.push(`${metodo}:${monto}`);
            totalPagado += monto;
            if (!metodos[metodo]) metodos[metodo] = 0;
            metodos[metodo] += monto;
        }
        // Obtener saldo pendiente de la venta
        const saldo = parseFloat(form.closest('.accordion-body').querySelector('span[id^="saldoPendiente"]').textContent.replace(/[^0-9\.]/g, ''));
        // Eliminar mensajes previos de error
        form.parentNode.querySelectorAll('.alert-danger').forEach(e => e.remove());

        // Validaciones de monto
        if (totalPagado < saldo) {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'mt-3 alert alert-danger';
            msgDiv.textContent = 'El monto pagado es menor al saldo pendiente.';
            form.parentNode.appendChild(msgDiv);
            return;
        }
        // Solo un método de pago
        const metodosUnicos = Object.keys(metodos);
        // Función para mostrar el modal de confirmación y continuar si acepta
        function mostrarModalConfirmacion(detalle, onConfirm) {
            const confirmModalEl = document.getElementById('confirmPagoModal');
            const confirmModal = new bootstrap.Modal(confirmModalEl);
            document.getElementById('confirmPagoBody').innerHTML = detalle;
            // Remover listeners previos
            const btnConfirmar = document.getElementById('confirmarPagoBtn');
            const btnCancelar = document.getElementById('cancelarConfirmPago');
            btnConfirmar.onclick = null;
            btnCancelar.onclick = null;

            btnConfirmar.onclick = function() {
                // Ejecuta la acción de confirmación inmediatamente y luego cierra el modal
                onConfirm();
                confirmModal.hide();
            };
            btnCancelar.onclick = function() {
                confirmModal.hide();
            };

            // Si hay un modal de registro de pago abierto, ciérralo primero
            const registroModal = document.querySelector('.modal.show');
            if (registroModal && registroModal !== confirmModalEl) {
                const registroInstance = bootstrap.Modal.getOrCreateInstance(registroModal);
                registroModal.addEventListener('hidden.bs.modal', function handler() {
                    registroModal.removeEventListener('hidden.bs.modal', handler);
                    confirmModal.show();
                });
                registroInstance.hide();
            } else {
                confirmModal.show();
            }
        }

        if (metodosUnicos.length === 1) {
            const metodo = metodosUnicos[0];
            // Solo efectivo
            if (metodo === 'Efectivo') {
                if (totalPagado > saldo) {
                    const cambio = totalPagado - saldo;
                    // Cerrar primero el modal de registro de pago, luego mostrar el de confirmación
                    const modal = form.closest('.modal');
                    if (modal) {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(modal);
                        modal.addEventListener('hidden.bs.modal', function handler() {
                            modal.removeEventListener('hidden.bs.modal', handler);
                            mostrarModalConfirmacion(
                                `<p>El cliente entregó <b>$${totalPagado.toFixed(2)}</b> en efectivo.<br> <b>Cambio a devolver:</b> $${cambio.toFixed(2)}</p><p>¿Registrar pago?</p>`,
                                registrarPago
                            );
                        });
                        modalInstance.hide();
                    } else {
                        mostrarModalConfirmacion(
                            `<p>El cliente entregó <b>$${totalPagado.toFixed(2)}</b> en efectivo.<br> <b>Cambio a devolver:</b> $${cambio.toFixed(2)}</p><p>¿Registrar pago?</p>`,
                            registrarPago
                        );
                    }
                    return;
                }
                // Registrar pago y luego redirigir a ticket
                registrarPago();
            } else {
                // Solo transferencia o solo tarjeta
                if (totalPagado > saldo) {
                    const msgDiv = document.createElement('div');
                    msgDiv.className = 'mt-3 alert alert-danger';
                    msgDiv.textContent = 'El monto pagado excede el saldo pendiente.';
                    form.parentNode.appendChild(msgDiv);
                    return;
                }
                // Registrar pago y luego redirigir a ticket
                registrarPago();
            }
        } else {
            // Múltiples métodos de pago (mixto)
            if (totalPagado > saldo) {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'mt-3 alert alert-danger';
                msgDiv.textContent = 'El monto pagado excede el saldo pendiente.';
                form.parentNode.appendChild(msgDiv);
                return;
            }
            // Mostrar confirmación detallando los métodos y montos
            let detalle = '<p>Está pagando:</p><ul>';
            for (const [met, monto] of Object.entries(metodos)) {
                detalle += `<li><b>${met}:</b> $${monto.toFixed(2)}</li>`;
            }
            detalle += `</ul><p><b>Total:</b> $${totalPagado.toFixed(2)}</p><p>¿Registrar pago?</p>`;
            mostrarModalConfirmacion(detalle, registrarPago);
            return;
        }

        // Función para registrar el pago y recargar la página tras éxito
        function registrarPago() {
            let metodoPagoStr = pagos.join(', ');
            if (metodoPagoStr.length > 200) metodoPagoStr = metodoPagoStr.substring(0, 200);
            const csrfToken = form.querySelector('input[name="csrf_token"]').value;
            form.querySelectorAll('button, input[type="submit"]').forEach(btn => btn.disabled = true);
            fetch('<?php echo BASE_URL; ?>ventas/registrarPago', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    id_venta: idVenta,
                    metodo_pago: metodoPagoStr,
                    monto: totalPagado,
                    csrf_token: csrfToken
                })
            })
            .then(res => res.json())
            .then(data => {
                const msgDiv = document.createElement('div');
                msgDiv.className = 'mt-3';
                if (data.success) {
                    let mensaje = 'Pago registrado correctamente.';
                    if (data.cambio && metodosUnicos.length === 1 && metodosUnicos[0] === 'Efectivo') {
                        mensaje += `<br><b>Cambio a devolver:</b> $${parseFloat(data.cambio).toFixed(2)}`;
                    }
                    msgDiv.innerHTML = `<div class=\"alert alert-success\">${mensaje}</div>`;
                    form.parentNode.appendChild(msgDiv);
                    setTimeout(() => {
                        // Cierra cualquier modal abierto antes de redirigir a ticket
                        const openModal = document.querySelector('.modal.show');
                        if (openModal) {
                            const modalInstance = bootstrap.Modal.getOrCreateInstance(openModal);
                            modalInstance.hide();
                            setTimeout(function() {
                                window.open('<?php echo BASE_URL; ?>ventas/ticket?id=' + idVenta, '_blank', 'width=400,height=600');
                            }, 350);
                        } else {
                            window.open('<?php echo BASE_URL; ?>ventas/ticket?id=' + idVenta, '_blank', 'width=400,height=600');
                        }
                    }, 200);
                } else {
                    msgDiv.innerHTML = '<div class=\"alert alert-danger\">' + (data.error || 'Error al registrar el pago.') + '</div>';
                    form.querySelectorAll('button, input[type=\"submit\"]').forEach(btn => btn.disabled = false);
                }
            })
            .catch(() => {
                form.querySelectorAll('button, input[type="submit"]').forEach(btn => btn.disabled = false);
            });
        }
    });
});
</script>