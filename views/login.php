<?php
// La variable $error viene del controlador AuthController
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Restaurante</title>
    <link href="<?php BASE_URL ?>assets/bootstrap/bootstrap-5.3.0-dist/css/bootstrap.min.css" rel="stylesheet">
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
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="<?= BASE_URL ?>login">
                <?php require_once __DIR__ . '/../helpers/Csrf.php'; ?>
                <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
                    <?php
                    // Generar token CSRF si no existe
                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }
                    ?>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
    <script src="<?php BASE_URL ?>assets/bootstrap/bootstrap-5.3.0-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>