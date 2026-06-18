<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/gastos.php';

// Datos para el gráfico: agrupados por divisa → categoría → total
$chartData = [];
foreach ($gastos as $g) {
    $div = $g['divisa'];
    $cat = $g['categoria'];
    if (!isset($chartData[$div])) $chartData[$div] = [];
    $chartData[$div][$cat] = ($chartData[$div][$cat] ?? 0) + (float)$g['importe'];
}
?>
<!doctype html>
<html class="no-js" lang="es">
<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="row">
                <div class="col-12 mt-5">

                    <?php if ($gastos_error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($gastos_error) ?></div>
                    <?php endif; ?>
                    <?php if ($gastos_success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($gastos_success) ?></div>
                    <?php endif; ?>

                    <!-- Totales + Gráfico -->
                    <?php if (!empty($totales)): ?>
                    <div class="row mb-4">
                        <?php foreach ($totales as $divisa => $total): ?>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center h-100">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <h6 class="text-muted text-uppercase">Total gastado (<?= htmlspecialchars($divisa) ?>)</h6>
                                    <h3 class="font-weight-bold mb-0"><?= number_format($total, 2) ?> <?= htmlspecialchars($divisa) ?></h3>
                                    <small class="text-muted mt-1"><?= count($gastos) ?> gasto(s)</small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if (!empty($chartData)): ?>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100">
                                <div class="card-body p-2">
                                    <?php if (count($chartData) > 1): ?>
                                    <div class="btn-group btn-group-sm w-100 mb-1" id="divisaTabs">
                                        <?php foreach (array_keys($chartData) as $i => $div): ?>
                                        <button type="button"
                                            class="btn btn-<?= $i === 0 ? 'primary' : 'outline-primary' ?>"
                                            data-divisa="<?= htmlspecialchars($div, ENT_QUOTES) ?>">
                                            <?= htmlspecialchars($div) ?>
                                        </button>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    <div style="position:relative;height:160px;">
                                        <canvas id="gastosChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info">Aún no hay gastos registrados.</div>
                    <?php endif; ?>

                    <!-- Formulario + Tabla -->
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="header-title">Nuevo Gasto</h5>
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="accion" value="agregar">

                                        <div class="form-group">
                                            <label>Categoría</label>
                                            <select name="categoria" class="form-control" required>
                                                <option value="">— selecciona —</option>
                                                <?php foreach ($categorias as $cat): ?>
                                                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Descripción</label>
                                            <input type="text" name="descripcion" class="form-control" placeholder="Ej: Taxi aeropuerto" required>
                                        </div>

                                        <div class="form-row">
                                            <div class="col">
                                                <label>Importe</label>
                                                <input type="number" name="importe" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
                                            </div>
                                            <div class="col">
                                                <label>Divisa</label>
                                                <select name="divisa" class="form-control">
                                                    <?php foreach ($divisas as $div): ?>
                                                        <option value="<?= htmlspecialchars($div) ?>" <?= $div === 'EUR' ? 'selected' : '' ?>><?= htmlspecialchars($div) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group mt-2">
                                            <label>Fecha</label>
                                            <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-block mt-2">
                                            <i class="fa fa-plus"></i> Añadir gasto
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="header-title">Historial de Gastos</h5>
                                    <div class="table-responsive">
                                        <table id="gastosTable" class="table table-hover table-sm">
                                            <thead class="bg-dark text-white text-uppercase">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Categoría</th>
                                                    <th>Descripción</th>
                                                    <th>Importe</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($gastos as $g): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($g['fecha']) ?></td>
                                                <td><span class="badge badge-secondary"><?= htmlspecialchars($g['categoria']) ?></span></td>
                                                <td><?= htmlspecialchars($g['descripcion']) ?></td>
                                                <td class="font-weight-bold"><?= number_format($g['importe'], 2) ?> <?= htmlspecialchars($g['divisa']) ?></td>
                                                <td>
                                                    <form method="post" style="display:inline">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                        <input type="hidden" name="accion" value="borrar">
                                                        <input type="hidden" name="id" value="<?= intval($g['id']) ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            data-confirm="¿Eliminar «<?= htmlspecialchars($g['descripcion'], ENT_QUOTES) ?>»?">
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
                    </div><!-- /row form+tabla -->

                </div><!-- /col-12 mt-5 -->
            </div><!-- /row -->
        </div><!-- /main-content-inner -->
    </div><!-- /main-content -->

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
</div><!-- /page-container -->

<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
$(document).ready(function () {
    $('#gastosTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25,
        language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json' }
    });
});

<?php if (!empty($chartData)): ?>
var chartDataAll = <?= json_encode($chartData, JSON_HEX_TAG) ?>;

var colorMap = {
    'Transporte':  '#4e73df',
    'Alojamiento': '#1cc88a',
    'Comida':      '#f6c23e',
    'Actividades': '#e74a3b',
    'Compras':     '#fd7e14',
    'Otros':       '#858796'
};
var fallbackColors = ['#6f42c1','#20c9a6','#36b9cc','#e83e8c','#17a2b8','#343a40'];

function getColor(label, index) {
    return colorMap[label] || fallbackColors[index % fallbackColors.length];
}

var gastosChart = null;

function renderChart(divisa) {
    var data   = chartDataAll[divisa];
    var labels = Object.keys(data);
    var values = Object.values(data);
    var colors = labels.map(function(l, i){ return getColor(l, i); });

    if (gastosChart) gastosChart.destroy();

    gastosChart = new Chart(document.getElementById('gastosChart'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 8, font: { size: 10 }, boxWidth: 10 }
                },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var total = ctx.dataset.data.reduce(function(a,b){ return a+b; }, 0);
                            var pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ' ' + ctx.label + ': ' + ctx.parsed.toFixed(2) + ' ' + divisa + ' (' + pct + '%)';
                        }
                    }
                }
            },
            cutout: '55%'
        }
    });
}

renderChart(Object.keys(chartDataAll)[0]);

document.querySelectorAll('#divisaTabs button').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('#divisaTabs button').forEach(function(b){
            b.classList.remove('btn-primary');
            b.classList.add('btn-outline-primary');
        });
        btn.classList.add('btn-primary');
        btn.classList.remove('btn-outline-primary');
        renderChart(btn.getAttribute('data-divisa'));
    });
});
<?php endif; ?>
</script>
</body>
</html>
