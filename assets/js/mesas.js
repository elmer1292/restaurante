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

        $.post('procesar_mesa.php', {
            action: 'toggle_estado',
            id_mesa: idMesa,
            estado: nuevoEstado
        }, function(response) {
            location.reload();
        });
    });

    $('#mesaModal').on('hidden.bs.modal', function() {
        $('#modalTitle').text('Nueva Mesa');
        $('#mesaForm').trigger('reset');
        $('#id_mesa').val('');
    });

    /* $('.mesa-card').click(function() {
        const idMesa = $(this).data('id');
        const estado = $(this).data('estado');
        if (estado === 0) {
            window.location.href = '../comandas/nueva.php?mesa=' + idMesa;
        } else {
            window.location.href = '../comandas/agregar_productos.php?mesa=' + idMesa;
        }
    }); */
});
