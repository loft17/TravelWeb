// Esperar a que el DOM esté completamente cargado antes de ejecutar el script
document.addEventListener('DOMContentLoaded', () => {
    // Obtener referencias al botón de hamburguesa y al menú
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu');

    // Verificar si los elementos existen (para evitar errores)
    if (!menuToggle || !menu) {
        console.error('Elementos .menu-toggle o .menu no encontrados.');
        return; // Salir si alguno de los elementos no existe
    }

    // Alternar la clase "active" en el menú al hacer clic en el botón de hamburguesa
    menuToggle.addEventListener('click', (event) => {
        event.stopPropagation(); // Evitar que el clic se propague fuera del menú
        menu.classList.toggle('active'); // Agregar/quitar la clase "active"
    });

    // Cerrar el menú al hacer clic fuera de él
    document.addEventListener('click', (event) => {
        // Si el clic ocurre fuera del menú y no en el botón de hamburguesa
        if (!menu.contains(event.target) && event.target !== menuToggle) {
            menu.classList.remove('active'); // Quitar la clase "active"
        }
    });

    // Funcionalidad para atracciones
    const atracciones = document.querySelectorAll('.atraccion');
    atracciones.forEach(atraccion => {
        const titulo = atraccion.querySelector('.titulo-atraccion h2');
        const info = atraccion.querySelector('.info');
        const checkIcon = atraccion.querySelector('.check-icon');

        // Mostrar/ocultar información al hacer clic en el título
        titulo.addEventListener('click', () => {
            // Cerrar todas las demás informaciones abiertas
            atracciones.forEach(a => {
                const otherInfo = a.querySelector('.info');
                if (otherInfo !== info && otherInfo.classList.contains('active')) {
                    otherInfo.classList.remove('active');
                }
            });
            // Alternar la visualización de la información actual
            info.classList.toggle('active');
        });

        // Marcar como "visto" o "no visto"
        checkIcon.addEventListener('click', (e) => {
            e.stopPropagation(); // Evitar que se propague el clic al título
            toggleVisto(checkIcon.dataset.id, checkIcon.classList.contains('checked'));
        });
    });
});

// Función para marcar como "visto" o "no visto"
function toggleVisto(id, currentVisto) {
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=toggle_visto&id=${id}&visto=${currentVisto}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const checkIcon = document.querySelector(`[data-id="${id}"]`);
            checkIcon.classList.toggle('checked', data.visto);
        }
    })
    .catch(error => {
        console.error('Error al cambiar el estado "visto":', error);
    });
}