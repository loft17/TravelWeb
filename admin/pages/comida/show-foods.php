<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn     = conectar_bd();
$viaje_id = (int)($_SESSION['viaje_id'] ?? 1);
$stmt     = $conn->prepare("SELECT * FROM comida WHERE viaje_id = ?");
$stmt->bind_param("i", $viaje_id);
$stmt->execute();
$comidas  = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

if (isset($_GET['message'])) {
    $flash = htmlspecialchars($_GET['message']);
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-lg-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Comida</h4>

                                <?php if (isset($flash)): ?>
                                    <div class="alert alert-success"><?= $flash ?></div>
                                <?php endif; ?>

                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table id="foodTable" class="table text-center">
                                            <thead class="text-uppercase bg-dark">
                                                <tr class="text-white">
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Info</th>
                                                    <th>Puntuación</th>
                                                    <th>Imagen</th>
                                                    <th>Comido</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($comidas as $row): ?>
                                                <tr>
                                                    <th scope="row"><?= intval($row['id']) ?></th>
                                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                                    <td><?= !empty($row['descripcion']) ? '<i class="fa fa-check" style="color:green;"></i>' : '<i class="fa fa-times" style="color:red;"></i>' ?></td>
                                                    <td><?= !empty($row['puntuacion']) ? '<i class="fa fa-check" style="color:green;"></i>' : '<i class="fa fa-times" style="color:red;"></i>' ?></td>
                                                    <td><?= !empty($row['imagen_url']) ? '<i class="fa fa-check" style="color:green;"></i>' : '<i class="fa fa-times" style="color:red;"></i>' ?></td>
                                                    <td><?= (!empty($row['comido']) && $row['comido'] != 0) ? '<i class="fa fa-check" style="color:green;"></i>' : '<i class="fa fa-times" style="color:red;"></i>' ?></td>
                                                    <td>
                                                        <a href="edit-food.php?id=<?= intval($row['id']) ?>" class="btn btn-primary btn-sm">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                        <form method="post" action="borrar_comida.php" style="display:inline">
                                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                            <input type="hidden" name="id" value="<?= intval($row['id']) ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="¿Eliminar el plato «<?= htmlspecialchars($row['nombre'], ENT_QUOTES) ?>»?">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#foodTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                pageLength: 25,
                order: [[0, 'asc']]
            });
        });
    </script>
</body>
</html>
