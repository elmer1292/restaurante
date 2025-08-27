<?php
require_once 'config/Session.php';
require_once dirname(__DIR__, 2) . '/helpers/Pagination.php';

Session::init();
Session::checkRole(['Administrador', 'Cajero']);

$ventaModel = new VentaModel();
$params = getPageParams($_GET, 50);
$ventasPendientes = [];
try {
    $conn = (new \Database())->connect();
    $stmt = $conn->prepare("SELECT v.*, m.Numero_Mesa FROM ventas v INNER JOIN mesas m ON v.ID_Mesa = m.ID_Mesa WHERE v.Estado = 'Pendiente' ORDER BY v.Fecha_Hora DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', (int)$params['limit'], PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$params['offset'], PDO::PARAM_INT);
    $stmt->execute();
    $ventasPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmtTotal = $conn->query("SELECT COUNT(*) as total FROM ventas WHERE Estado = 'Pendiente'");
    $rowTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC);
    $totalVentas = $rowTotal ? (int)$rowTotal['total'] : 0;
    $totalPages = $params['limit'] > 0 ? ceil($totalVentas / $params['limit']) : 1;
} catch (Exception $e) {
    $ventasPendientes = [];
    $totalPages = 1;
}

// ...existing code...
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
        <!-- Controles de paginación -->
        <nav aria-label="Paginación de ventas">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $params['page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $params['limit']; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
<?php
// Procesar cobro de la venta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cobrar_venta'])) {
    $idVenta = (int)$_POST['cobrar_venta'];
    try {
    $conn = (new \Database())->connect();
    // Marcar venta como pagada
    $stmt = $conn->prepare("UPDATE ventas SET Estado = 'Pagada' WHERE ID_Venta = ?");
    $stmt->execute([$idVenta]);
    // Liberar la mesa asociada
    $stmtMesa = $conn->prepare("UPDATE mesas SET Estado = 0 WHERE ID_Mesa = (SELECT ID_Mesa FROM ventas WHERE ID_Venta = ?)");
    $stmtMesa->execute([$idVenta]);
    echo '<script>alert("Venta cobrada y mesa liberada correctamente.");window.location.reload();</script>';
    } catch (Exception $e) {
        echo '<script>alert("Error al cobrar la venta: ' . $e->getMessage() . '");</script>';
    }
}
?>

<?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
<script>
function agregarMetodoPago(idVenta) {
    const container = document.getElementById('metodosPagoContainer' + idVenta);
    if (!container) return;
    const row = document.createElement('div');
    row.className = 'row mb-2 metodo-pago-row';
    row.innerHTML = `<div class="col-6">
        <select class="form-select metodo-pago-select" required>
            <option value="Efectivo">Efectivo</option>
            <option value="Transferencia">Transferencia</option>
            <option value="Tarjeta">Tarjeta</option>
        </select>
    </div>
    <div class="col-6">
        <input type="number" min="1" class="form-control metodo-pago-monto" placeholder="Monto" required>
    </div>`;
    container.appendChild(row);
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
        if (totalPagado > saldo) {
            alert('El monto pagado excede el saldo pendiente.');
            return;
        }
        // Limitar el string a 20 caracteres para Metodo_Pago
        let metodoPagoStr = pagos.join(', ');
        if (metodoPagoStr.length > 20) metodoPagoStr = metodoPagoStr.substring(0, 20);
        const csrfToken = form.querySelector('input[name="csrf_token"]').value;
    fetch('<?php echo BASE_URL; ?>views/ventas/registrar_pago.php', {
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
            if (data.success) {
                alert('Pago registrado correctamente.');
                window.location.reload();
            } else {
                alert(data.error || 'Error al registrar el pago.');
            }
        });
    });
});
</script>