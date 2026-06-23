<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/aerolineas.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
?>
<!doctype html>
<html lang="es">
<head><meta charset="UTF-8"><title>Aerolíneas</title></head>
<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">

            <?php if ($ae_error): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($ae_error) ?></div>
            <?php endif; ?>
            <?php if ($ae_success): ?>
                <div class="alert alert-success mt-3"><?= htmlspecialchars($ae_success) ?></div>
            <?php endif; ?>

            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#modalAgregar">
                <i class="fa fa-plus"></i> Añadir Aerolínea
            </button>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Aerolíneas</h4>
                            <div class="table-responsive">
                                <table class="table table-hover text-center">
                                    <thead class="text-uppercase bg-dark text-white">
                                        <tr>
                                            <th>Logo</th>
                                            <th>Nombre</th>
                                            <th>Código IATA</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($aerolineas)): ?>
                                        <tr><td colspan="4" class="text-muted py-4">No hay aerolíneas registradas.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($aerolineas as $al): ?>
                                    <tr>
                                        <td>
                                            <?php if ($al['icono'] && (str_starts_with($al['icono'], '/') || str_starts_with($al['icono'], 'http'))): ?>
                                                <img src="<?= htmlspecialchars($al['icono']) ?>" alt="<?= htmlspecialchars($al['nombre']) ?>" height="28">
                                            <?php else: ?>
                                                –
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($al['nombre']) ?></td>
                                        <td><?= $al['codigo'] ? htmlspecialchars($al['codigo']) : '–' ?></td>
                                        <td style="white-space:nowrap">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                    onclick='abrirEditar(<?= htmlspecialchars(json_encode($al), ENT_QUOTES) ?>)'>
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <form method="post" style="display:inline" enctype="multipart/form-data">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                <input type="hidden" name="accion" value="borrar">
                                                <input type="hidden" name="id" value="<?= intval($al['id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        data-confirm="¿Eliminar esta aerolínea?">
                                                    <i class="ti-trash"></i>
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

            <!-- Modal: Añadir Aerolínea -->
            <div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Añadir Aerolínea</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="formAgregar" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="accion" value="agregar">
                                <div class="form-group">
                                    <label>Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control" placeholder="Emirates" required>
                                </div>
                                <div class="form-group">
                                    <label>Código IATA</label>
                                    <input type="text" name="codigo" class="form-control" placeholder="EK" maxlength="10">
                                </div>
                                <div class="form-group">
                                    <label>URL del logo</label>
                                    <input type="text" name="icono_url" class="form-control" placeholder="https://...">
                                </div>
                                <div class="form-group">
                                    <label>O subir imagen</label>
                                    <input type="file" name="icono_file" class="form-control-file" accept=".png,.jpg,.jpeg,.svg,.webp,.gif">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" form="formAgregar" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Editar Aerolínea -->
            <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Aerolínea</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="formEditar" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="accion" value="editar">
                                <input type="hidden" name="id" id="edit-id">
                                <div class="form-group">
                                    <label>Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Código IATA</label>
                                    <input type="text" name="codigo" id="edit-codigo" class="form-control" maxlength="10">
                                </div>
                                <div class="form-group">
                                    <label>URL del logo</label>
                                    <input type="text" name="icono_url" id="edit-icono-url" class="form-control" placeholder="https://...">
                                </div>
                                <div id="edit-icono-preview" class="mb-2" style="display:none">
                                    <img id="edit-icono-img" src="" alt="Logo actual" height="32">
                                    <small class="text-muted ml-2">Logo actual (subir archivo o escribir URL para reemplazar)</small>
                                </div>
                                <div class="form-group">
                                    <label>O subir imagen</label>
                                    <input type="file" name="icono_file" class="form-control-file" accept=".png,.jpg,.jpeg,.svg,.webp,.gif">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" form="formEditar" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
<script>
function abrirEditar(al) {
    document.getElementById('edit-id').value         = al.id;
    document.getElementById('edit-nombre').value     = al.nombre || '';
    document.getElementById('edit-codigo').value     = al.codigo || '';
    document.getElementById('edit-icono-url').value  = (al.icono && (al.icono.startsWith('http') || al.icono.startsWith('/'))) ? '' : '';

    var preview = document.getElementById('edit-icono-preview');
    var img     = document.getElementById('edit-icono-img');
    if (al.icono && (al.icono.startsWith('/') || al.icono.startsWith('http'))) {
        img.src = al.icono;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }

    $('#modalEditar').modal('show');
}
</script>
</body>
</html>
