<?php
// views/mesas/dividir_cuenta.php
// Vista para dividir la cuenta de una mesa en parciales
require_once dirname(__DIR__, 2) . '/config/base_url.php';
require_once dirname(__DIR__, 2) . '/models/VentaModel.php';

$id_mesa = isset($_GET['id_mesa']) ? (int)$_GET['id_mesa'] : null;
if (!$id_mesa) {
    echo '<div class="alert alert-danger">ID de mesa no especificado.</div>';
    exit;
}
$ventaModel = new VentaModel();
$comanda = $ventaModel->getVentaActivaByMesa($id_mesa);
if (!$comanda) {
    echo '<div class="alert alert-warning">No hay comanda activa para esta mesa.</div>';
    exit;
}
$detalles = $ventaModel->getSaleDetails($comanda['ID_Venta']);
if (!$detalles) {
    echo '<div class="alert alert-info">No hay productos en la mesa.</div>';
    exit;
}
?>
<div class="container py-4">
    <h2>Dividir Cuenta - Mesa #<?= htmlspecialchars($id_mesa) ?></h2>
    <form id="formDividirCuenta" method="post">
        <input type="hidden" name="id_mesa" value="<?= htmlspecialchars($id_mesa) ?>">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad total</th>
                    <th colspan="3">Asignar a parciales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $detalle): ?>
                <tr>
                    <td><?= htmlspecialchars($detalle['Nombre_Producto']) ?></td>
                    <td><?= (int)$detalle['Cantidad'] ?></td>
                    <td>
                        <input type="number" name="parcial1[<?= (int)$detalle['ID_Detalle'] ?>]" min="0" max="<?= (int)$detalle['Cantidad'] ?>" value="0" class="form-control form-control-sm" placeholder="Parcial 1">
                    </td>
                    <td>
                        <input type="number" name="parcial2[<?= (int)$detalle['ID_Detalle'] ?>]" min="0" max="<?= (int)$detalle['Cantidad'] ?>" value="0" class="form-control form-control-sm" placeholder="Parcial 2">
                    </td>
                    <td>
                        <input type="number" name="parcial3[<?= (int)$detalle['ID_Detalle'] ?>]" min="0" max="<?= (int)$detalle['Cantidad'] ?>" value="0" class="form-control form-control-sm" placeholder="Parcial 3">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Guardar División</button>
            <a href="<?= BASE_URL ?>mesa?id_mesa=<?= $id_mesa ?>&csrf_token=<?= urlencode($csrf_token ?? Csrf::getToken()) ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<!-- Validación simple en JS: la suma de parciales debe ser igual a la cantidad total -->
<script>
document.getElementById('formDividirCuenta').addEventListener('submit', function(e) {
    let ok = true;
    <?php foreach ($detalles as $detalle): ?>
    {
        let total = <?= (int)$detalle['Cantidad'] ?>;
        let suma = 0;
        for (let i = 1; i <= 3; i++) {
            let input = document.querySelector('[name="parcial'+i+'[<?= (int)$detalle['ID_Detalle'] ?>]"]');
            if (input) suma += parseInt(input.value) || 0;
        }
        if (suma !== total) {
            alert('Debes asignar todas las unidades de <?= addslashes($detalle['Nombre_Producto']) ?> (asignadas: '+suma+' de '+total+')');
            ok = false;
        }
    }
    <?php endforeach; ?>
    if (!ok) e.preventDefault();
});
</script>
