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

require_once 'views/shared/header.php';
?>

<h1 class="mb-4">Ventas Pendientes</h1>
<?php if (empty($ventasPendientes)): ?>
    <div class="alert alert-info">No hay ventas pendientes.</div>
<?php else: ?>
    <div class="accordion" id="ventasAccordion">
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
                            <form method="post" style="margin:0;">
                                <?php require_once '../../helpers/Csrf.php'; ?>
                                <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
                                <input type="hidden" name="cobrar_venta" value="<?= $venta['ID_Venta'] ?>">
                                <button type="submit" class="btn btn-success">Cobrar Mesa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

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

<?php require_once 'views/shared/footer.php'; ?>