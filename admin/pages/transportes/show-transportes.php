<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/transportes.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';

$tipos  = ['avion'=>'Avión','bus'=>'Bus','tren'=>'Tren','ferry'=>'Ferry','taxi'=>'Taxi','coche'=>'Coche','otro'=>'Otro'];
$iconos = ['avion'=>'fa-plane','bus'=>'fa-bus','tren'=>'fa-train','ferry'=>'fa-ship','taxi'=>'fa-taxi','coche'=>'fa-car','otro'=>'fa-route'];
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/aerolineas.php';
$aerolineas_list = get_aerolineas();
$aerolineas_map  = array_column($aerolineas_list, null, 'id');

// Carriers JSON para mostrar nombres desde código IATA
$_carriers_json = $_SERVER['DOCUMENT_ROOT'] . '/data/carriers.json';
$carriers_by_code = [];
if (file_exists($_carriers_json)) {
    foreach (json_decode(file_get_contents($_carriers_json), true) ?? [] as $c) {
        if (!empty($c['codigo'])) $carriers_by_code[strtoupper($c['codigo'])] = $c;
    }
}
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
                                            <th>Compañía</th>
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
                                        <tr><td colspan="10" class="text-muted py-4">No hay transportes registrados.</td></tr>
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
                                        <td>
                                            <?php
                                            $carrier_display = '–';
                                            if (!empty($t['compania'])) {
                                                $c_info = $carriers_by_code[strtoupper($t['compania'])] ?? null;
                                                $carrier_display = $c_info ? htmlspecialchars($c_info['nombre']) : htmlspecialchars($t['compania']);
                                                if ($c_info && !empty($c_info['icono'])) {
                                                    $carrier_display = '<img src="'.htmlspecialchars($c_info['icono']).'" style="height:16px;object-fit:contain;margin-right:4px" onerror="this.remove()">' . $carrier_display;
                                                }
                                            } elseif (!empty($t['aerolinea_id']) && isset($aerolineas_map[$t['aerolinea_id']])) {
                                                $carrier_display = htmlspecialchars($aerolineas_map[$t['aerolinea_id']]['nombre']);
                                            }
                                            echo $carrier_display;
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($t['origen']) ?></td>
                                        <td><?= htmlspecialchars($t['destino']) ?></td>
                                        <td><?= htmlspecialchars($t['fecha']) ?></td>
                                        <td><?= $t['hora_salida']  ? substr($t['hora_salida'],  0, 5) : '–' ?></td>
                                        <td>
                                            <?= $t['hora_llegada'] ? substr($t['hora_llegada'], 0, 5) : '–' ?>
                                            <?php if (!empty($t['fecha_llegada']) && $t['fecha_llegada'] !== $t['fecha']):
                                                $dias = (int)((strtotime($t['fecha_llegada']) - strtotime($t['fecha'])) / 86400);
                                            ?>
                                                <span class="badge badge-warning ml-1" title="Llega el <?= htmlspecialchars($t['fecha_llegada']) ?>">+<?= $dias ?>d</span>
                                            <?php endif; ?>
                                        </td>
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
                <div class="modal-dialog modal-lg" role="document">
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
                <div class="modal-dialog modal-lg" role="document">
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
var TIPOS       = <?= json_encode(array_keys($tipos)) ?>;
var AEROLINEAS  = <?= json_encode(array_values($aerolineas_list)) ?>;

