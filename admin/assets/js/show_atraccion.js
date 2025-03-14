$(document).ready(function() {
    // Inicializar DataTable
    var dataTable = $('#dataTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [ [10, 20, 50, 100], [10, 20, 50, 100] ],
        "order": [[0, "asc"]],
        "columnDefs": [
            // Deshabilita el ordenamiento en todas las columnas excepto la 0 ("Orden")
            { "orderable": false, "targets": [1,2,3,4,5,6,7,8,9,10,11,12] }
        ],
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });

    // Hacer sortable el tbody de la tabla para reordenar filas
    $("#dataTable tbody").sortable({
        update: function(event, ui) {
            var newOrder = [];
            $("#dataTable tbody tr").each(function(index) {
                var id = $(this).data("id");
                newOrder.push({ id: id, orden: index + 1 });
                // Actualizar el número de orden en la primera celda
                $(this).find('td:eq(0)').text(index + 1);
            });

            // Enviar el nuevo orden al servidor mediante AJAX
            $.ajax({
                url: "/admin/includes/functions/update_order.php",
                method: "POST",
                data: { order: newOrder },
                success: function(response) {
                    console.log("Orden actualizado correctamente.");
                },
                error: function() {
                    console.error("Error al actualizar el orden.");
                }
            });
        }
    }).disableSelection();
});
