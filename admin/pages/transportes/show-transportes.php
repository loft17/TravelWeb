<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/transportes.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';

$tipos  = ['avion'=>'Avión','bus'=>'Bus','tren'=>'Tren','ferry'=>'Ferry','taxi'=>'Taxi','coche'=>'Coche','otro'=>'Otro'];
$iconos = ['avion'=>'fa-plane','bus'=>'fa-bus','tren'=>'fa-train','ferry'=>'fa-ship','taxi'=>'fa-taxi','coche'=>'fa-car','otro'=>'fa-route'];
?>
<!doctype html>
<html lang="es">
<head><meta charset="UTF-8"><title>Transportes</title></head>
<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">

            <?php if ($tr_error): ?>
                <div class="alert alert-danger mt-3"><?= htmlspecialchars($tr_error) ?></div>
            <?php endif; ?>
            <?php if ($tr_success): ?>
                <div class="alert alert-success mt-3"><?= htmlspecialchars($tr_success) ?></div>
            <?php endif; ?>

            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#modalTransporte">
                <i class="fa fa-plus"></i> Añadir Transporte
            </button>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Transportes del viaje</h4>
                            <div class="table-responsive">
                                <table class="table table-hover text-center">
                                    <thead class="text-uppercase bg-dark text-white">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Fecha</th>
                                            <th>Salida</th>
                                            <th>Llegada</th>
                                            <th>Número</th>
                                            <th>Notas</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($transportes)): ?>
                                        <tr><td colspan="9" class="text-muted py-4">No hay transportes registrados.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($transportes as $t): ?>
                                    <tr>
                                        <td>
                                            <i class="fa <?= $iconos[$t['tipo']] ?? 'fa-route' ?> mr-1"></i>
                                            <?= htmlspecialchars($tipos[$t['tipo']] ?? ucfirst($t['tipo'])) ?>
                                        </td>
                                        <td><?= htmlspecialchars($t['origen']) ?></td>
                                        <td><?= htmlspecialchars($t['destino']) ?></td>
                                        <td><?= htmlspecialchars($t['fecha']) ?></td>
                                        <td><?= $t['hora_salida']  ? substr($t['hora_salida'],  0, 5) : '–' ?></td>
                                        <td><?= $t['hora_llegada'] ? substr($t['hora_llegada'], 0, 5) : '–' ?></td>
                                        <td><?= $t['numero'] ? htmlspecialchars($t['numero']) : '–' ?></td>
                                        <td class="text-left">
                                            <?php if ($t['notas']): ?>
                                                <?= htmlspecialchars(mb_substr($t['notas'], 0, 60)) . (mb_strlen($t['notas']) > 60 ? '…' : '') ?>
                                            <?php else: ?>–<?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="post" style="display:inline">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                <input type="hidden" name="accion" value="borrar">
                                                <input type="hidden" name="id" value="<?= intval($t['id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        data-confirm="¿Eliminar este transporte?">
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

            <!-- Modal Añadir Transporte -->
            <div class="modal fade" id="modalTransporte" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Añadir Transporte</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="formTransporte">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="accion" value="agregar">
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <select name="tipo" class="form-control">
                                        <?php foreach ($tipos as $k => $v): ?>
                                        <option value="<?= $k ?>"><?= $v ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label>Origen</label>
                                        <input type="text" name="origen" class="form-control" placeholder="Ciudad de salida" required>
                                    </div>
                                    <div class="form-group col">
                                        <label>Destino</label>
                                        <input type="text" name="destino" class="form-control" placeholder="Ciudad de llegada" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label>Fecha</label>
                                        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="form-group col">
                                        <label>Número (vuelo, bus…)</label>
                                        <input type="text" name="numero" class="form-control" placeholder="IB1234">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label>Hora salida</label>
                                        <input type="time" name="hora_salida" class="form-control">
                                    </div>
                                    <div class="form-group col">
                                        <label>Hora llegada</label>
                                        <input type="time" name="hora_llegada" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Notas</label>
                                    <textarea name="notas" class="form-control" rows="2"
                                              placeholder="Terminal, asiento, equipaje…"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" form="formTransporte" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
</body>
</html>
