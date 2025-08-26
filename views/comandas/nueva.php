
<!-- Controles de paginación -->
<nav aria-label="Paginación de productos">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i == $params['page'] ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $params['limit']; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php
require_once 'config/Session.php';
Session::init();
require_once 'models/MesaModel.php';
require_once 'models/ProductModel.php';
require_once 'models/VentaModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Pagination.php';
$params = getPageParams($_GET, 50);
$productos = $productModel->getAllProducts($params['offset'], $params['limit']);
$totalProductos = $productModel->getTotalProducts();
$totalPages = isset($totalProductos) && $params['limit'] > 0 ? ceil($totalProductos / $params['limit']) : 1;
// DEBUG: Mostrar el contenido de la sesión en pantalla
if (isset($_GET['debug'])) {
    echo '<pre>SESSION: ' . print_r($_SESSION, true) . '</pre>';
}

Session::checkRole(['Administrador', 'Mesero']);

$idMesa = isset($_GET['mesa']) ? (int)$_GET['mesa'] : 0;

if (!$idMesa) {
    header('Location: views/mesas/');
    exit();
}

$mesaModel = new MesaModel();
$mesa = $mesaModel->getTableById($idMesa);

if (!$mesa || $mesa['Estado'] == 1) {
    $_SESSION['mensaje'] = 'Mesa no disponible o inválida';
    header('Location: views/mesas/');
    exit();
}

$productModel = new ProductModel();
$categorias = $productModel->getAllCategories();
// ...existing code...

// Debug: Verificar los productos obtenidos
error_log('Productos obtenidos: ' . print_r($productos, true));

// Agrupar productos por categoría
$productosPorCategoria = [];
foreach ($productos as $producto) {
    error_log('Procesando producto: ' . print_r($producto, true));
    $idCategoria = $producto['ID_Categoria'];
    if (!isset($productosPorCategoria[$idCategoria])) {
        $productosPorCategoria[$idCategoria] = [];
    }
    $productosPorCategoria[$idCategoria][] = $producto;
}

