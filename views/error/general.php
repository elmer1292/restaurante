<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - RestBar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #6c757d; /* Default error color */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h2 class="mb-4">Ha ocurrido un error</h2>
            <p><?php echo htmlspecialchars($message ?? 'Ha ocurrido un error inesperado. Por favor, intente de nuevo más tarde.'); ?></p>
            <a href="/" class="btn btn-primary">Volver al Inicio</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>