function esc(v) { return v ? String(v).replace(/&/g,'&amp;').replace(/"/g,'&quot;') : ''; }

function buildEscalaRow(container, idx, data) {
    data = data || {};
    var div = document.createElement('div');
    div.className = 'escala-row';

    var escCompania = data.compania || '';
    if (!escCompania && data.aerolinea_id) {
        var escAl = AEROLINEAS.find(function(a){ return String(a.id) === String(data.aerolinea_id); });
        if (escAl) escCompania = escAl.codigo || escAl.nombre || '';
    }

    div.innerHTML =
        // ── Cabecera de la escala
        '<div class="escala-row-hdr">' +
        '  <div class="escala-row-title"><i class="fa fa-exchange"></i> Escala ' + (idx + 1) + '</div>' +
        '  <div class="escala-row-actions">' +
        '    <span class="escala-wait-label">Tiempo espera</span>' +
        '    <input type="text" name="escalas[' + idx + '][duracion_escala]" class="form-control form-control-sm escala-wait-input" placeholder="1h30" value="' + esc(data.duracion_escala) + '">' +
        '    <button type="button" class="escala-remove-btn" onclick="this.closest(\'.escala-row\').remove()" title="Eliminar escala"><i class="fa fa-times"></i></button>' +
        '  </div>' +
        '</div>' +

        '<div class="escala-row-body">' +

        // ── Aerolínea + Nº vuelo + Duración
        '<div class="form-row mb-3">' +
        '  <div class="form-group col-5 mb-0" style="position:relative">' +
        '    <label class="tr-field-label">Aerolínea</label>' +
        '    <input type="text" name="escalas[' + idx + '][compania]" class="form-control form-control-sm tr-carrier-input" placeholder="EK – Emirates" autocomplete="off" value="' + esc(escCompania) + '">' +
        '    <input type="hidden" name="escalas[' + idx + '][aerolinea_id]" value="">' +
        '    <div class="tr-carrier-dropdown" style="display:none;position:absolute;z-index:9999;background:#fff;border:1px solid #ced4da;border-radius:4px;width:100%;max-height:180px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,.15)"></div>' +
        '  </div>' +
        '  <div class="form-group col-4 mb-0">' +
        '    <label class="tr-field-label">Nº vuelo</label>' +
        '    <input type="text" name="escalas[' + idx + '][numero]" class="form-control form-control-sm" placeholder="EK317" value="' + esc(data.numero) + '">' +
        '  </div>' +
        '  <div class="form-group col-3 mb-0">' +
        '    <label class="tr-field-label">Duración</label>' +
        '    <input type="text" name="escalas[' + idx + '][duracion]" class="form-control form-control-sm" placeholder="7h" value="' + esc(data.duracion) + '">' +
        '  </div>' +
        '</div>' +

        // ── Sección SALIDA (aeropuerto de la escala)
        '<div class="tr-section tr-section-salida mb-2">' +
        '  <div class="tr-sec-hdr"><i class="fa fa-sign-out"></i><span class="tr-sec-title">Salida desde escala</span></div>' +
        '  <div class="form-row">' +
        '    <div class="form-group col-5 mb-2">' +
        '      <label class="tr-field-label">Código IATA</label>' +
        '      <input type="text" name="escalas[' + idx + '][aeropuerto]" class="form-control form-control-sm" placeholder="DXB" value="' + esc(data.aeropuerto) + '" autocomplete="off">' +
        '    </div>' +
        '    <div class="form-group col-4 mb-2">' +
        '      <label class="tr-field-label">Fecha</label>' +
        '      <input type="date" name="escalas[' + idx + '][fecha_salida]" class="form-control form-control-sm" value="' + esc(data.fecha_salida) + '">' +
        '    </div>' +
        '    <div class="form-group col-3 mb-2">' +
        '      <label class="tr-field-label">Hora</label>' +
        '      <input type="time" name="escalas[' + idx + '][hora_salida]" class="form-control form-control-sm" value="' + esc(data.hora_salida) + '">' +
        '    </div>' +
        '  </div>' +
        '  <div class="form-row tr-subfields">' +
        '    <div class="form-group col-4 mb-0">' +
        '      <input type="text" name="escalas[' + idx + '][ciudad]" class="form-control form-control-sm" placeholder="Ciudad" value="' + esc(data.ciudad) + '" autocomplete="off">' +
        '    </div>' +
        '    <div class="form-group col-8 mb-0">' +
        '      <input type="text" name="escalas[' + idx + '][aeropuerto_nombre]" class="form-control form-control-sm" placeholder="Nombre del aeropuerto" value="' + esc(data.aeropuerto_nombre) + '" autocomplete="off">' +
        '    </div>' +
        '  </div>' +
        '</div>' +

        // ── Sección LLEGADA (siguiente tramo)
        '<div class="tr-section tr-section-llegada">' +
        '  <div class="tr-sec-hdr"><i class="fa fa-sign-in"></i><span class="tr-sec-title">Llegada siguiente tramo</span></div>' +
        '  <div class="form-row">' +
        '    <div class="form-group col-5 mb-2">' +
        '      <label class="tr-field-label">Código IATA</label>' +
        '      <input type="text" name="escalas[' + idx + '][destino_sig]" class="form-control form-control-sm" placeholder="NRT" value="' + esc(data.destino_sig) + '" autocomplete="off">' +
        '    </div>' +
        '    <div class="form-group col-4 mb-2">' +
        '      <label class="tr-field-label">Fecha</label>' +
        '      <input type="date" name="escalas[' + idx + '][fecha_llegada_sig]" class="form-control form-control-sm" value="' + esc(data.fecha_llegada_sig) + '">' +
        '    </div>' +
        '    <div class="form-group col-3 mb-2">' +
        '      <label class="tr-field-label">Hora</label>' +
        '      <input type="time" name="escalas[' + idx + '][hora_llegada_sig]" class="form-control form-control-sm" value="' + esc(data.hora_llegada_sig) + '">' +
        '    </div>' +
        '  </div>' +
        '  <div class="form-row tr-subfields">' +
        '    <div class="form-group col-4 mb-0">' +
        '      <input type="text" name="escalas[' + idx + '][ciudad_sig]" class="form-control form-control-sm" placeholder="Ciudad" value="' + esc(data.ciudad_sig) + '" autocomplete="off">' +
        '    </div>' +
        '    <div class="form-group col-8 mb-0">' +
        '      <input type="text" name="escalas[' + idx + '][aeropuerto_nombre_sig]" class="form-control form-control-sm" placeholder="Nombre del aeropuerto" value="' + esc(data.aeropuerto_nombre_sig) + '" autocomplete="off">' +
        '    </div>' +
        '  </div>' +
        '</div>' +

        '</div>'; // escala-row-body

    container.appendChild(div);
}

function getLastLegEnd(form, container) {
    var rows = container.querySelectorAll('.escala-row');
    if (rows.length === 0) {
        var fl = form.querySelector('[name="fecha_llegada"]').value;
        return {
            aeropuerto:   form.querySelector('[name="destino"]').value,
            fecha_salida: fl || form.querySelector('[name="fecha"]').value,
            hora_salida:  form.querySelector('[name="hora_llegada"]').value
        };
    }
    var last = rows[rows.length - 1];
    return {
        aeropuerto:   last.querySelector('[name$="[destino_sig]"]').value,
        fecha_salida: last.querySelector('[name$="[fecha_llegada_sig]"]').value,
        hora_salida:  last.querySelector('[name$="[hora_llegada_sig]"]').value
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
    modal.querySelector('[name="hora_salida"]').value   = t.hora_salida  ? t.hora_salida.substring(0,5)  : '';
    modal.querySelector('[name="hora_llegada"]').value  = t.hora_llegada ? t.hora_llegada.substring(0,5) : '';
    modal.querySelector('[name="fecha_llegada"]').value = t.fecha_llegada && t.fecha_llegada !== t.fecha ? t.fecha_llegada : '';
    modal.querySelector('[name="duracion"]').value           = t.duracion || '';
    modal.querySelector('[name="numero"]').value             = t.numero  || '';
    modal.querySelector('[name="notas"]').value              = t.notas   || '';
    modal.querySelector('[name="ciudad_origen"]').value      = t.ciudad_origen      || '';
    modal.querySelector('[name="aeropuerto_origen"]').value  = t.aeropuerto_origen  || '';
    modal.querySelector('[name="ciudad_destino"]').value     = t.ciudad_destino     || '';
    modal.querySelector('[name="aeropuerto_destino"]').value = t.aeropuerto_destino || '';
    // Carrier: prefer compania field; fall back to looking up airline code from DB list
    var companiaVal = t.compania || '';
    if (!companiaVal && t.aerolinea_id) {
        var al = AEROLINEAS.find(function(a){ return String(a.id) === String(t.aerolinea_id); });
        if (al) companiaVal = al.codigo || al.nombre || '';
    }
    modal.querySelector('[name="compania"]').value = companiaVal;

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
