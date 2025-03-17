<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/maleta.php';

?>


<!doctype html>
<html class="no-js" lang="en">


<body>
    <!-- page container area start -->
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php';?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php';?>


        <!-- main content area start -->
        <div class="main-content">

            <!-- page title area end -->
            <div class="main-content-inner">
                <div class="row">
                    <!-- No gutters start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">ITEMS</div>

                                <form method="POST" class="mb-3">
                                    <div class="form-row align-items-center">
                                        <!-- Campo de nombre -->
                                        <div class="col-sm-3 my-1">
                                            <label class="sr-only" for="nombre">Nombre del artículo</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                placeholder="Nombre del artículo" required>
                                        </div>

                                        <!-- Campo de categoría -->
                                        <div class="col-sm-3 my-1">
                                            <select class="form-control" name="categoria" required>
                                                <option value="" disabled selected>Seleccionar categoría</option>
                                                <?php foreach ($categorias as $categoria): ?>
                                                <option value="<?= htmlspecialchars($categoria) ?>">
                                                    <?= htmlspecialchars($categoria) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- Campo de cantidad -->
                                        <div class="col-sm-2 my-1">
                                            <input class="form-control" type="number" name="cantidad"
                                                placeholder="Cantidad" min="1" required>
                                        </div>

                                        <!-- Checkbox de importante -->
                                        <div class="col-auto my-1">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="importante"
                                                    name="importante">
                                                <label class="custom-control-label" for="importante">Importante</label>
                                            </div>
                                        </div>

                                        <!-- Botón de enviar -->
                                        <div class="col-auto my-1">
                                            <button type="submit" name="agregar"
                                                class="btn btn-primary">Agregar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- No gutters end -->

                    <!-- No gutters start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">MALETA</div>

                                <div class="row">
                                    <?php foreach ($articulos_por_categoria as $categoria => $items): ?>
                                    <?php if (!empty($items)): ?>
                                    <div class="col-lg-6 mt-5">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="header-title"><?= htmlspecialchars($categoria) ?></h4>
                                                <div class="single-table">
                                                    <div class="table-responsive">
                                                        <table class="table text-center">
                                                            <thead class="text-uppercase bg-info">
                                                                <tr class="text-white">
                                                                    <th scope="col">Nombre</th>
                                                                    <th scope="col">Cantidad</th>
                                                                    <th scope="col">Importante</th>
                                                                    <th scope="col">Acción</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($items as $articulo): ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($articulo['nombre']) ?>
                                                                    </td>
                                                                    <td><?= $articulo['cantidad'] ?></td>
                                                                    <td><?= $articulo['importante'] ? '✅' : '❌' ?></td>
                                                                    <td>
                                                                        <a href="#"
                                                                            onclick="editarArticulo(<?= $articulo['id'] ?>, '<?= htmlspecialchars($articulo['nombre']) ?>', '<?= htmlspecialchars($articulo['categoria']) ?>', <?= $articulo['cantidad'] ?>, <?= $articulo['importante'] ?>)"
                                                                            title="Editar">
                                                                            <i class="fa fa-edit"></i>
                                                                        </a>
                                                                        &nbsp;&nbsp;
                                                                        <!-- Espacio entre los íconos -->
                                                                        <a href="?delete=<?= $articulo['id'] ?>"
                                                                            onclick="return confirm('¿Seguro que deseas eliminar este artículo?')"
                                                                            title="Eliminar">
                                                                            <i class="ti-trash"></i>
                                                                        </a>
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
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- No gutters end -->
                </div>
            </div>
            <!-- Notificación de copiado -->
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <!-- main content area end -->
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>

    </div>
    <!-- page container area end -->
    <script defer src="/admin/assets/js/maleta.js"></script>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>
</body>

</html>