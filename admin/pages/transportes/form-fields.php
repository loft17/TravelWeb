<?php
// form-fields.php — campos del formulario de transporte (incluido en add y edit modal)
?>

<!-- ── SELECTOR DE TIPO ─────────────────────────────────────────────── -->
<div class="form-group mb-3">
    <label class="tr-field-label mb-2">Tipo de transporte</label>
    <div class="tr-tipo-btns">
        <button type="button" class="tr-tipo-btn" data-tipo="avion">
            <i class="fa fa-plane"></i><span>Avión</span>
        </button>
        <button type="button" class="tr-tipo-btn" data-tipo="tren">
            <i class="fa fa-train"></i><span>Tren</span>
        </button>
        <button type="button" class="tr-tipo-btn" data-tipo="bus">
            <i class="fa fa-bus"></i><span>Bus</span>
        </button>
        <button type="button" class="tr-tipo-btn" data-tipo="ferry">
            <i class="fa fa-ship"></i><span>Ferry</span>
        </button>
        <button type="button" class="tr-tipo-btn" data-tipo="taxi">
            <i class="fa fa-taxi"></i><span>Taxi</span>
        </button>
        <button type="button" class="tr-tipo-btn" data-tipo="coche">
            <i class="fa fa-car"></i><span>Coche</span>
        </button>
        <button type="button" class="tr-tipo-btn" data-tipo="otro">
            <i class="fa fa-ellipsis-h"></i><span>Otro</span>
        </button>
    </div>
    <!-- Select oculto — lo usa el JS existente de escalas y abrirEditar -->
    <select name="tipo" class="tr-tipo-select" style="position:absolute;opacity:0;pointer-events:none;height:0;overflow:hidden" tabindex="-1" aria-hidden="true">
        <?php foreach ($tipos as $k => $v): ?>
        <option value="<?= $k ?>"><?= $v ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- ── COMPAÑÍA / AEROLÍNEA (oculto para taxi, coche) ──────────────── -->
<div class="form-group tr-vis-carrier mb-3">
    <label class="tr-label-carrier tr-field-label">Aerolínea</label>
    <div style="position:relative">
        <input type="text" name="compania" class="form-control tr-carrier-input" placeholder="IB – Iberia" autocomplete="off">
        <input type="hidden" name="aerolinea_id" value="">
        <div class="tr-carrier-dropdown" style="display:none;position:absolute;z-index:9999;background:#fff;border:1px solid #ced4da;border-radius:4px;width:100%;max-height:200px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,.15)"></div>
    </div>
</div>

<!-- ── SECCIÓN SALIDA ───────────────────────────────────────────────── -->
<div class="tr-section tr-section-salida mb-2">
    <div class="tr-sec-hdr tr-sec-hdr-salida">
        <i class="fa fa-sign-out"></i>
        <span class="tr-sec-title">Salida</span>
        <span class="tr-hint-avion tr-sec-sub" style="display:none">aeropuerto de origen (IATA)</span>
    </div>
    <div class="form-row tr-sec-body">
        <div class="form-group col-5 mb-2">
            <label class="tr-label-origen tr-field-label">
                Código <small class="text-muted tr-hint-avion" style="display:none;font-weight:400">(IATA)</small>
            </label>
            <input type="text" name="origen" class="form-control tr-placeholder-origen" placeholder="MAD" required autocomplete="off">
        </div>
        <div class="form-group col-4 mb-2">
            <label class="tr-field-label">Fecha salida</label>
            <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group col-3 mb-2">
            <label class="tr-field-label">Hora</label>
            <input type="time" name="hora_salida" class="form-control">
        </div>
    </div>
    <!-- Sub-campos: ciudad + nombre terminal (ocultos para taxi/coche) -->
    <div class="form-row tr-vis-terminal tr-sec-body tr-subfields">
        <div class="form-group col-4 mb-1">
            <input type="text" name="ciudad_origen" class="form-control form-control-sm" placeholder="Ciudad" autocomplete="off">
        </div>
        <div class="form-group col-8 mb-1">
            <input type="text" name="aeropuerto_origen" class="form-control form-control-sm tr-placeholder-terminal-o" placeholder="Nombre completo del aeropuerto" autocomplete="off">
        </div>
    </div>
