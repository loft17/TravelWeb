<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';

// Incluir el archivo con la función para obtener las atracciones
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/show_atraccion.php';

// Obtener el listado de atracciones
$atracciones = getAtracciones();
?>


<!doctype html>
<html class="no-js" lang="en">
<head>
    <!-- CSS de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <!-- CSS adicional para jQuery UI (drag & drop) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    <!-- page container area start -->
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

        <!-- main content area start -->
        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <!-- Tabla con formato dark -->
                    <div class="col-lg-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Atracciones</h4>
                                <!-- Se han eliminado los filtros de fecha y ciudad -->
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <!-- Se agrega id a la tabla para inicializar DataTables -->
                                        <table id="dataTable" class="table text-center">
                                            <thead class="text-uppercase bg-dark">
                                                <tr class="text-white">
                                                    <th scope="col">Orden</th>
                                                    <th scope="col">Nombre</th>
                                                    <th scope="col">Ciudad</th>
                                                    <th scope="col">Fecha</th>
                                                    <th scope="col">Imagen</th>
                                                    <th scope="col">Visto</th>
                                                    <th scope="col">Activo</th>
                                                    <th scope="col">Mapa</th>
                                                    <th scope="col">Info</th>
                                                    <th scope="col">Instagram 1</th>
                                                    <th scope="col">Instagram 2</th>
                                                    <th scope="col">Instagram 3</th>
                                                    <th scope="col">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($atracciones) > 0): ?>
                                                    <?php foreach ($atracciones as $row): ?>
                                                    <!-- Se agrega data-id para identificar cada registro -->
                                                    <tr data-id="<?php echo $row['id']; ?>">
                                                        <td><?php echo htmlspecialchars($row['orden']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['ciudad']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                                        <td>
                                                            <?php
                                                                echo !empty($row['imagen_url'])
                                                                    ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                    : '<i class="fa fa-times" style="color:red;"></i>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                                echo (!empty($row['visto']) && $row['visto'] != 0)
                                                                    ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                    : '<i class="fa fa-times" style="color:red;"></i>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                                echo (!empty($row['activo']) && $row['activo'] != 0)
                                                                    ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                    : '<i class="fa fa-times" style="color:red;"></i>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                                echo !empty($row['mapa_url'])
                                                                    ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                    : '<i class="fa fa-times" style="color:red;"></i>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                                echo !empty($row['wikipedia_url'])
                                                                    ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                    : '<i class="fa fa-times" style="color:red;"></i>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                                echo !empty($row['instagram_url_1'])
                                                                    ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                    : '<i class="fa fa-times" style="color:red;"></i>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                                echo !empty($row['instagram_url_2'])
                                                                    ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                    : '<i class="fa fa-times" style="color:red;"></i>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                                echo !empty($row['instagram_url_3'])
                                                                    ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                    : '<i class="fa fa-times" style="color:red;"></i>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a href="edit-atraccion.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </a>
                                                            <a href="/admin/includes/functions/delete-atraccion.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este registro?')">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="13">No se encontraron registros.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div><!-- table-responsive -->
                                </div><!-- single-table -->
                            </div><!-- card-body -->
                        </div><!-- card -->
                    </div>
                    <!-- table dark end -->
                </div>
            </div>
            <!-- Notificación de copiado -->
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    </div>
    <!-- page container area end -->

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>

    <!-- Cargar jQuery -->
    <script src="/admin/assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- Cargar jQuery UI para habilitar sortable -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- Cargar DataTables -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <!-- Cargar el script externo con la funcionalidad de la tabla -->
    <script src="/admin/assets/js/show_atraccion.js"></script>
</body>
</html>
