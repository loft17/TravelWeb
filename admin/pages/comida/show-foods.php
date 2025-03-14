<?php
// Lógica PHP: carga de archivos, conexión y obtención de registros
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Conectar a la base de datos y obtener los registros de la tabla 'comida'
$conn = conectar_bd();
$sql = "SELECT * FROM comida";
$result = $conn->query($sql);

$comidas = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $comidas[] = $row;
    }
}
$conn->close();
?>
<!doctype html>
<html class="no-js" lang="en">

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
                                <h4 class="header-title">Comida</h4>
                                <div class="single-table">
                                    <div class="table-responsive">
                                        <table class="table text-center">
                                            <thead class="text-uppercase bg-dark">
                                                <tr class="text-white">
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Nombre</th>
                                                    <th scope="col">Info</th>
                                                    <th scope="col">Puntuación</th>
                                                    <th scope="col">Imagen</th>
                                                    <th scope="col">Comido</th>
                                                    <th scope="col">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($comidas) > 0): ?>
                                                <?php foreach ($comidas as $row): ?>
                                                <tr>
                                                    <th scope="row"><?php echo $row['id']; ?></th>
                                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                                    <td>
                                                        <?php
                                                            echo !empty($row['descripcion'])
                                                                ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                : '<i class="fa fa-check" style="color:red;"></i>';
                                                            ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            echo !empty($row['puntuacion'])
                                                                ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                : '<i class="fa fa-check" style="color:red;"></i>';
                                                            ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            echo !empty($row['imagen_url'])
                                                                ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                : '<i class="fa fa-check" style="color:red;"></i>';
                                                            ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            echo (!empty($row['comido']) && $row['comido'] != 0)
                                                                ? '<i class="fa fa-check" style="color:green;"></i>'
                                                                : '<i class="fa fa-check" style="color:red;"></i>';
                                                            ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit-food.php?id=<?php echo $row['id']; ?>"
                                                            class="btn btn-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <a href="borrar_comida.php?id=<?php echo $row['id']; ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('¿Está seguro de eliminar este registro?')"><i class="fa-solid fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php else: ?>
                                                <tr>
                                                    <td colspan="7">No se encontraron registros.</td>
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
                <!-- Notificación de copiado -->
                <div id="copyNotification" style="display: none;" class="alert"></div>
            </div>
        </div>
        <!-- main content area end -->
    </div>
    <!-- page container area end -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
</body>

</html>