<?php
require_once dirname(__DIR__, 2) . '/config/base_url.php';
?>
<div class="container py-4">
    <h2 class="mb-4">Reporte de Inventario</h2>
    <form class="row g-3 mb-4" method="get" action="<?= BASE_URL ?>reportes/inventario">
        <div class="col-auto">
            <button type="submit" name="exportar" value="excel" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
            </button>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Precio Costo</th>
                    <th>Precio Venta</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($productos && count($productos) > 0): ?>
                    <?php foreach ($productos as $i => $prod): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= htmlspecialchars($prod['Nombre_Producto']) ?></td>
                            <td><?= htmlspecialchars($prod['Nombre_Categoria']) ?></td>
                            <td><?= (int)$prod['Stock'] ?></td>
                            <td>C$ <?= number_format($prod['Precio_Costo'], 2) ?></td>
                            <td>C$ <?= number_format($prod['Precio_Venta'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay productos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="<?= BASE_URL ?>reportes" class="btn btn-secondary btn-sm mt-3">Volver a reportes</a>
</div>
