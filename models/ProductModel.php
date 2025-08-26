<?php
require_once __DIR__ . '/../config/database.php';

class ProductModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAllProducts() {
        try {
            error_log('Obteniendo todos los productos...');
            $query = 'SELECT p.*, c.Nombre_Categoria 
                     FROM productos p 
                     LEFT JOIN categorias c ON p.ID_Categoria = c.ID_Categoria 
                     ORDER BY p.Nombre_Producto';
            
            error_log('Ejecutando consulta: ' . $query);
            $stmt = $this->conn->query($query);
            
            if (!$stmt) {
                error_log('Error en la consulta: ' . print_r($this->conn->errorInfo(), true));
                return [];
            }
            
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Productos encontrados: ' . count($productos));
            if (count($productos) > 0) {
                error_log('Muestra del primer producto: ' . print_r($productos[0], true));
            }
            
            return $productos;
        } catch (PDOException $e) {
            error_log('Error en getAllProducts: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    public function getAllCategories() {
        try {
            error_log('Obteniendo todas las categorías...');
            $query = 'SELECT * FROM categorias ORDER BY Nombre_Categoria';
            
            error_log('Ejecutando consulta: ' . $query);
            $stmt = $this->conn->query($query);
            
            if (!$stmt) {
                error_log('Error en la consulta de categorías: ' . print_r($this->conn->errorInfo(), true));
                return [];
            }
            
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Categorías encontradas: ' . count($categorias));
            if (count($categorias) > 0) {
                error_log('Muestra de la primera categoría: ' . print_r($categorias[0], true));
            }
            
            return $categorias;
        } catch (PDOException $e) {
            error_log('Error en getAllCategories: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    public function addProduct($nombre, $precioCosto, $precioVenta, $idCategoria, $stock) {
        try {
            $stmt = $this->conn->prepare('CALL sp_AddProduct(?, ?, ?, ?, ?)');
            $stmt->execute([
                $nombre,
                $precioCosto,
                $precioVenta,
                $idCategoria,
                $stock
            ]);
            return true;
        } catch (PDOException $e) {
            error_log('Error en addProduct: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($idProducto, $nombre, $precioCosto, $precioVenta, $idCategoria, $stock) {
        try {
            $stmt = $this->conn->prepare('CALL sp_UpdateProduct(?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $idProducto,
                $nombre,
                $precioCosto,
                $precioVenta,
                $idCategoria,
                $stock
            ]);
            return true;
        } catch (PDOException $e) {
            error_log('Error en updateProduct: ' . $e->getMessage());
            return false;
        }
    }

    public function getProductsByCategory($idCategoria) {
        try {
            $stmt = $this->conn->prepare('SELECT * FROM productos WHERE ID_Categoria = ?');
            $stmt->execute([$idCategoria]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getProductsByCategory: ' . $e->getMessage());
            return false;
        }
    }
        /**
     * Devuelve el total de productos en la base de datos.
     * @return int
     */
    public function getTotalProducts() {
        try {
            $stmt = $this->conn->query('SELECT COUNT(*) as total FROM productos');
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['total'] : 0;
        } catch (PDOException $e) {
            error_log('Error en getTotalProducts: ' . $e->getMessage());
            return 0;
        }
    }
}