</div>

<!-- ── SECCIÓN LLEGADA ──────────────────────────────────────────────── -->
<div class="tr-section tr-section-llegada mb-2">
    <div class="tr-sec-hdr tr-sec-hdr-llegada">
        <i class="fa fa-sign-in"></i>
        <span class="tr-sec-title">Llegada</span>
        <span class="tr-hint-avion tr-sec-sub" style="display:none">destino final del vuelo</span>
    </div>
    <div class="tr-hint-avion tr-escalas-note" style="display:none">
        <i class="fa fa-info-circle"></i>
        Si el vuelo tiene <strong>escalas</strong>, pon aquí el destino de la <strong>primera escala</strong>. Las conexiones se añaden abajo.
    </div>
    <div class="form-row tr-sec-body">
        <div class="form-group col-5 mb-2">
            <label class="tr-label-destino tr-field-label">
                Código <small class="text-muted tr-hint-avion" style="display:none;font-weight:400">(IATA)</small>
            </label>
            <input type="text" name="destino" class="form-control tr-placeholder-destino" placeholder="NRT" required autocomplete="off">
        </div>
        <div class="form-group col-4 mb-2">
            <label class="tr-field-label">Fecha llegada <small class="text-muted" style="font-weight:400">(si diferente)</small></label>
            <input type="date" name="fecha_llegada" class="form-control fecha-llegada-field">
        </div>
        <div class="form-group col-3 mb-2">
            <label class="tr-field-label">Hora</label>
            <input type="time" name="hora_llegada" class="form-control">
        </div>
    </div>
    <!-- Sub-campos: ciudad + nombre terminal (ocultos para taxi/coche) -->
    <div class="form-row tr-vis-terminal tr-sec-body tr-subfields">
        <div class="form-group col-4 mb-1">
            <input type="text" name="ciudad_destino" class="form-control form-control-sm" placeholder="Ciudad" autocomplete="off">
        </div>
        <div class="form-group col-8 mb-1">
            <input type="text" name="aeropuerto_destino" class="form-control form-control-sm tr-placeholder-terminal-d" placeholder="Nombre completo del aeropuerto" autocomplete="off">
        </div>
    </div>
</div>

<!-- ── DETALLES ──────────────────────────────────────────────────────── -->
<div class="form-row">
    <div class="form-group col-4 tr-vis-duracion">
        <label class="tr-field-label">Duración total</label>
        <input type="text" name="duracion" class="form-control" placeholder="2h 30m">
    </div>
    <div class="form-group tr-numero-col">
        <label class="tr-label-numero tr-field-label">Nº vuelo / ref.</label>
        <input type="text" name="numero" class="form-control tr-placeholder-numero" placeholder="IB1234">
    </div>
</div>

<div class="form-group">
    <label class="tr-field-label">Notas</label>
    <textarea name="notas" class="form-control" rows="2" placeholder="Terminal, asiento, equipaje, confirmación…"></textarea>
</div>

