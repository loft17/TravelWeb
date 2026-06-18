<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
?>

<!-- jquery latest version -->
<script src="/admin/assets/js/vendor/jquery-2.2.4.min.js"></script>
<!-- bootstrap 4 js -->
<script src="/admin/assets/js/popper.min.js"></script>
<script src="/admin/assets/js/bootstrap.min.js"></script>
<script src="/admin/assets/js/owl.carousel.min.js"></script>
<script src="/admin/assets/js/metisMenu.min.js"></script>
<script src="/admin/assets/js/jquery.slimscroll.min.js"></script>
<script src="/admin/assets/js/jquery.slicknav.min.js"></script>

<!-- others plugins -->
<script src="/admin/assets/js/plugins.js"></script>
<script src="/admin/assets/js/scripts.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            var message = el.getAttribute('data-confirm') || '¿Estás seguro?';
            var form = el.closest('form');
            Swal.fire({
                title: '¿Confirmar acción?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    if (form) form.submit();
                }
            });
        });
    });
});

function showToast(icon, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: title,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}
</script>
