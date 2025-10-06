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
            <div class="input-group mt-2">
                <input type="text" class="form-control" id="impresora_cocina" name="impresora_cocina" placeholder="Nombre o puerto" value="<?php echo htmlspecialchars($config['impresora_cocina'] ?? ''); ?>">
                <button type="button" class="btn btn-outline-secondary" onclick="buscarImpresoras('cocina')">Buscar impresoras</button>
            </div>
            <div id="listaImpresorasCocina" class="mt-2"></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Impresora de Barra</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="usar_impresora_barra" name="usar_impresora_barra" value="1" <?php echo ($config['usar_impresora_barra'] ?? '0') == '1' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="usar_impresora_barra">Usar impresora de barra</label>
            </div>
            <div class="input-group mt-2">
                <input type="text" class="form-control" id="impresora_barra" name="impresora_barra" placeholder="Nombre o puerto" value="<?php echo htmlspecialchars($config['impresora_barra'] ?? ''); ?>">
                <button type="button" class="btn btn-outline-secondary" onclick="buscarImpresoras('barra')">Buscar impresoras</button>
            </div>
            <div id="listaImpresorasBarra" class="mt-2"></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Impresora de ticket</label>
            <div class="input-group mt-2">
                <input type="text" class="form-control" id="impresora_ticket" name="impresora_ticket" placeholder="Nombre o puerto" value="<?php echo htmlspecialchars($config['impresora_ticket'] ?? ''); ?>">
                <button type="button" class="btn btn-outline-secondary" onclick="buscarImpresoras('ticket')">Buscar impresoras</button>
            </div>
            <div id="listaImpresorasTicket" class="mt-2"></div>
        </div>
        <!-- <div class="mb-3">
            <label for="moneda" class="form-label">Símbolo de la moneda</label>
            <input type="text" class="form-control" id="moneda" name="moneda" value="<php echo htmlspecialchars($config['moneda'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="iva" class="form-label">Porcentaje de IVA (%)</label>
            <input type="number" step="0.01" class="form-control" id="iva" name="iva" value="<php echo htmlspecialchars($config['iva'] ?? ''); ?>" required>
        </div> -->
        <div class="mb-3">
            <label for="servicio" class="form-label">Porcentaje de Servicio (<?php echo htmlspecialchars($config['servicio']*100 ?? ''); ?>%)</label>
            <input type="number" step="0.01" class="form-control" id="servicio" name="servicio" value="<?php echo htmlspecialchars($config['servicio'] ?? ''); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar configuración</button>
        <a href="<?php echo BASE_URL; ?>config/backup" class="btn btn-success ms-2">Respaldar Base de Datos</a>
    </form>
</div>
<script>
function buscarImpresoras(tipo) {
    fetch('<?php echo BASE_URL; ?>configuracion/buscarImpresoras', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ tipo })
    })
    .then(res => res.json())
    .then(data => {
        let lista = '';
        if (Array.isArray(data.impresoras) && data.impresoras.length > 0) {
            lista = '<ul class="list-group">' + data.impresoras.map(function(nombre) {
                return `<li class="list-group-item list-group-item-action" onclick=\"seleccionarImpresora('${tipo}', '${nombre.replace(/'/g, "\\'")}')\">${nombre}</li>`;
            }).join('') + '</ul>';
        } else if (data.error) {
            lista = `<div class="alert alert-danger">Error: ${data.error}</div>`;
        } else {
            lista = '<div class="alert alert-warning">No se encontraron impresoras.</div>';
        }
        if (tipo === 'cocina') {
            document.getElementById('listaImpresorasCocina').innerHTML = lista;
        } else {
            document.getElementById('listaImpresorasBarra').innerHTML = lista;
        }
    })
    .catch(err => {
        let msg = `<div class="alert alert-danger">Error al buscar impresoras: ${err}</div>`;
        if (tipo === 'cocina') {
            document.getElementById('listaImpresorasCocina').innerHTML = msg;
        } else {
            document.getElementById('listaImpresorasBarra').innerHTML = msg;
        }
    });
}
function seleccionarImpresora(tipo, nombre) {
    if (tipo === 'cocina') {
        document.getElementById('impresora_cocina').value = nombre;
        document.getElementById('listaImpresorasCocina').innerHTML = '';
    } else {
        document.getElementById('impresora_barra').value = nombre;
        document.getElementById('listaImpresorasBarra').innerHTML = '';
    }
}
</script>