<script>
(function () {
    var TR_META = {
        avion: {
            origenLabel: 'Código', origenPh: 'MAD',
            destinoLabel: 'Código', destinoPh: 'NRT',
            termOPh: 'Adolfo Suárez Madrid-Barajas', termDPh: 'Aeropuerto de Narita',
            numeroPh: 'IB1234', numeroLabel: 'Nº vuelo',
            carrierLabel: 'Aerolínea', carrierPh: 'IB – Iberia',
            showCarrier: true, showTerminal: true, showDetails: true,
            esAvion: true
        },
        tren: {
            origenLabel: 'Estación origen', origenPh: 'Madrid',
            destinoLabel: 'Estación destino', destinoPh: 'Barcelona',
            termOPh: 'Madrid Atocha', termDPh: 'Barcelona Sants',
            numeroPh: 'AVE 02131', numeroLabel: 'Nº tren / billete',
            carrierLabel: 'Compañía', carrierPh: 'RENFE',
            showCarrier: true, showTerminal: true, showDetails: true,
            esAvion: false
        },
        bus: {
            origenLabel: 'Ciudad / Parada', origenPh: 'Madrid',
            destinoLabel: 'Ciudad / Parada', destinoPh: 'Barcelona',
            termOPh: 'Estación Sur Madrid', termDPh: 'Estació del Nord',
            numeroPh: 'ALSA-1234', numeroLabel: 'Nº billete',
            carrierLabel: 'Compañía', carrierPh: 'ALSA',
            showCarrier: true, showTerminal: true, showDetails: true,
            esAvion: false
        },
        ferry: {
            origenLabel: 'Puerto origen', origenPh: 'Barcelona',
            destinoLabel: 'Puerto destino', destinoPh: 'Palma',
            termOPh: 'Terminal Drassanes', termDPh: 'Terminal Palma',
            numeroPh: 'TFS-001', numeroLabel: 'Nº travesía',
            carrierLabel: 'Compañía', carrierPh: 'Baleària',
            showCarrier: true, showTerminal: true, showDetails: true,
            esAvion: false
        },
        taxi: {
            origenLabel: 'Recogida', origenPh: 'Hotel / dirección',
            destinoLabel: 'Destino', destinoPh: 'Aeropuerto / dirección',
            termOPh: '', termDPh: '',
            numeroPh: 'REF-123', numeroLabel: 'Referencia',
            carrierLabel: '', carrierPh: '',
            showCarrier: false, showTerminal: false, showDetails: false,
            esAvion: false
        },
        coche: {
            origenLabel: 'Desde', origenPh: 'Ciudad / dirección',
            destinoLabel: 'Hasta', destinoPh: 'Ciudad / dirección',
            termOPh: '', termDPh: '',
            numeroPh: 'RENT-001', numeroLabel: 'Referencia',
            carrierLabel: '', carrierPh: '',
            showCarrier: false, showTerminal: false, showDetails: false,
            esAvion: false
        },
        otro: {
            origenLabel: 'Origen', origenPh: 'Origen',
            destinoLabel: 'Destino', destinoPh: 'Destino',
            termOPh: 'Terminal / parada', termDPh: 'Terminal / parada',
            numeroPh: 'REF-001', numeroLabel: 'Referencia',
            carrierLabel: '', carrierPh: '',
            showCarrier: false, showTerminal: false, showDetails: true,
            esAvion: false
        }
    };

    // Colors per tipo for the active button
    var TR_COLOR = {
        avion:  '#4e8cff',
        tren:   '#28a745',
        bus:    '#fd7e14',
        ferry:  '#17a2b8',
        taxi:   '#e0a800',
        coche:  '#6c757d',
        otro:   '#adb5bd'
    };

    function show(el) { if (el) el.style.display = ''; }
    function hide(el) { if (el) el.style.display = 'none'; }
    function showAll(form, sel) { form.querySelectorAll(sel).forEach(show); }
    function hideAll(form, sel) { form.querySelectorAll(sel).forEach(hide); }

    function applyMeta(form, tipo) {
        var meta = TR_META[tipo] || TR_META['otro'];

        // ── Buttons active state
        form.querySelectorAll('.tr-tipo-btn').forEach(function(btn) {
            var t = btn.dataset.tipo;
            var isActive = (t === tipo);
            btn.classList.toggle('tr-tipo-btn-active', isActive);
            btn.style.borderColor  = isActive ? (TR_COLOR[t] || '#4e8cff') : '';
            btn.style.color        = isActive ? (TR_COLOR[t] || '#4e8cff') : '';
            btn.style.background   = isActive ? (TR_COLOR[t] || '#4e8cff') + '18' : '';
        });

        // ── Visibility groups
        var vis = function(sel, show_) {
            form.querySelectorAll(sel).forEach(function(el){ el.style.display = show_ ? '' : 'none'; });
        };
        vis('.tr-vis-carrier',  meta.showCarrier);
        vis('.tr-vis-terminal', meta.showTerminal);
        vis('.tr-vis-duracion', meta.showCarrier); // hidden only for taxi/coche (no carrier)
        vis('.tr-hint-avion',   meta.esAvion);

        // Adjust numero column width: full when no duracion, partial when duracion shown
        form.querySelectorAll('.tr-numero-col').forEach(function(el) {
            el.className = el.className.replace(/\bcol-\d+\b/g, '').trim();
            el.classList.add(meta.showCarrier ? 'col-8' : 'col-12');
        });

        // ── Carrier label / placeholder
        var carrierLabel = form.querySelector('.tr-label-carrier');
        var carrierInput = form.querySelector('.tr-carrier-input');
        if (carrierLabel) carrierLabel.textContent = meta.carrierLabel || 'Compañía';
        if (carrierInput) carrierInput.placeholder = meta.carrierPh || '';

        // ── Origen label / placeholder
        form.querySelectorAll('.tr-label-origen').forEach(function(el) {
            var small = el.querySelector('small');
            el.firstChild.textContent = meta.origenLabel + ' ';
            if (small) el.appendChild(small);
        });
        form.querySelectorAll('.tr-placeholder-origen').forEach(function(el) { el.placeholder = meta.origenPh; });

        // ── Destino label / placeholder
        form.querySelectorAll('.tr-label-destino').forEach(function(el) {
            var small = el.querySelector('small');
            el.firstChild.textContent = meta.destinoLabel + ' ';
            if (small) el.appendChild(small);
        });
        form.querySelectorAll('.tr-placeholder-destino').forEach(function(el) { el.placeholder = meta.destinoPh; });

        // ── Terminal placeholders
        form.querySelectorAll('.tr-placeholder-terminal-o').forEach(function(el) { el.placeholder = meta.termOPh; });
        form.querySelectorAll('.tr-placeholder-terminal-d').forEach(function(el) { el.placeholder = meta.termDPh; });

        // ── Número label / placeholder
        form.querySelectorAll('.tr-label-numero').forEach(function(el) { el.textContent = meta.numeroLabel; });
        form.querySelectorAll('.tr-placeholder-numero').forEach(function(el) { el.placeholder = meta.numeroPh; });

        // ── Section header colors
        var sColor = meta.esAvion ? '#4e8cff' : (meta.showCarrier ? (TR_COLOR[tipo] || '#28a745') : '#6c757d');
        form.querySelectorAll('.tr-section').forEach(function(sec) {
            sec.style.borderLeftColor = sColor;
        });
    }

    // ── Init each form containing these fields
    document.querySelectorAll('.tr-tipo-select').forEach(function(sel) {
        var form = sel.closest('form');
        if (!form) return;

        // Sync select → UI on change (also triggered by abrirEditar)
        sel.addEventListener('change', function() {
            applyMeta(form, sel.value);
        });

        // Button clicks → update select + trigger change
        form.querySelectorAll('.tr-tipo-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                sel.value = btn.dataset.tipo;
                sel.dispatchEvent(new Event('change'));
            });
        });

        // Initial render
        applyMeta(form, sel.value || 'avion');
    });
})();
</script>

