<?php
require_once 'config/Session.php';
require_once 'models/VentaModel.php';

Session::init();

$ventaModel = new VentaModel();
$comandas = $ventaModel->getVentasPendientesCocina();

// Agrupar comandas por mesa
$comandasPorMesa = [];
foreach ($comandas as $comanda) {
    $idMesa = $comanda['ID_Mesa'];
    if (!isset($comandasPorMesa[$idMesa])) {
        $comandasPorMesa[$idMesa] = [
            'numero_mesa' => $comanda['Numero_Mesa'],
            'fecha_hora' => $comanda['Fecha_Hora'],
            'items' => []
        ];
    }
    $comandasPorMesa[$idMesa]['items'][] = $comanda;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Cocina - RestBar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .comanda-card {
            transition: all 0.3s ease;
        }
        .comanda-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .tiempo-espera {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .mesa-numero {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .item-cantidad {
            font-weight: bold;
            color: #dc3545;
        }
        .refresh-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        @media (min-width: 768px) {
            .comandas-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 1rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'views/shared/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Panel de Cocina</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-primary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Actualizar
                        </button>
                    </div>
                </div>

                <div class="comandas-grid">
                    <?php foreach ($comandasPorMesa as $idMesa => $comanda): ?>
                    <div class="comanda-card card mb-3">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
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
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($comanda['items'] as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
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

    <button class="btn btn-primary btn-lg rounded-circle refresh-button" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Actualizar la página cada 30 segundos
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>