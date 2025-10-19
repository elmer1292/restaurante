// JS para la gestión de productos
$(document).ready(function() {
    // Inline Spanish translations to avoid external XHR (CORS) when serving locally
    $('#productosTable').DataTable({
        language: {
            emptyTable: "No hay datos disponibles en la tabla",
            info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            infoEmpty: "Mostrando 0 a 0 de 0 entradas",
            infoFiltered: "(filtrado de _MAX_ entradas totales)",
            lengthMenu: "Mostrar _MENU_ entradas",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron registros coincidentes",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna ascendente",
                sortDescending: ": activar para ordenar la columna descendente"
            }
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
