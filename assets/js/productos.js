// JS para la gestión de productos
$(document).ready(function() {
    $('#productosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        }
    });

    $('.edit-producto').click(function() {
        $('#modalTitle').text('Editar Producto');
        $('#id_producto').val($(this).data('id'));
        $('#nombre').val($(this).data('nombre'));
        $('#categoria').val($(this).data('categoria'));
        $('#precio_costo').val($(this).data('costo'));
        $('#precio_venta').val($(this).data('venta'));
        $('#stock').val($(this).data('stock'));
        $('#productoModal').modal('show');
    });

    $('#productoModal').on('hidden.bs.modal', function() {
        $('#modalTitle').text('Nuevo Producto');
        $('#productoForm').trigger('reset');
        $('#id_producto').val('');
    });
});