<?php if (!defined('_TR_AUTOCOMPLETE_JS_LOADED')): define('_TR_AUTOCOMPLETE_JS_LOADED', true); ?>
<script>
(function () {
    var _airports = null;
    var _carriers = null;

    function loadAirports(cb) {
        if (_airports) return cb(_airports);
        fetch('/data/airports.json').then(function(r){ return r.json(); }).then(function(d){ _airports = d; cb(d); }).catch(function(){ _airports = {}; cb({}); });
    }
    function loadCarriers(cb) {
        if (_carriers) return cb(_carriers);
        fetch('/data/carriers.json').then(function(r){ return r.json(); }).then(function(d){ _carriers = d; cb(d); }).catch(function(){ _carriers = []; cb([]); });
    }

    // ── AIRPORT AUTOCOMPLETE ─────────────────────────────────────────────────
    function applyAirport(codeInput, data) {
        var form = codeInput.closest('form');
        if (!form) return;
        var code = codeInput.value.trim().toUpperCase();
        if (code.length !== 3) return;
        var info = data[code];
        if (!info) return;
        var name = codeInput.name;
        if (name === 'origen') {
            var fCity = form.querySelector('[name="ciudad_origen"]');
            var fApt  = form.querySelector('[name="aeropuerto_origen"]');
            if (fCity && !fCity.value) fCity.value = info.c;
            if (fApt  && !fApt.value)  fApt.value  = info.n;
            showAptBadge(codeInput, info);
        } else if (name === 'destino') {
            var fCity = form.querySelector('[name="ciudad_destino"]');
            var fApt  = form.querySelector('[name="aeropuerto_destino"]');
            if (fCity && !fCity.value) fCity.value = info.c;
            if (fApt  && !fApt.value)  fApt.value  = info.n;
            showAptBadge(codeInput, info);
        }
    }

    function showAptBadge(input, info) {
        var existing = input.parentNode.querySelector('.tr-apt-badge');
        if (existing) existing.remove();
        if (!info) return;
        var badge = document.createElement('small');
        badge.className = 'tr-apt-badge';
        badge.style.cssText = 'display:block;margin-top:3px;font-size:.75em;color:#28a745';
        badge.textContent = '✓ ' + info.c + ' · ' + info.n;
        input.parentNode.appendChild(badge);
    }

    function applyEscalaAirport(input, data) {
        var code = input.value.trim().toUpperCase();
        if (code.length !== 3) return;
        var info = data[code];
        if (!info) return;
        var row = input.closest('.escala-row');
        if (!row) return;
        var nm = input.name || '';
        if (nm.indexOf('[aeropuerto]') !== -1) {
            var fCity = row.querySelector('[name$="[ciudad]"]');
            var fApt  = row.querySelector('[name$="[aeropuerto_nombre]"]');
            if (fCity && !fCity.value) fCity.value = info.c;
            if (fApt  && !fApt.value)  fApt.value  = info.n;
            showAptBadge(input, info);
        } else if (nm.indexOf('[destino_sig]') !== -1) {
            var fCity = row.querySelector('[name$="[ciudad_sig]"]');
            var fApt  = row.querySelector('[name$="[aeropuerto_nombre_sig]"]');
            if (fCity && !fCity.value) fCity.value = info.c;
            if (fApt  && !fApt.value)  fApt.value  = info.n;
            showAptBadge(input, info);
        }
    }

    document.addEventListener('blur', function(e) {
        var el = e.target;
        if (!el.matches) return;
        if (el.matches('[name="origen"],[name="destino"]')) {
            var form = el.closest('form');
            if (!form) return;
            var tipoSel = form.querySelector('[name="tipo"]');
            if (!tipoSel || tipoSel.value !== 'avion') return;
            if (el.value.trim().length !== 3) return;
            loadAirports(function(d){ applyAirport(el, d); });
        }
        if (el.matches('[name$="[aeropuerto]"],[name$="[destino_sig]"]')) {
            if (el.value.trim().length !== 3) return;
            loadAirports(function(d){ applyEscalaAirport(el, d); });
        }
    }, true);

    document.addEventListener('keyup', function(e) {
        var el = e.target;
        if (!el.matches) return;
        if (!el.matches('[name="origen"],[name="destino"],[name$="[aeropuerto]"],[name$="[destino_sig]"]')) return;
        var v = el.value.trim();
        if (v.length === 3 && /^[A-Za-z]{3}$/.test(v)) {
            el.value = v.toUpperCase();
            el.dispatchEvent(new Event('blur', {bubbles:true}));
        }
    });

    // ── CARRIER AUTOCOMPLETE ─────────────────────────────────────────────────
    var TIPO_CARRIER_MAP = { avion:'aerolinea', tren:'tren', ferry:'ferry', bus:'bus', taxi:null, coche:null, otro:null };

    function openCarrierDropdown(input, carriers) {
        closeDropdowns();
        var dropdown = input.parentNode.querySelector('.tr-carrier-dropdown');
        if (!dropdown) return;
        var form = input.closest('form');
        var tipoSel = form ? form.querySelector('[name="tipo"]') : null;
        var tipo = tipoSel ? tipoSel.value : 'otro';
        var filterTipo = TIPO_CARRIER_MAP[tipo] || null;
        var query = input.value.trim().toLowerCase();
        var filtered = carriers.filter(function(c) {
            if (filterTipo && c.tipo !== filterTipo) return false;
            if (!query) return true;
            return (c.codigo && c.codigo.toLowerCase().indexOf(query) !== -1) ||
                   (c.nombre && c.nombre.toLowerCase().indexOf(query) !== -1);
        }).slice(0, 12);
        if (filtered.length === 0) { dropdown.style.display = 'none'; return; }
        dropdown.innerHTML = '';
        filtered.forEach(function(c) {
            var item = document.createElement('div');
            item.style.cssText = 'padding:6px 10px;cursor:pointer;font-size:.87em;display:flex;align-items:center;gap:8px;border-bottom:1px solid #f0f0f0';
            item.innerHTML = (c.icono ? '<img src="'+c.icono+'" style="height:18px;object-fit:contain;flex-shrink:0" onerror="this.style.display=\'none\'">' : '<span style="width:18px;display:inline-block"></span>') +
                             '<span><strong>' + c.codigo + '</strong> – ' + c.nombre + '</span>';
            item.addEventListener('mousedown', function(ev) {
                ev.preventDefault();
                input.value = c.codigo;
                dropdown.style.display = 'none';
                showCarrierBadge(input, c);
            });
            item.addEventListener('mouseover', function(){ item.style.background = '#f5f7fa'; });
            item.addEventListener('mouseout',  function(){ item.style.background = ''; });
            dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
    }

    function closeDropdowns() {
        document.querySelectorAll('.tr-carrier-dropdown').forEach(function(d){ d.style.display = 'none'; });
    }

    function showCarrierBadge(input, carrier) {
        var existing = input.parentNode.querySelector('.tr-carrier-badge');
        if (existing) existing.remove();
        if (!carrier) return;
        var badge = document.createElement('small');
        badge.className = 'tr-carrier-badge';
        badge.style.cssText = 'display:flex;align-items:center;gap:4px;margin-top:3px;font-size:.75em;color:#28a745';
        badge.innerHTML = (carrier.icono ? '<img src="'+carrier.icono+'" style="height:14px;object-fit:contain" onerror="this.style.display=\'none\'">' : '') +
                          '<span>✓ ' + carrier.nombre + '</span>';
        input.parentNode.appendChild(badge);
    }

    document.addEventListener('input', function(e) {
        var el = e.target;
        if (!el.matches || !el.matches('.tr-carrier-input')) return;
        var badge = el.parentNode.querySelector('.tr-carrier-badge');
        if (badge) badge.remove();
        loadCarriers(function(d){ openCarrierDropdown(el, d); });
    });

    document.addEventListener('focus', function(e) {
        var el = e.target;
        if (!el.matches || !el.matches('.tr-carrier-input')) return;
        if (el.value.trim() === '') loadCarriers(function(d){ openCarrierDropdown(el, d); });
    }, true);

    document.addEventListener('blur', function(e) {
        var el = e.target;
        if (!el.matches || !el.matches('.tr-carrier-input')) return;
        setTimeout(closeDropdowns, 150);
        var code = el.value.trim().toUpperCase();
        if (!code) return;
        loadCarriers(function(carriers) {
            var match = carriers.find(function(c){ return c.codigo.toUpperCase() === code; });
            if (match) showCarrierBadge(el, match);
        });
    }, true);

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.tr-carrier-input, .tr-carrier-dropdown')) closeDropdowns();
    });

    document.addEventListener('DOMContentLoaded', function() {
        loadCarriers(function(carriers) {
            document.querySelectorAll('.tr-carrier-input').forEach(function(input) {
                var code = input.value.trim().toUpperCase();
                if (!code) return;
                var match = carriers.find(function(c){ return c.codigo.toUpperCase() === code; });
                if (match) showCarrierBadge(input, match);
            });
        });
    });
})();
</script>
<?php endif; ?>
