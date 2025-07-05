<?php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function validateUser($username) {
        try {
            $stmt = $this->conn->prepare('CALL sp_ValidateUser(?)');
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en validateUser: ' . $e->getMessage());
            return false;
        }
    }

    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    public function checkUserExists($username) {
        try {
            $stmt = $this->conn->prepare('CALL sp_CheckUserExists(?, @exists)');
            $stmt->execute([$username]);
            $stmt->closeCursor();
            
            $result = $this->conn->query('SELECT @exists AS existe')->fetch(PDO::FETCH_ASSOC);
            return $result['existe'] == 1;
        } catch (PDOException $e) {
            error_log('Error en checkUserExists: ' . $e->getMessage());
            return false;
        }
    }

    public function createUserWithEmployee($userData) {
        try {
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare('CALL sp_CreateUserWithEmployee(?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $userData['username'],
                $hashedPassword,
                $userData['role_id'],
                $userData['nombre_completo'],
                $userData['correo'],
                $userData['telefono'],
                $userData['fecha_contratacion']
            ]);
            return true;
        } catch (PDOException $e) {
            error_log('Error en createUserWithEmployee: ' . $e->getMessage());
            return false;
        }
    }

    public function updateUser($userId, $username, $password = null, $roleId) {
        try {
            if ($password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            } else {
                $hashedPassword = null;
            }

            $stmt = $this->conn->prepare('CALL sp_UpdateUser(?, ?, ?, ?)');
            $stmt->execute([$userId, $username, $hashedPassword, $roleId]);
            return true;
        } catch (PDOException $e) {
            error_log('Error en updateUser: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers() {
        try {
            $query = "SELECT u.ID_Usuario, e.Nombre_Completo as Nombre, u.Nombre_Usuario as Usuario, 
                     r.Nombre_Rol as Rol, e.Estado 
                     FROM Usuarios u 
                     INNER JOIN Empleados e ON u.ID_Usuario = e.ID_Usuario 
                     INNER JOIN Roles r ON u.ID_Rol = r.ID_Rol";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getAllUsers: ' . $e->getMessage());
            return [];
        }
    }

    public function deleteUser($userId) {
        try {
            $query = "UPDATE Empleados SET Estado = 0 WHERE ID_Usuario = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            return true;
        } catch (PDOException $e) {
            error_log('Error en deleteUser: ' . $e->getMessage());
            return false;
        }
    }

    public function getUserById($userId) {
        try {
            $query = "SELECT u.ID_Usuario, e.Nombre_Completo, u.Nombre_Usuario, 
                     r.ID_Rol, r.Nombre_Rol, e.Estado 
                     FROM Usuarios u 
                     INNER JOIN Empleados e ON u.ID_Usuario = e.ID_Usuario 
                     INNER JOIN Roles r ON u.ID_Rol = r.ID_Rol 
                     WHERE u.ID_Usuario = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getUserById: ' . $e->getMessage());
            return false;
        }
    }
}