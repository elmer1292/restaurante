<?php

require_once 'BaseController.php';

class ProductoController extends BaseController {
    public function index() {
        // Cargar productos y categorías
        $productModel = new ProductModel();
        $productos = $productModel->getAllProducts();
        $categorias = $productModel->getAllCategories();
        $params = [
            'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
            'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : 10
        ];
        $totalProductos = count($productos);
        $mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
        unset($_SESSION['mensaje']);
        $this->render('views/productos/index.php', compact('productos', 'categorias', 'params', 'totalProductos', 'mensaje'));
    }

    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = new ProductModel();
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!Csrf::validateToken($csrfToken)) {
                $_SESSION['mensaje'] = 'Token CSRF inválido.';
                header('Location: ' . BASE_URL . 'productos');
                exit;
            }
            $id = $_POST['id_producto'] ?? null;
            $nombre = trim($_POST['nombre'] ?? '');
            $categoria = $_POST['categoria'] ?? null;
            $precio_costo = $_POST['precio_costo'] ?? null;
            $precio_venta = $_POST['precio_venta'] ?? null;
            $stock = $_POST['stock'] ?? null;
            if ($id) {
                // Editar producto
                $result = $productModel->updateProduct($id, $nombre, $precio_costo, $precio_venta, $categoria, $stock);
                $_SESSION['mensaje'] = $result ? 'Producto actualizado correctamente.' : 'Error al actualizar el producto.';
            } else {
                // Crear producto
                $result = $productModel->addProduct($nombre, $precio_costo, $precio_venta, $categoria, $stock);
                $_SESSION['mensaje'] = $result ? 'Producto creado correctamente.' : 'Error al crear el producto.';
            }
            header('Location: ' . BASE_URL . 'productos');
            exit;
        }
    }
}
