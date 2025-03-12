<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include '../includes/templates/head.php';  // Asegúrate de que head.php tenga los elementos <head> y <meta> adecuados.
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <!-- Aquí van los otros elementos de <head> -->
</head>
<body>
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">
        <?php include '../includes/templates/sidebar.php'; ?>
        <?php include '../includes/templates/user-profile.php'; ?>


        <!-- main content area start -->
        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <!-- No gutters start -->
                    <div class="col-12 mt-5">
                                
                        <!-- Aquí va el código para mostrar los usuarios -->
                        <?php
                        // Incluir el archivo que contiene la consulta y la lógica para obtener los usuarios
                        include '../includes/functions/show-users.php';
                        ?>

                        <!-- Progress Table start -->
                        <div class="col-12 mt-5">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Progress Table</h4>
                                    <div class="single-table">
                                        <div class="table-responsive">
                                            <table class="table table-hover progress-table text-center">
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
                                                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="text-secondary">
                                                                                <i class="fa fa-edit"></i>
                                                                            </a>
                                                                        </li>
                                                                        <li class="mr-3">
                                                                            <a href="../includes/functions/delete-user.php?id=<?php echo $user['id']; ?>" class="text-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                                                                <i class="ti-trash"></i>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No hay usuarios registrados.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No gutters end -->
                </div>
            </div>

            <!-- mensaje de usuario -->
            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success"><?php echo $_GET['message']; ?></div>
            <?php endif; ?>
            
        </div>
        <!-- main content area end -->


    </div>
    <!-- page container area end -->



    <?php include '../includes/templates/footer.php'; ?>
    <?php include '../includes/libraries/scripts.php'; ?>
</body>
</html>
