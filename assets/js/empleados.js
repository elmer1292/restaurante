// Función para editar empleado
function editarEmpleado(id) {
    const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
    fetch(`/restaurante/empleados/get?id=${id}`, {
        headers: csrfToken ? {'X-CSRF-Token': csrfToken} : {}
    })
        .then(response => response.json())
        .then(data => {
            document.getElementById('empleadoId').value = data.ID_usuario;
            document.getElementById('nombre').value = data.Nombre_Completo;
            document.getElementById('usuario').value = data.Nombre_Usuario;
            // Asignar el nombre del rol en vez del ID
            document.getElementById('rol').value = data.Nombre_Rol;
            document.getElementById('estado').value = data.Estado;
            document.querySelector('#empleadoForm [name="action"]').value = 'update';
            document.getElementById('password').required = false;
            // Abrir el modal usando Bootstrap 5 API puro
            var modal = new bootstrap.Modal(document.getElementById('empleadoModal'));
            modal.show();
        })
        .catch(error => console.error('Error:', error));
}

// Función para eliminar empleado
function eliminarEmpleado(id) {
    if (confirm('¿Está seguro de que desea eliminar este empleado?')) {
        fetch('../../controllers/UserController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&id=${id}`
        })
        .then(response => response.text())
        .then(() => window.location.reload())
        .catch(error => console.error('Error:', error));
    }
}

// Limpiar formulario cuando se abre el modal para crear nuevo empleado
document.getElementById('empleadoModal').addEventListener('show.bs.modal', function(event) {
    if (!event.relatedTarget.hasAttribute('data-edit')) {
        document.getElementById('empleadoForm').reset();
        document.querySelector('#empleadoForm [name="action"]').value = 'create';
        document.getElementById('empleadoId').value = '';
        document.getElementById('password').required = true;
    }
});