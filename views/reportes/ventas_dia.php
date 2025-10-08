<?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
<div class="container py-4">
    <h3>Ventas por Empleado - <?= htmlspecialchars($fecha) ?></h3>
    <form method="get" class="mb-3">
        <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" required>
        <button type="submit" class="btn btn-primary btn-sm">Ver</button>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Empleado</th>
                <th># Ventas</th>
                <th>Total Vendido</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ventas as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['empleado']) ?></td>
                <td><?= (int)$v['ventas'] ?></td>
                <td>C$ <?= number_format($v['total'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($ventas)): ?>
            <tr><td colspan="3" class="text-center">Sin ventas para este día.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="<?= BASE_URL ?>reportes" class="btn btn-secondary btn-sm">Volver a reportes</a>
</div>