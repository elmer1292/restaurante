<?php
require_once 'config/Session.php';
require_once 'models/UserModel.php';

Session::init();

if (Session::isLoggedIn()) {
    header('Location: /restaurante/');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $userModel = new UserModel();
        $user = $userModel->validateUser($username);

        if ($user && $userModel->verifyPassword($password, $user['Contrasenia'])) {
            // Obtener el ID del empleado
            $empleadoId = $userModel->getEmpleadoIdByUserId($user['ID_Usuario']);
            
            $_SESSION['user_id'] = $user['ID_Usuario'];
            $_SESSION['empleado_id'] = $empleadoId;
            $_SESSION['username'] = $user['Nombre_Usuario'];
            $_SESSION['user_role'] = $user['Nombre_Rol'];
            $_SESSION['nombre_completo'] = $user['Nombre_Completo'];

            header('Location: /restaurante/');
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    } else {
        $error = 'Por favor, complete todos los campos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo h1 {
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <h1>RestBar</h1>
                <p class="text-muted">Sistema de Gestión de Restaurante</p>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>