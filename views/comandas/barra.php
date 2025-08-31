<?php
// Ahora $comandasPorMesa viene del controlador
?>
    <div class="container-fluid">
        <div class="row">

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Panel de Barra</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-success" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Actualizar
                        </button>
                    </div>
                </div>

                <div class="comandas-grid">
                    <?php foreach ($comandasPorMesa as $idMesa => $comanda): ?>
                    <div class="comanda-card card mb-3">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <span class="mesa-numero">Mesa <?php echo htmlspecialchars($comanda['numero_mesa']); ?></span>
                            <small class="tiempo-espera">
                                <?php 
                                $tiempo = new DateTime($comanda['fecha_hora']);
                                $ahora = new DateTime();
                                $diferencia = $ahora->diff($tiempo);
                                if ($diferencia->h > 0) {
                                    echo $diferencia->format('%h h %i min');
                                } else {
                                    echo $diferencia->format('%i min');
                                }
                                ?>
                            </small>
                        </div>
                        <button class="btn btn-outline-info mt-3" onclick="imprimirComandaBarra(<?php echo $idMesa; ?>)"><i class="bi bi-printer"></i> Imprimir Comanda</button>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($comanda['items'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                                    <div>
                                        <span class="item-cantidad"><?php echo $item['Cantidad']; ?>x</span>
                                        <?php echo htmlspecialchars($item['Nombre_Producto']); ?>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($comandasPorMesa)): ?>
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle"></i> No hay comandas pendientes en este momento.
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <button class="btn btn-success btn-lg rounded-circle refresh-button" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise"></i>
    </button>

    <script src="<?php BASE_URL ?>assets/bootstrap/bootstrap-5.3.0-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Actualizar la página cada 30 segundos
        setInterval(() => {
            location.reload();
        }, 30000);

        function imprimirComandaBarra(idMesa) {
            fetch('<?php echo BASE_URL; ?>comandas/imprimirComanda', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_mesa: idMesa, tipo: 'barra' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Comanda enviada a la impresora de barra.');
                } else {
                    alert(data.error || 'Error al imprimir comanda.');
                }
            })
            .catch(err => {
                alert('Error de comunicación: ' + err);
            });
        }
    </script>

<?php
// Procesar marcado de producto como preparado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_preparado'])) {
    // Eliminado: ahora el cambio de estado se hace por AJAX
}
?>
