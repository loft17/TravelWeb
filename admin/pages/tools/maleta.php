<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Conectar a la base de datos
$conn = conectar_bd();

// Función para limpiar entradas de usuario
function limpiar_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Agregar un artículo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre = limpiar_input($_POST['nombre']);
    $categoria = limpiar_input($_POST['categoria']);
    $cantidad = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);
    $importante = isset($_POST['importante']) ? 1 : 0;

    if ($nombre && $categoria && $cantidad !== false) {
        $stmt = $conn->prepare("INSERT INTO maleta (nombre, categoria, cantidad, importante) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $nombre, $categoria, $cantidad, $importante);
        $stmt->execute();
        $stmt->close();
    }
}

// Modificar un artículo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $nombre = limpiar_input($_POST['nombre']);
    $categoria = limpiar_input($_POST['categoria']);
    $cantidad = filter_var($_POST['cantidad'], FILTER_VALIDATE_INT);
    $importante = isset($_POST['importante']) ? 1 : 0;

    if ($id !== false && $nombre && $categoria && $cantidad !== false) {
        $stmt = $conn->prepare("UPDATE maleta SET nombre = ?, categoria = ?, cantidad = ?, importante = ? WHERE id = ?");
        $stmt->bind_param("ssiii", $nombre, $categoria, $cantidad, $importante, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Eliminar un artículo
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);

    if ($id !== false) {
        $stmt = $conn->prepare("DELETE FROM maleta WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Obtener artículos ordenados por categoría
$result = $conn->query("SELECT * FROM maleta ORDER BY categoria ASC, nombre ASC");
$articulos = $result->fetch_all(MYSQLI_ASSOC);
$result->close();

// Categorías predefinidas
$categorias = ["Ropa", "Neceser", "Botiquin", "Electronica", "Documentacion", "Varios"];
$articulos_por_categoria = [];
foreach ($categorias as $categoria) {
    $articulos_por_categoria[$categoria] = array_filter($articulos, function ($articulo) use ($categoria) {
        return $articulo['categoria'] === $categoria;
    });
}

$conn->close();
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
                                                                    <th scope="col">ID</th>
                                                                    <th scope="col">Nombre</th>
                                                                    <th scope="col">Cantidad</th>
                                                                    <th scope="col">Importante</th>
                                                                    <th scope="col">Acción</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($items as $articulo): ?>
                                                                <tr>
                                                                    <th scope="row"><?= $articulo['id'] ?></th>
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
                </div>
            </div>
            <!-- Notificación de copiado -->
            <div id="copyNotification" style="display: none;" class="alert"></div>
        </div>
        <!-- main content area end -->

    </div>
    <!-- page container area end -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>

    <script>
    function editarArticulo(id, nombre, categoria, cantidad, importante) {
        const form = document.querySelector('form');

        // Cambiar el título del formulario
        const headerTitle = document.querySelector('.header-title');
        if (headerTitle) headerTitle.textContent = "Editar Artículo";

        // Modificar los valores de los campos existentes
        form.querySelector('[name="nombre"]').value = nombre;
        form.querySelector('[name="categoria"]').value = categoria;
        form.querySelector('[name="cantidad"]').value = cantidad;
        form.querySelector('[name="importante"]').checked = importante;

        // Agregar campo oculto para el ID si no existe
        let inputId = form.querySelector('[name="id"]');
        if (!inputId) {
            inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id';
            form.appendChild(inputId);
        }
        inputId.value = id;

        // Modificar el botón de envío
        let submitButton = form.querySelector('[name="agregar"]');
        if (submitButton) {
            submitButton.textContent = "Actualizar";
            submitButton.name = "editar"; // Cambiar el nombre del botón para enviar como edición
        }
    }
    </script>

</body>

</html>