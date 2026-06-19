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

            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#modalAgregar">
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
                                            <?php if (!empty($t['escalas'])): ?>
                                                <span class="badge badge-info ml-1" title="Con escalas">
                                                    <?= count(json_decode($t['escalas'], true)) ?>✦
                                                </span>
                                            <?php endif; ?>
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
                                        <td style="white-space:nowrap">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                    onclick='abrirEditar(<?= htmlspecialchars(json_encode($t), ENT_QUOTES) ?>)'>
                                                <i class="fa fa-edit"></i>
                                            </button>
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

            <!-- Modal: Añadir Transporte -->
            <div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Añadir Transporte</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="formAgregar">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="accion" value="agregar">
                                <?php include __DIR__ . '/form-fields.php'; ?>
                                <div id="escalas-section-a" class="escalas-section" style="display:none">
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong style="font-size:.9em">Escalas</strong>
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-add-escala">+ Añadir escala</button>
                                    </div>
                                    <div class="escalas-container"></div>
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

            <!-- Modal: Editar Transporte -->
            <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Transporte</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="formEditar">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="accion" value="editar">
                                <input type="hidden" name="id" id="edit-id">
                                <?php include __DIR__ . '/form-fields.php'; ?>
                                <div id="escalas-section-e" class="escalas-section" style="display:none">
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong style="font-size:.9em">Escalas</strong>
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-add-escala">+ Añadir escala</button>
                                    </div>
                                    <div class="escalas-container"></div>
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
var TIPOS = <?= json_encode(array_keys($tipos)) ?>;

function esc(v) { return v ? String(v).replace(/&/g,'&amp;').replace(/"/g,'&quot;') : ''; }

// Construye una fila de escala con el layout solicitado
function buildEscalaRow(container, idx, data) {
    data = data || {};
    var div = document.createElement('div');
    div.className = 'escala-row border rounded p-2 mb-2';
    div.innerHTML =
        '<div class="d-flex justify-content-between align-items-center mb-2">' +
        '  <strong style="font-size:.82em;color:#555">Escala ' + (idx + 1) + '</strong>' +
        '  <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="this.closest(\'.escala-row\').remove()">Eliminar</button>' +
        '</div>' +

        // Hora llegada a la escala: oculta, auto-asumida del tramo anterior
        '<input type="hidden" name="escalas[' + idx + '][hora_llegada]" value="' + esc(data.hora_llegada) + '">' +

        // 1. Duración de la escala
        '<div class="form-group">' +
        '  <label style="font-size:.8em">Duración escala</label>' +
        '  <input type="text" name="escalas[' + idx + '][duracion_escala]" class="form-control form-control-sm" placeholder="2h30" value="' + esc(data.duracion_escala) + '">' +
        '</div>' +

        // 2. Aeropuerto + Salida
        '<div class="form-row">' +
        '  <div class="form-group col-6"><label style="font-size:.8em">Aeropuerto</label>' +
        '    <input type="text" name="escalas[' + idx + '][aeropuerto]" class="form-control form-control-sm" placeholder="DXB" value="' + esc(data.aeropuerto) + '"></div>' +
        '  <div class="form-group col-6"><label style="font-size:.8em">Salida</label>' +
        '    <input type="time" name="escalas[' + idx + '][hora_salida]" class="form-control form-control-sm" value="' + esc(data.hora_salida) + '"></div>' +
        '</div>' +

        // 3. Destino + Llegada
        '<div class="form-row">' +
        '  <div class="form-group col-6"><label style="font-size:.8em">Destino</label>' +
        '    <input type="text" name="escalas[' + idx + '][destino_sig]" class="form-control form-control-sm" placeholder="NRT" value="' + esc(data.destino_sig) + '"></div>' +
        '  <div class="form-group col-6"><label style="font-size:.8em">Llegada</label>' +
        '    <input type="time" name="escalas[' + idx + '][hora_llegada_sig]" class="form-control form-control-sm" value="' + esc(data.hora_llegada_sig) + '"></div>' +
        '</div>' +

        // 4. Número de vuelo
        '<div class="form-group mb-0"><label style="font-size:.8em">Nº vuelo</label>' +
        '  <input type="text" name="escalas[' + idx + '][numero]" class="form-control form-control-sm" placeholder="EK317" value="' + esc(data.numero) + '"></div>';

    container.appendChild(div);
}

// Obtiene el destino y hora_llegada del último tramo disponible para pre-rellenar la nueva escala
function getLastLegEnd(form, container) {
    var rows = container.querySelectorAll('.escala-row');
    if (rows.length === 0) {
        return {
            aeropuerto:  form.querySelector('[name="destino"]').value,
            hora_llegada: form.querySelector('[name="hora_llegada"]').value
        };
    }
    var last = rows[rows.length - 1];
    return {
        aeropuerto:   last.querySelector('[name$="[destino_sig]"]').value,
        hora_llegada: last.querySelector('[name$="[hora_llegada_sig]"]').value
    };
}

// Inicializa la lógica de escalas para un modal dado
function initModal(modalId, sectionId) {
    var modal     = document.getElementById(modalId);
    var section   = document.getElementById(sectionId);
    var tipoSel   = modal.querySelector('[name="tipo"]');
    var container = section.querySelector('.escalas-container');
    var btnAdd    = section.querySelector('.btn-add-escala');
    var idx       = 0;

    function toggleEscalas() {
        section.style.display = tipoSel.value === 'avion' ? 'block' : 'none';
    }
    tipoSel.addEventListener('change', toggleEscalas);
    toggleEscalas();

    btnAdd.addEventListener('click', function () {
        var prefill = getLastLegEnd(modal.querySelector('form'), container);
        buildEscalaRow(container, idx++, prefill);
    });

    $(modal).on('hidden.bs.modal', function () {
        container.innerHTML = '';
        idx = 0;
    });

    return { container: container, setIdx: function(v) { idx = v; } };
}

var ctxA = initModal('modalAgregar', 'escalas-section-a');
var ctxE = initModal('modalEditar',  'escalas-section-e');

function abrirEditar(t) {
    var modal = document.getElementById('modalEditar');

    // Campos básicos
    modal.querySelector('[name="id"]').value           = t.id;
    modal.querySelector('[name="tipo"]').value         = t.tipo;
    modal.querySelector('[name="origen"]').value       = t.origen;
    modal.querySelector('[name="destino"]').value      = t.destino;
    modal.querySelector('[name="fecha"]').value        = t.fecha;
    modal.querySelector('[name="hora_salida"]').value  = t.hora_salida  ? t.hora_salida.substring(0,5)  : '';
    modal.querySelector('[name="hora_llegada"]').value = t.hora_llegada ? t.hora_llegada.substring(0,5) : '';
    modal.querySelector('[name="numero"]').value       = t.numero  || '';
    modal.querySelector('[name="notas"]').value        = t.notas   || '';

    // Disparar change para mostrar/ocultar sección escalas
    modal.querySelector('[name="tipo"]').dispatchEvent(new Event('change'));

    // Cargar escalas existentes
    var container = document.getElementById('escalas-section-e').querySelector('.escalas-container');
    container.innerHTML = '';
    var escalas = [];
    try { escalas = t.escalas ? JSON.parse(t.escalas) : []; } catch(e) {}
    escalas.forEach(function(e, i) { buildEscalaRow(container, i, e); });
    ctxE.setIdx(escalas.length);

    $('#modalEditar').modal('show');
}
</script>
</body>
</html>
