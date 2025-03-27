$(document).ready(function(){
    // Inicializar Quill con toolbar restringido a negrita, cursiva y color.
    var toolbarOptions = [
        ['bold', 'italic'],
        [{ 'color': [] }]
    ];
    var quillCiudad = new Quill('#ciudadEditor', {
        modules: { toolbar: toolbarOptions },
        theme: 'snow'
    });
    var quillVisitaManana = new Quill('#visitaMananaEditor', {
        modules: { toolbar: toolbarOptions },
        theme: 'snow'
    });
    var quillVisitaTarde = new Quill('#visitaTardeEditor', {
        modules: { toolbar: toolbarOptions },
        theme: 'snow'
    });
    var quillVisitaNoche = new Quill('#visitaNocheEditor', {
        modules: { toolbar: toolbarOptions },
        theme: 'snow'
    });
    
    // Al hacer clic en una celda del calendario, rellenar el modal con los datos almacenados en data-*.
    $("table.table td").on("click", function(){
        var fecha = $(this).data("date");
        // Si no hay fecha, no se abre el modal.
        if(typeof fecha === "undefined" || fecha === "") {
        return;
        }


        var ciudad = $(this).data("ciudad");
        var visita_manana = $(this).data("visita_manana");
        var visita_tarde = $(this).data("visita_tarde");
        var visita_noche = $(this).data("visita_noche");

        $("#eventDate").val(fecha);
        quillCiudad.root.innerHTML = ciudad;
        quillVisitaManana.root.innerHTML = visita_manana;
        quillVisitaTarde.root.innerHTML = visita_tarde;
        quillVisitaNoche.root.innerHTML = visita_noche;

        $("#eventModal").modal("show");
    });

    // Al enviar el formulario, copiar el contenido de cada Quill en los inputs ocultos.
    $("#eventForm").on("submit", function(){
        $("#ciudadInput").val(quillCiudad.root.innerHTML);
        $("#visitaMananaInput").val(quillVisitaManana.root.innerHTML);
        $("#visitaTardeInput").val(quillVisitaTarde.root.innerHTML);
        $("#visitaNocheInput").val(quillVisitaNoche.root.innerHTML);
    });
});