// Función para mostrar mensajes de alerta personalizados
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.body.appendChild(alertDiv);

    // Eliminar la alerta después de 3 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Función para confirmar acciones
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Función para formatear moneda
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(amount);
}

// Función para formatear fecha y hora
function formatDateTime(dateString) {
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('es-MX', options);
}

// Función para validar formularios
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// Función para manejar errores en peticiones fetch
function handleFetchError(error) {
    console.error('Error:', error);
    showAlert('Ha ocurrido un error. Por favor, inténtelo de nuevo más tarde.', 'danger');
}

// Función para realizar peticiones fetch
async function fetchData(url, options = {}) {
    // Obtener el token CSRF desde una variable global o meta tag
    const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...(csrfToken ? {'X-CSRF-Token': csrfToken} : {}),
                ...options.headers
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        handleFetchError(error);
        throw error;
    }
}

// Función para actualizar el estado de una mesa
async function updateTableStatus(tableId, status) {
    try {
        const response = await fetchData('views/mesas/procesar_mesa.php', {
            method: 'POST',
            body: JSON.stringify({
                id_mesa: tableId,
                estado: status
            })
        });

        if (response.success) {
            showAlert('Estado de la mesa actualizado correctamente');
            return true;
        } else {
            showAlert(response.message || 'Error al actualizar el estado de la mesa', 'danger');
            return false;
        }
    } catch (error) {
        handleFetchError(error);
        return false;
    }
}

// Función para inicializar DataTables con configuración común
function initDataTable(tableId, options = {}) {
    return new DataTable(`#${tableId}`, {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        },
        responsive: true,
        pageLength: 10,
        ...options
    });
}

// Función para manejar la carga de imágenes
function handleImageUpload(inputElement, previewElement) {
    inputElement.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewElement.src = e.target.result;
                previewElement.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
}

// Función para calcular el tiempo transcurrido
function getTimeElapsed(dateString) {
    const start = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - start) / 1000); // diferencia en segundos

    if (diff < 60) return `${diff} segundos`;
    if (diff < 3600) return `${Math.floor(diff / 60)} minutos`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} horas`;
    return `${Math.floor(diff / 86400)} días`;
}

// Inicializar tooltips de Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});