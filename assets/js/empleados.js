// Función para editar empleado
function editarEmpleado(id) {
    fetch(`../../controllers/UserController.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('empleadoId').value = data.ID_Usuario;
            document.getElementById('nombre').value = data.Nombre;
            document.getElementById('usuario').value = data.Usuario;
            document.getElementById('rol').value = data.Rol;
            document.getElementById('estado').value = data.Estado;
            
            document.querySelector('#empleadoForm [name="action"]').value = 'update';
            document.getElementById('password').required = false;
            
            const modal = new bootstrap.Modal(document.getElementById('empleadoModal'));
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