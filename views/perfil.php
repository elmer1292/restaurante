<?php
require_once __DIR__ . '/../config/base_url.php';
require_once __DIR__ . '/../config/autoloader.php';
Session::init();
if (!Session::get('loggedIn')) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}
$usuario = isset($usuario) ? $usuario : [];
?>
<div class="container mt-4">
    <h2>Mi Perfil</h2>
    <form id="perfilForm" method="POST" action="<?= BASE_URL ?>user/update">
        <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['Nombre_Completo'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario['Nombre_Usuario'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($usuario['Correo'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['Telefono'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control" id="password" name="password">
            <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
    </form>
    <div id="perfilMsg" class="mt-3"></div>
</div>
<script>
document.getElementById('perfilForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const datos = new FormData(form);
    fetch(window.BASE_URL + 'user/update', {
        method: 'POST',
        body: datos
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.getElementById('perfilMsg');
        if (data.success) {
            msg.innerHTML = '<div class="alert alert-success">Perfil actualizado correctamente.</div>';
        } else {
            msg.innerHTML = '<div class="alert alert-danger">' + (data.error || 'Error al actualizar perfil') + '</div>';
        }
    })
    .catch(err => {
        alert('Error al actualizar perfil');
        console.error(err);
    });
});
</script>
