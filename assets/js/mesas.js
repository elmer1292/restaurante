// JS para la gestión de mesas
// Nota: El campo 'estado' aquí solo se refiere al estado de la mesa (ocupada/libre), no al estado de productos.
$(document).ready(function() {
    $('.edit-mesa').click(function(e) {
        e.stopPropagation();
        $('#modalTitle').text('Editar Mesa');
        $('#id_mesa').val($(this).data('id'));
        $('#numero_mesa').val($(this).data('numero'));
        $('#capacidad').val($(this).data('capacidad'));
        $('#mesaModal').modal('show');
    });

    $('.toggle-estado').click(function(e) {
        e.stopPropagation();
        const idMesa = $(this).data('id');
        const estadoActual = $(this).data('estado');
        const nuevoEstado = estadoActual ? 0 : 1;
        const csrf = document.getElementById('csrf_token_mesas') ? document.getElementById('csrf_token_mesas').value : '';
        $.post(BASE_URL + 'mesas/procesar_mesa', {
            action: 'toggle_estado',
            id_mesa: idMesa,
            estado: nuevoEstado,
            csrf_token: csrf
        }, function(response) {
            location.reload();
        });
    });

    $('#mesaModal').on('hidden.bs.modal', function() {
        $('#modalTitle').text('Nueva Mesa');
        $('#mesaForm').trigger('reset');
        $('#id_mesa').val('');
    });

    // Eliminado el evento click automático en la card de la mesa
});
