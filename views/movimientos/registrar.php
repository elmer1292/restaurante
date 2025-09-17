<?php require_once dirname(__DIR__, 2) . '/config/base_url.php'; ?>
<div class="container py-4">
    <h2>Registrar Movimiento</h2>
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="col-md-4">
            <label for="tipo" class="form-label">Tipo de Movimiento</label>
            <select name="tipo" id="tipo" class="form-select" required>
                <option value="">Seleccione...</option>
                <option value="Ingreso" <?= ($tipo ?? '') === 'Ingreso' ? 'selected' : '' ?>>Ingreso</option>
                <option value="Egreso" <?= ($tipo ?? '') === 'Egreso' ? 'selected' : '' ?>>Egreso</option>
                <option value="Apertura" <?= ($tipo ?? '') === 'Apertura' ? 'selected' : '' ?>>Apertura</option>
                <option value="Cierre" <?= ($tipo ?? '') === 'Cierre' ? 'selected' : '' ?>>Cierre</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="monto" class="form-label">Monto</label>
            <input type="number" step="0.01" min="0.01" name="monto" id="monto" class="form-control" value="<?= htmlspecialchars($monto ?? '') ?>" required>
        </div>
        <div class="col-md-8">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" maxlength="255" value="<?= htmlspecialchars($descripcion ?? '') ?>" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Registrar</button>
            <a href="<?= BASE_URL ?>movimientos" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