// Debug: Verificar productos agrupados
foreach ($productosPorCategoria as $idCategoria => $prods) {
    error_log("Categoría $idCategoria tiene " . count($prods) . " productos");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Comanda - Mesa <?php echo $mesa['Numero_Mesa']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/restaurante/assets/css/styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'views/shared/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Nueva Comanda - Mesa <?php echo $mesa['Numero_Mesa']; ?></h1>
                </div>

                <div class="row">
                    <!-- Lista de Productos -->
                    <div class="col-md-8">
                        <div class="accordion" id="accordionProductos">
                            <?php foreach ($categorias as $categoria): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#categoria<?php echo $categoria['ID_Categoria']; ?>">
                                        <?php echo htmlspecialchars($categoria['Nombre_Categoria']); ?>
                                    </button>
                                </h2>
                                <div id="categoria<?php echo $categoria['ID_Categoria']; ?>" class="accordion-collapse collapse show" 
                                     data-bs-parent="#accordionProductos">
                                    <div class="accordion-body">
                                        <div class="row row-cols-1 row-cols-md-3 g-4">
                                            <?php 
                                            if (isset($productosPorCategoria[$categoria['ID_Categoria']])) {
                                                foreach ($productosPorCategoria[$categoria['ID_Categoria']] as $producto): 
                                            ?>
                                            <div class="col">
                                                <div class="card h-100 producto-card"
                                                     data-producto='<?= json_encode($producto, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                                                     onclick="void(0)">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></h5>
                                                        <p class="card-text">
                                                            Precio: $<?php echo number_format($producto['Precio_Venta'], 2); ?><br>
                                                            Stock: <?php echo $producto['Stock']; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php 
                                                endforeach; 
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Resumen de Comanda -->
                    <div class="col-md-4">
                        <div class="card comanda-preview">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Resumen de Comanda</h5>
                            </div>
                            <div class="card-body">
                                <div id="items-comanda"></div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h5>Total:</h5>
                                    <h5 id="total-comanda">$0.00</h5>
                                </div>
                                <button class="btn btn-primary w-100 mt-3" onclick="procesarComanda()">Procesar Comanda</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicialización de variables globales
        let comandaItems = {};
        let total = 0;

        // Agregar event listeners cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM cargado. Configurando event listeners...');
            
            // Contar las tarjetas de productos encontradas
            const cards = document.querySelectorAll('.producto-card');
            console.log('Tarjetas de productos encontradas:', cards.length);
            
            // Agregar listeners a todas las tarjetas de productos
            cards.forEach(card => {
                console.log('Configurando tarjeta:', card.querySelector('.card-title').textContent);
                
                // Usar mousedown en lugar de click para mejor respuesta
                card.addEventListener('mousedown', function(e) {
                    e.preventDefault(); // Prevenir comportamiento por defecto
                    e.stopPropagation(); // Detener propagación del evento
                    
                    console.log('Clic en tarjeta:', this.querySelector('.card-title').textContent);
                    
                    // Añadir clase para feedback visual
                    this.classList.add('clicked');
                    setTimeout(() => this.classList.remove('clicked'), 300);

                    try {
                        const productoData = this.dataset.producto;
                        console.log('Datos del producto (raw):', productoData);
                        const producto = JSON.parse(productoData);
                        agregarProducto(producto);
                    } catch (error) {
                        console.error('Error al procesar el producto:', error, 'Valor data-producto:', this.dataset.producto);
                        alert('Error al seleccionar el producto. Por favor, inténtelo de nuevo.');
                    }
                });

                // Prevenir el comportamiento por defecto en click también
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            // Verificar que los datos de productos están correctamente almacenados
            cards.forEach(card => {
                console.log('Datos almacenados en tarjeta:', card.dataset.producto);
            });
        });

        function agregarProducto(producto) {
            if (!producto || !producto.ID_Producto) {
                console.error('Producto inválido:', producto);
                return;
            }

            // Convertir campos numéricos a número
            producto.Precio_Venta = Number(producto.Precio_Venta);
            producto.Precio_Costo = Number(producto.Precio_Costo);
            producto.Stock = Number(producto.Stock);
            producto.ID_Producto = Number(producto.ID_Producto);
            producto.ID_Categoria = Number(producto.ID_Categoria);

            console.log('Producto recibido:', producto);
            const id = producto.ID_Producto;
            console.log('ID del producto:', id);

            if (!comandaItems[id]) {
                console.log('Añadiendo nuevo producto al carrito');
                comandaItems[id] = {
                    ...producto,
                    cantidad: 0
                };
            }
            comandaItems[id].cantidad++;
            console.log('Carrito actualizado:', comandaItems);
            actualizarComanda();
        }

        function eliminarProducto(id) {
            if (comandaItems[id].cantidad > 1) {
                comandaItems[id].cantidad--;
            } else {
                delete comandaItems[id];
            }
            actualizarComanda();
        }

        function actualizarComanda() {
            const container = document.getElementById('items-comanda');
            container.innerHTML = '';
            total = 0;

            Object.values(comandaItems).forEach(item => {
                const subtotal = item.cantidad * item.Precio_Venta;
                total += subtotal;

                container.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="mb-0">${item.Nombre_Producto}</h6>
                            <small class="text-muted">
                                ${item.cantidad} x $${item.Precio_Venta.toFixed(2)}
                            </small>
                        </div>
                        <div>
                            <span class="me-2">$${subtotal.toFixed(2)}</span>
                            <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${item.ID_Producto})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            document.getElementById('total-comanda').textContent = `$${total.toFixed(2)}`;
        }

        function procesarComanda() {
            if (Object.keys(comandaItems).length === 0) {
                alert('Agregue al menos un producto a la comanda');
                return;
            }

            const items = Object.values(comandaItems).map(item => ({
                id_producto: item.ID_Producto,
                cantidad: item.cantidad,
                precio_venta: item.Precio_Venta
            }));

            $.post('procesar_comanda.php', {
                id_mesa: <?php echo $idMesa; ?>,
                items: JSON.stringify(items)
            }, function(response) {
                if (response.success) {
                    alert('Comanda procesada exitosamente');
                    window.location.href = '../mesas/';
                } else {
                    alert('Error al procesar la comanda: ' + response.message);
                }
            }, 'json');
        }

        // Verificar el estado del procedimiento almacenado al cargar la página
        $(document).ready(function() {
            $.get('verificar_procedimiento.php', { nombre: 'sp_CreateSale' }, function(data) {
                if (data.existe) {
                    console.log('El procedimiento almacenado sp_CreateSale existe.');
                } else {
                    console.warn('El procedimiento almacenado sp_CreateSale NO existe.');
                }
            }, 'json');
        });
    <script src="/restaurante/assets/js/nueva_comanda.js"></script>
</body>
</html>

;