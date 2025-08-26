// Función para abrir el modal de edición/creación y cargar datos si es edición
async function refreshCsrfToken(form) {
    try {
    const res = await fetch(BASE_URL + 'api/csrf_token.php');
        const data = await res.json();
        if (data.csrf_token) {
            form.querySelector('[name="csrf_token"]').value = data.csrf_token;
        }
    } catch (err) {
        console.error('Error al refrescar CSRF token', err);
    }
}

async function abrirEmpleadoModal(id = null) {
    const modal = new bootstrap.Modal(document.getElementById('empleadoModal'));
    const form = document.getElementById('empleadoForm');
    if (!form) return;
    await refreshCsrfToken(form);
    if (id) {
        // Edición: cargar datos por AJAX
    fetch(BASE_URL + `empleados/get?id=${id}`)
            .then(res => res.json())
            .then(data => {
                form.querySelector('[name="action"]').value = 'update';
                form.querySelector('[name="id"]').value = data.ID_usuario;
                form.querySelector('[name="nombre"]').value = data.Nombre_Completo;
                form.querySelector('[name="usuario"]').value = data.Nombre_Usuario;
                form.querySelector('[name="rol"]').value = data.ID_Rol;
                form.querySelector('[name="estado"]').value = data.Estado;
                form.querySelector('[name="password"]').value = '';
                form.querySelector('[name="password"]').required = false;
                modal.show();
            })
            .catch(err => {
                alert('Error al cargar datos del empleado');
                console.error(err);
            });
    } else {
    // Nuevo: limpiar formulario
    form.reset();
    form.querySelector('[name="action"]').value = 'create';
    form.querySelector('[name="id"]').value = '';
    form.querySelector('[name="correo"]').value = '';
    form.querySelector('[name="telefono"]').value = '';
    form.querySelector('[name="fecha_contratacion"]').value = new Date().toISOString().slice(0, 10);
    form.querySelector('[name="password"]').required = true;
    modal.show();
    }
}
window.abrirEmpleadoModal = abrirEmpleadoModal;

let empleadoAEliminar = null;
let estadoAEliminar = null;
function abrirEliminarModal(id, nuevoEstado = 0) {
    empleadoAEliminar = id;
    estadoAEliminar = nuevoEstado;
    const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    // Cambiar el texto del botón según la acción
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        if (nuevoEstado === 1) {
            confirmBtn.textContent = 'Activar';
            confirmBtn.classList.remove('btn-danger');
            confirmBtn.classList.add('btn-success');
        } else {
            confirmBtn.textContent = 'Eliminar';
            confirmBtn.classList.remove('btn-success');
            confirmBtn.classList.add('btn-danger');
        }
    }
    modal.show();
}
window.abrirEliminarModal = abrirEliminarModal;
window.abrirEmpleadoModal = abrirEmpleadoModal;
document.addEventListener('DOMContentLoaded', function() {
    var empleadoForm = document.getElementById('empleadoForm');
    if (empleadoForm) {
        empleadoForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            await refreshCsrfToken(form);
            const csrfInput = form.querySelector('[name="csrf_token"]');
            if (!csrfInput || !csrfInput.value) {
                alert('CSRF token faltante. Recargue la página.');
                return;
            }
            const datos = new FormData(form);
            fetch(BASE_URL + 'empleados/update', {
                method: 'POST',
                body: datos
            })
            .then(async res => {
                const text = await res.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error || 'Error al guardar empleado');
                    }
                } catch (e) {
                    alert('Respuesta inesperada del servidor:\n' + text);
                    console.error('Respuesta inesperada:', text);
                }
            })
        });
    }
    var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function() {
            if (!empleadoAEliminar) return;
            // Obtener el token CSRF actual
            const form = document.getElementById('empleadoForm');
            let csrfToken = '';
            if (form) {
                await refreshCsrfToken(form);
                const csrfInput = form.querySelector('[name="csrf_token"]');
                csrfToken = csrfInput ? csrfInput.value : '';
            }
            // Acción: activar si estadoAEliminar=1, eliminar si estadoAEliminar=0
            const accion = estadoAEliminar === 1 ? 'activar' : 'delete';
            const body = `action=${accion}&id=${empleadoAEliminar}&estado=${estadoAEliminar}&csrf_token=${encodeURIComponent(csrfToken)}`;
            fetch(BASE_URL + 'empleados/delete', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: body
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.error || 'Error al eliminar/activar empleado');
                }
            })
            .catch(err => {
                alert('Error al eliminar/activar empleado');
                console.error(err);
            });
        });
    }
});
