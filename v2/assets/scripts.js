// Script para que solo un <details> esté abierto a la vez
document.querySelectorAll('details').forEach((targetDetail) => {
    targetDetail.addEventListener("toggle", (event) => {
        if (targetDetail.open) {
            document.querySelectorAll('details').forEach((detail) => {
                if (detail !== targetDetail) {
                    detail.removeAttribute("open");
                }
            });
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.seen-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            console.log("Checkbox cambiado:", this.id, "nuevo estado:", this.checked);
            var id = this.id.replace('check_', '');
            // Si el checkbox acaba de quedar marcado, el estado anterior era 'false'
            var currentState = this.checked ? 'false' : 'true';
            
            fetch('../v2/includes/update_visto.php', {  // Asegúrate de que la ruta sea correcta
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'toggle_visto',
                    id: id,
                    visto: currentState
                })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    // Seleccionamos el ícono asociado al checkbox
                    var label = checkbox.nextElementSibling;
                    var icon = label.querySelector('.check-icon');
                    
                    if (data.visto) {
                        // Si se marca como "visto", agregamos la clase "checked" y cambiamos el ícono a "check_circle"
                        icon.classList.add('checked');
                        icon.textContent = 'check_circle';
                    } else {
                        // Si se desmarca, removemos la clase y ponemos el ícono "check_circle_outline"
                        icon.classList.remove('checked');
                        icon.textContent = 'check_circle_outline';
                    }
                } else {
                    console.error('Error en la respuesta:', data.error);
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
        });
    });
});
