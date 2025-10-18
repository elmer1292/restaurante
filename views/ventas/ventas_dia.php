<?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
<?php require_once dirname(__DIR__) . '/shared/header.php'; ?>

<?php
require_once __DIR__ . '/../../models/VentaModel.php';

// $ventas y $fecha vienen del controller
$fechaFiltro = isset($fecha) ? $fecha : date('Y-m-d');
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Ventas del día</h2>
        <form class="d-flex" method="get" action="<?= BASE_URL ?>ventas/dia">
            <input type="date" name="fecha" class="form-control me-2" value="<?= htmlspecialchars($fechaFiltro) ?>">
            <button class="btn btn-primary">Filtrar</button>
        </form>
    </div>

    <?php if (empty($ventas)): ?>
        <div class="alert alert-info">No hay ventas para la fecha seleccionada.</div>
    <?php else: ?>
        <div class="accordion" id="ventasDiaAccordion">
            <?php foreach ($ventas as $venta): ?>
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="headingDia<?= $venta['ID_Venta'] ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDia<?= $venta['ID_Venta'] ?>">
                            Venta #<?= $venta['ID_Venta'] ?> | Mesa <?= htmlspecialchars($venta['Numero_Mesa'] ?? '-') ?> | Total: <?= htmlspecialchars(number_format(($venta['Total'] + ($venta['Servicio'] ?? 0)), 2)) ?> | Estado: <?= htmlspecialchars($venta['Estado']) ?> | <?= date('d/m/Y H:i', strtotime($venta['Fecha_Hora'])) ?>
                        </button>
                    </h2>
                    <div id="collapseDia<?= $venta['ID_Venta'] ?>" class="accordion-collapse collapse" data-bs-parent="#ventasDiaAccordion">
                        <div class="accordion-body">
                            <h5>Detalles</h5>
                            <ul class="list-group mb-3">
                                <?php
                                $ventaModel = new VentaModel();
                                $detalles = $ventaModel->getSaleDetails($venta['ID_Venta']);
                                if (empty($detalles)) {
                                    echo '<li class="list-group-item text-muted">Sin detalles</li>';
                                } else {
                                    foreach ($detalles as $d) {
                                        $sub = number_format((float)$d['Cantidad'] * (float)$d['Precio_Venta'], 2);
                                        echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . htmlspecialchars($d['Nombre_Producto']) . '<span>' . (int)$d['Cantidad'] . ' x ' . htmlspecialchars(number_format($d['Precio_Venta'], 2)) . ' = <strong>$' . $sub . '</strong></span></li>';
                                    }
                                }
                                ?>
                            </ul>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Subtotal:</strong> <?= htmlspecialchars(number_format($venta['Total'], 2)) ?>
                                    <?php if (isset($venta['Servicio']) && $venta['Servicio'] > 0): ?>
                                        | <strong>Servicio:</strong> <?= htmlspecialchars(number_format($venta['Servicio'], 2)) ?>
                                    <?php endif; ?>
                                    | <strong>Total:</strong> <?= htmlspecialchars(number_format($venta['Total'] + ($venta['Servicio'] ?? 0), 2)) ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>imprimir_ticket.php?id=<?= $venta['ID_Venta'] ?>">Imprimir factura</a>
                                    <?php if ($venta['Estado'] === 'Pendiente'): ?>
                                        <a class="btn btn-outline-primary" href="<?= BASE_URL ?>ventas?ver=<?= $venta['ID_Venta'] ?>">Ver venta</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/shared/footer.php'; ?>
