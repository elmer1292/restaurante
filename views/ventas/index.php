<?php
// Archivo index.php para la sección de ventas

$ventaModel = new VentaModel();
$mesaModel = new MesaModel();

// Obtener todas las ventas pendientes (Estado = 'Pendiente')
$ventasPendientes = [];
try {
    $conn = (new \Database())->connect();
    $stmt = $conn->query("SELECT v.*, m.Numero_Mesa FROM ventas v INNER JOIN mesas m ON v.ID_Mesa = m.ID_Mesa WHERE v.Estado = 'Pendiente' ORDER BY v.Fecha_Hora DESC");
    $ventasPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $ventasPendientes = [];
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas Pendientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
    <?php include 'views/shared/sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
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
                                        <form method="post" style="margin:0;">
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
        </main>
    </div>

</div>
<?php
// Procesar cobro de la venta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cobrar_venta'])) {
    $idVenta = (int)$_POST['cobrar_venta'];
    try {
        $conn = (new \Database())->connect();
        $stmt = $conn->prepare("UPDATE ventas SET Estado = 'Pagada' WHERE ID_Venta = ?");
        $stmt->execute([$idVenta]);
        echo '<script>alert("Venta cobrada correctamente.");window.location.reload();</script>';
    } catch (Exception $e) {
        echo '<script>alert("Error al cobrar la venta: ' . $e->getMessage() . '");</script>';
    }
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>