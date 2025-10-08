<?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
<div class="container py-4">
    <h2>Movimientos de Caja</h2>
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success"> <?= htmlspecialchars($mensaje) ?> </div>
    <?php endif; ?>
    <div class="mb-3">
        <a href="<?= BASE_URL ?>movimientos/registrar" class="btn btn-primary">Registrar Movimiento</a>
    </div>
    <form class="row g-2 mb-3" method="get">
        <div class="col-md-2">
            <input type="date" name="fecha_desde" value="<?= htmlspecialchars($fechaDesde ?? '') ?>" class="form-control" placeholder="Desde">
        </div>
        <div class="col-md-2">
            <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($fechaHasta ?? '') ?>" class="form-control" placeholder="Hasta">
        </div>
        <div class="col-md-2">
            <select name="tipo" class="form-select">
                <option value="">Todos</option>
                <option value="Ingreso" <?= ($tipo ?? '') === 'Ingreso' ? 'selected' : '' ?>>Ingreso</option>
                <option value="Egreso" <?= ($tipo ?? '') === 'Egreso' ? 'selected' : '' ?>>Egreso</option>
                <option value="Apertura" <?= ($tipo ?? '') === 'Apertura' ? 'selected' : '' ?>>Apertura</option>
                <option value="Cierre" <?= ($tipo ?? '') === 'Cierre' ? 'selected' : '' ?>>Cierre</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="limit" class="form-select">
                <option value="10" <?= (isset($limit) && $limit == 10) ? 'selected' : '' ?>>10</option>
                <option value="20" <?= (isset($limit) && $limit == 20) ? 'selected' : '' ?>>20</option>
                <option value="50" <?= (isset($limit) && $limit == 50) ? 'selected' : '' ?>>50</option>
                <option value="100" <?= (isset($limit) && $limit == 100) ? 'selected' : '' ?>>100</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-secondary">Filtrar</button>
        </div>
    </form>
    <div class="mb-3">
        <strong>Saldo actual: </strong> <span class="text-success">C$ <?= number_format($saldo, 2) ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($movimientos && count($movimientos) > 0): ?>
                    <?php foreach ($movimientos as $mov): ?>
                        <tr>
                            <td><?= htmlspecialchars($mov['Fecha_Hora']) ?></td>
                            <td><?= htmlspecialchars($mov['Tipo']) ?></td>
                            <td class="text-end">C$ <?= number_format($mov['Monto'], 2) ?></td>
                            <td><?= htmlspecialchars($mov['Descripcion']) ?></td>
                            <td><?= htmlspecialchars($mov['Nombre_Usuario'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No hay movimientos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
    <nav aria-label="Paginación">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"> <?= $i ?> </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>
