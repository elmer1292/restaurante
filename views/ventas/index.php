<?php
require_once dirname(__DIR__, 2) . '/config/Session.php';

Session::init();
Session::checkRole(['Administrador', 'Cajero']);

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
    <div class="alert alert-info">No hay ventas pendientes.</div>
<?php else: ?>
    <div class="accordion" id="ventasAccordion">
        <?php foreach ($ventasPendientes as $venta): ?>
            <div class="accordion-item mb-2">
                <h2 class="accordion-header" id="heading<?= $venta['ID_Venta'] ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $venta['ID_Venta'] ?>">
                        Mesa <?= htmlspecialchars($venta['Numero_Mesa']) ?> | Venta #<?= $venta['ID_Venta'] ?> | Total: $<?= number_format($venta['Total'], 2) ?> | Fecha: <?= $venta['Fecha_Hora'] ?>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm ms-2" onclick="window.open('<?php echo BASE_URL; ?>ventas/ticket.php?id=<?= $venta['ID_Venta'] ?>', '_blank', 'width=300,height=600')">
                        <i class="bi bi-printer"></i> Imprimir Ticket
                    </button>
                </h2>
                <div id="collapse<?= $venta['ID_Venta'] ?>" class="accordion-collapse collapse" data-bs-parent="#ventasAccordion">
                    <div class="accordion-body">
                        <h5>Detalle de la Comanda</h5>
                        <ul class="list-group mb-3">
                            <?php 
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
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pagoModal<?= $venta['ID_Venta'] ?>">Registrar Pago</button>
                        </div>
                        <!-- Modal de pago -->
                        <div class="modal fade" id="pagoModal<?= $venta['ID_Venta'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Registrar Pago - Venta #<?= $venta['ID_Venta'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form class="formPagoVenta" data-id-venta="<?= $venta['ID_Venta'] ?>">
                                        <div class="modal-body">
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
                                                        <input type="number" min="1" max="<?= $venta['Total'] ?>" class="form-control metodo-pago-monto" placeholder="Monto" required>
                                                    </div>
                                                </div>
                                            </div>
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
    row.querySelector('.eliminar-metodo-pago').addEventListener('click', function() {
        row.remove();
    });
}

document.querySelectorAll('.formPagoVenta').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const idVenta = form.getAttribute('data-id-venta');
        const metodoSelects = form.querySelectorAll('.metodo-pago-select');
        const montoInputs = form.querySelectorAll('.metodo-pago-monto');
        let pagos = [];
        let totalPagado = 0;
        for (let i = 0; i < metodoSelects.length; i++) {
            const metodo = metodoSelects[i].value;
            const monto = parseFloat(montoInputs[i].value);
            if (!metodo || isNaN(monto) || monto <= 0) {
                alert('Completa todos los métodos y montos correctamente.');
                return;
            }
            pagos.push(`${metodo}:${monto}`);
            totalPagado += monto;
        }
        const saldo = parseFloat(form.closest('.accordion-body').querySelector('span[id^="saldoPendiente"]').textContent.replace(/[^\d\.]/g, ''));
        // Eliminar mensajes previos
        form.parentNode.querySelectorAll('.alert-danger').forEach(e => e.remove());
        if (totalPagado > saldo) {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'mt-3 alert alert-danger';
            msgDiv.textContent = 'El monto pagado excede el saldo pendiente.';
            form.parentNode.appendChild(msgDiv);
            return;
        }
        if (totalPagado < saldo) {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'mt-3 alert alert-danger';
            msgDiv.textContent = 'El monto pagado es menor al saldo pendiente.';
            form.parentNode.appendChild(msgDiv);
            return;
        }
        // Limitar el string a 20 caracteres para Metodo_Pago
        let metodoPagoStr = pagos.join(', ');
        if (metodoPagoStr.length > 20) metodoPagoStr = metodoPagoStr.substring(0, 20);
        const csrfToken = form.querySelector('input[name="csrf_token"]').value;
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
                msgDiv.innerHTML = '<div class="alert alert-success">Pago registrado correctamente.</div>';
                setTimeout(() => window.location.reload(), 1200);
            } else {
                msgDiv.innerHTML = '<div class="alert alert-danger">' + (data.error || 'Error al registrar el pago.') + '</div>';
            }
            form.parentNode.appendChild(msgDiv);
        });
    });
});
</script>