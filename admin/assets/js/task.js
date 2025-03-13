function editarTarea(tarea){
    document.getElementById('formTarea').accion.value='editar';
    document.getElementById('formTarea').id.value=tarea.id;
    document.getElementById('formTarea').titulo.value=tarea.titulo;
    document.getElementById('formTarea').fecha_inicio.value=tarea.fecha_inicio;
    document.getElementById('formTarea').fecha_fin.value=tarea.fecha_fin;
    document.getElementById('formTarea').info.value=tarea.info;
    document.getElementById('formTarea').url.value=tarea.url;
}

function limpiarForm(){
    document.getElementById('formTarea').reset();
    document.getElementById('formTarea').accion.value='crear';
    document.getElementById('formTarea').id.value='';
}