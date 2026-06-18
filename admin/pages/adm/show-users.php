<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
</head>

<body>
    <!-- page container area start -->
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php';?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php';?>

        <!-- main content area start -->
        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <!-- No gutters start -->
                    <div class="col-12 mt-5">

                        <!-- Aquí va el código para mostrar los usuarios -->
                        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/show_users.php';?>

                        <!-- Progress Table start -->
                        <div class="col-12 mt-5">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Usuarios registrados</h4>
                                    <div class="single-table">
                                        <div class="table-responsive">
                                            <table id="usersTable" class="table table-hover progress-table text-center">
                                                <thead class="text-uppercase">
                                                    <tr>
                                                        <th scope="col">ID</th>
                                                        <th scope="col">Nombre</th>
                                                        <th scope="col">Email</th>
                                                        <th scope="col">Rol</th>
                                                        <th scope="col">Activo</th>
                                                        <th scope="col">Accion</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($users)): ?>
                                                    <?php foreach ($users as $user): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                        <td>
                                                            <?php 
                                                                        if ($user['rol'] === 'admin') {
                                                                            echo '<span class="status-p bg-danger">' . htmlspecialchars($user['rol']) . '</span>';
                                                                        } elseif ($user['rol'] === 'usuario') {
                                                                            echo '<span class="status-p bg-success">' . htmlspecialchars($user['rol']) . '</span>';
                                                                        } else {
                                                                            echo '<span class="status-p bg-warning">' . htmlspecialchars($user['rol']) . '</span>';
                                                                        }
                                                                    ?>
                                                        </td>

                                                        <td><?php echo $user['active'] ? 'Sí' : 'No'; ?></td>
                                                        <td>
                                                            <ul class="d-flex justify-content-center">
                                                                <li class="mr-3">
                                                                    <a href="edit-user.php?id=<?php echo $user['id']; ?>"
                                                                        class="text-secondary">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                </li>
                                                                <li class="mr-3">
                                                                    <form method="post" action="../includes/functions/delete-user.php" style="display:inline">
                                                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                                                        <input type="hidden" name="id" value="<?php echo intval($user['id']); ?>">
                                                                        <button type="submit" class="btn btn-link text-danger p-0" data-confirm="¿Eliminar al usuario <?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>?">
                                                                            <i class="ti-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">No hay usuarios registrados.
                                                        </td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <br>
                            <!-- mensaje de usuario -->
                            <?php if (isset($_GET['message'])): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>



            </div>


        </div>


    </div>
    <!-- page container area end -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json' },
                pageLength: 25,
                order: [[0, 'asc']]
            });
        });
    </script>
</body>

</html>