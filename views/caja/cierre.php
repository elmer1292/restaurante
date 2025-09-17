<?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
<div class="container py-4">
    <h2>Cierre de Caja</h2>
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success"> <?= htmlspecialchars($mensaje) ?> </div>
    <?php endif; ?>
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if ($cajaAbierta): ?>
    <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="col-md-4">
            <label class="form-label">Saldo actual</label>
            <input type="text" class="form-control" value="C$ <?= number_format($saldo, 2) ?>" readonly>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-danger">Cerrar Caja</button>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-info">No hay caja abierta para cerrar.</div>
    <?php endif; ?>
</div>
