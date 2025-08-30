<div class="container mt-4">
    <h2>Configuración de la Aplicación</h2>
    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <form method="POST" action="<?php echo BASE_URL; ?>configuracion/update">
        <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
        <div class="mb-3">
            <label for="nombre_app" class="form-label">Nombre de la aplicación</label>
            <input type="text" class="form-control" id="nombre_app" name="nombre_app" value="<?php echo htmlspecialchars($config['nombre_app'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Impresora de Cocina</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="usar_impresora_cocina" name="usar_impresora_cocina" value="1" <?php echo ($config['usar_impresora_cocina'] ?? '0') == '1' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="usar_impresora_cocina">Usar impresora de cocina</label>
            </div>
            <input type="text" class="form-control mt-2" id="impresora_cocina" name="impresora_cocina" placeholder="Nombre o puerto" value="<?php echo htmlspecialchars($config['impresora_cocina'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Impresora de Barra</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="usar_impresora_barra" name="usar_impresora_barra" value="1" <?php echo ($config['usar_impresora_barra'] ?? '0') == '1' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="usar_impresora_barra">Usar impresora de barra</label>
            </div>
            <input type="text" class="form-control mt-2" id="impresora_barra" name="impresora_barra" placeholder="Nombre o puerto" value="<?php echo htmlspecialchars($config['impresora_barra'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Guardar configuración</button>
        <a href="<?php echo BASE_URL; ?>config/backup" class="btn btn-success ms-2">Respaldar Base de Datos</a>
    </form>
</div>
