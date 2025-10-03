
    <h2>Apertura de Caja</h2>
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
    <?php if (!$cajaAbierta): ?>
    <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <div class="col-md-4">
            <label for="monto" class="form-label">Monto inicial</label>
            <input type="number" step="0.01" min="0.01" name="monto" id="monto" class="form-control" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Abrir Caja</button>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-info">La caja ya está abierta.</div>
    <?php endif; ?>

