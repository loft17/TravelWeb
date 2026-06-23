<div class="form-group">
    <label>Tipo</label>
    <select name="tipo" class="form-control tr-tipo-select">
        <?php foreach ($tipos as $k => $v): ?>
        <option value="<?= $k ?>"><?= $v ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group tr-field-carrier">
    <label class="tr-label-carrier">Aerolínea</label>
    <div style="position:relative">
        <input type="text" name="compania" class="form-control tr-carrier-input" placeholder="IB – Iberia" autocomplete="off">
        <input type="hidden" name="aerolinea_id" value="">
        <div class="tr-carrier-dropdown" style="display:none;position:absolute;z-index:9999;background:#fff;border:1px solid #ced4da;border-radius:4px;width:100%;max-height:200px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,.15)"></div>
    </div>
</div>

<!-- ── SALIDA ── -->
<p class="tr-section-hdr mb-1">
    Salida
    <span class="tr-section-hint tr-hint-avion" style="display:none">— punto de origen del primer tramo</span>
</p>
<div class="form-row">
    <div class="form-group col-5">
        <label class="tr-label-origen">
            Código
            <small class="text-muted tr-hint-avion" style="display:none">(IATA, ej: MAD)</small>
        </label>
        <input type="text" name="origen" class="form-control tr-placeholder-origen" placeholder="MAD" required>
    </div>
    <div class="form-group col-4">
        <label>Fecha salida</label>
        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="form-group col-3">
        <label>Hora salida</label>
        <input type="time" name="hora_salida" class="form-control">
    </div>
</div>
<div class="form-row">
    <div class="form-group col-5">
        <label style="font-size:.8em;color:#888">Ciudad origen</label>
        <input type="text" name="ciudad_origen" class="form-control form-control-sm" placeholder="Madrid">
    </div>
    <div class="form-group col-7">
        <label class="tr-label-terminal-o" style="font-size:.8em;color:#888">
            Nombre completo
            <span class="tr-hint-avion" style="font-size:.85em;display:none">(aeropuerto)</span>
        </label>
        <input type="text" name="aeropuerto_origen" class="form-control form-control-sm tr-placeholder-terminal-o" placeholder="Adolfo Suárez Madrid-Barajas">
    </div>
</div>

<!-- ── LLEGADA ── -->
<p class="tr-section-hdr mb-1">
    Llegada
    <span class="tr-section-hint tr-hint-avion" style="display:none">— destino final del vuelo</span>
    <span class="tr-section-hint tr-hint-no-avion" style="display:none">— punto de llegada</span>
</p>
<p class="tr-section-note tr-hint-avion mb-2" style="display:none">
    <i class="fa fa-info-circle"></i>
    Si el vuelo tiene <strong>escalas</strong>, pon aquí el <strong>aeropuerto final</strong> (no la escala). Las escalas se añaden abajo.
</p>
<div class="form-row">
    <div class="form-group col-5">
        <label class="tr-label-destino">
            Código
            <small class="text-muted tr-hint-avion" style="display:none">(IATA, ej: NRT)</small>
        </label>
        <input type="text" name="destino" class="form-control tr-placeholder-destino" placeholder="NRT" required>
    </div>
    <div class="form-group col-4">
        <label>Fecha llegada <small class="text-muted">(si diferente)</small></label>
        <input type="date" name="fecha_llegada" class="form-control fecha-llegada-field">
    </div>
    <div class="form-group col-3">
        <label>Hora llegada</label>
        <input type="time" name="hora_llegada" class="form-control">
    </div>
</div>
<div class="form-row">
    <div class="form-group col-5">
        <label style="font-size:.8em;color:#888">Ciudad destino</label>
        <input type="text" name="ciudad_destino" class="form-control form-control-sm" placeholder="Tokio">
    </div>
    <div class="form-group col-7">
        <label class="tr-label-terminal-d" style="font-size:.8em;color:#888">
            Nombre completo
            <span class="tr-hint-avion" style="font-size:.85em;display:none">(aeropuerto destino final)</span>
        </label>
        <input type="text" name="aeropuerto_destino" class="form-control form-control-sm tr-placeholder-terminal-d" placeholder="Aeropuerto de Narita">
    </div>
</div>

<!-- ── DETALLES ── -->
<div class="form-row">
    <div class="form-group col-4">
        <label>Duración total</label>
        <input type="text" name="duracion" class="form-control" placeholder="2h30">
    </div>
    <div class="form-group col-8">
        <label class="tr-label-numero">Nº vuelo / ref.</label>
        <input type="text" name="numero" class="form-control tr-placeholder-numero" placeholder="IB1234">
    </div>
</div>
<div class="form-group">
    <label>Notas</label>
    <textarea name="notas" class="form-control" rows="2" placeholder="Terminal, asiento, equipaje…"></textarea>
</div>

<script>
(function () {
    var TR_META = {
        avion:  { origen: 'Código',              origenPh: 'MAD',        destino: 'Código',           destinoPh: 'NRT',      termO: 'Aeropuerto (nombre completo)',  termOPh: 'Adolfo Suárez Madrid-Barajas', termD: 'Aeropuerto destino final',      termDPh: 'Aeropuerto de Narita',   numero: 'Nº vuelo',           numeroPh: 'IB1234',    compania: false, esAvion: true  },
        bus:    { origen: 'Ciudad / Parada',     origenPh: 'Madrid',     destino: 'Ciudad / Parada',  destinoPh: 'Barcelona',termO: 'Estación (nombre completo)',    termOPh: 'Estación Sur Madrid',         termD: 'Estación destino (nombre)',    termDPh: 'Estació del Nord',       numero: 'Nº billete / ref.',  numeroPh: 'ALSA-1234', compania: true,  esAvion: false },
        tren:   { origen: 'Estación origen',     origenPh: 'Madrid',     destino: 'Estación destino', destinoPh: 'Barcelona',termO: 'Nombre estación origen',        termOPh: 'Madrid Atocha',               termD: 'Nombre estación destino',      termDPh: 'Barcelona Sants',        numero: 'Nº tren / billete',  numeroPh: 'AVE 02131', compania: true,  esAvion: false },
        ferry:  { origen: 'Puerto origen',       origenPh: 'Barcelona',  destino: 'Puerto destino',   destinoPh: 'Palma',    termO: 'Terminal embarque',             termOPh: 'Terminal Drassanes',          termD: 'Terminal llegada',             termDPh: 'Terminal Palma',         numero: 'Nº travesía / ref.', numeroPh: 'TFS-001',   compania: true,  esAvion: false },
        taxi:   { origen: 'Dirección recogida',  origenPh: 'Hotel X',    destino: 'Destino',          destinoPh: 'Aeropuerto',termO: 'Punto de recogida',            termOPh: 'Puerta del hotel',            termD: 'Punto de destino',             termDPh: 'T4 Aeropuerto',          numero: 'Referencia',         numeroPh: 'REF-123',   compania: true,  esAvion: false },
        coche:  { origen: 'Desde',               origenPh: 'Madrid',     destino: 'Hasta',            destinoPh: 'Zaragoza', termO: 'Punto de salida',               termOPh: 'Dirección o parking',         termD: 'Punto de llegada',             termDPh: 'Dirección o parking',    numero: 'Referencia',         numeroPh: 'RENT-001',  compania: true,  esAvion: false },
        otro:   { origen: 'Origen',              origenPh: 'Origen',     destino: 'Destino',          destinoPh: 'Destino',  termO: 'Terminal / parada',             termOPh: 'Terminal',                    termD: 'Terminal / parada destino',    termDPh: 'Terminal',               numero: 'Nº / ref.',          numeroPh: 'REF-001',   compania: true,  esAvion: false }
    };

    document.querySelectorAll('.tr-tipo-select').forEach(function (sel) {
        function update() {
            var meta = TR_META[sel.value] || TR_META['otro'];
            var form = sel.closest('form');

            // Label del carrier cambia según tipo
            var elCarrier = form.querySelector('.tr-field-carrier');
            if (elCarrier) {
                var labelCarrier = elCarrier.querySelector('.tr-label-carrier');
                if (labelCarrier) labelCarrier.textContent = meta.esAvion ? 'Aerolínea' : 'Compañía';
                var phCarrier = elCarrier.querySelector('.tr-carrier-input');
                if (phCarrier) phCarrier.placeholder = meta.esAvion ? 'IB – Iberia' : meta.compania ? 'Ej: ALSA, Renfe, Baleària…' : '–';
            }

            // Hints visibles solo para avión
            form.querySelectorAll('.tr-hint-avion').forEach(function(el) {
                el.style.display = meta.esAvion ? '' : 'none';
            });
            form.querySelectorAll('.tr-hint-no-avion').forEach(function(el) {
                el.style.display = !meta.esAvion ? '' : 'none';
            });

            // Labels dinámicos
            form.querySelectorAll('.tr-label-origen').forEach(function(el) {
                // conserva el <small> hijo si existe
                var small = el.querySelector('small');
                el.firstChild.textContent = meta.origen + ' ';
                if (small) el.appendChild(small);
            });
            form.querySelectorAll('.tr-label-destino').forEach(function(el) {
                var small = el.querySelector('small');
                el.firstChild.textContent = meta.destino + ' ';
                if (small) el.appendChild(small);
            });
            form.querySelectorAll('.tr-label-terminal-o').forEach(function(el) { el.childNodes[0].textContent = meta.termO + ' '; });
            form.querySelectorAll('.tr-label-terminal-d').forEach(function(el) { el.childNodes[0].textContent = meta.termD + ' '; });
            form.querySelectorAll('.tr-label-numero').forEach(function(el)     { el.textContent = meta.numero; });

            // Placeholders
            form.querySelectorAll('.tr-placeholder-origen').forEach(function(el)    { el.placeholder = meta.origenPh; });
            form.querySelectorAll('.tr-placeholder-destino').forEach(function(el)   { el.placeholder = meta.destinoPh; });
            form.querySelectorAll('.tr-placeholder-terminal-o').forEach(function(el){ el.placeholder = meta.termOPh; });
            form.querySelectorAll('.tr-placeholder-terminal-d').forEach(function(el){ el.placeholder = meta.termDPh; });
            form.querySelectorAll('.tr-placeholder-numero').forEach(function(el)    { el.placeholder = meta.numeroPh; });
        }
        sel.addEventListener('change', update);
        update();
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

    // ── AIRPORT AUTOCOMPLETE ──────────────────────────────────────────────────
    // Fills city + full-name fields when a 3-letter IATA code is entered
    function applyAirport(codeInput, data) {
        var form = codeInput.closest('form');
        if (!form) return;
        var code = codeInput.value.trim().toUpperCase();
        if (code.length !== 3) return;
        var info = data[code];
        if (!info) return;

        // Determine if this is the origen or destino input
        var name = codeInput.name; // 'origen' or 'destino'
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
        badge.className = 'tr-apt-badge text-success';
        badge.style.cssText = 'display:block;margin-top:2px;font-size:.75em';
        badge.textContent = '✓ ' + info.c + ' · ' + info.n;
        input.parentNode.appendChild(badge);
    }

    // Escala airport autocomplete via event delegation
    function applyEscalaAirport(input, data) {
        var code = input.value.trim().toUpperCase();
        if (code.length !== 3) return;
        var info = data[code];
        if (!info) return;

        var row = input.closest('.escala-row');
        if (!row) return;

        // Determine field role from name attribute
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

        // Main form airport inputs (only when tipo=avion)
        if (el.matches('[name="origen"],[name="destino"]')) {
            var form = el.closest('form');
            if (!form) return;
            var tipoSel = form.querySelector('[name="tipo"]');
            if (!tipoSel || tipoSel.value !== 'avion') return;
            if (el.value.trim().length !== 3) return;
            loadAirports(function(d){ applyAirport(el, d); });
        }

        // Escala airport inputs
        if (el.matches('[name$="[aeropuerto]"],[name$="[destino_sig]"]')) {
            if (el.value.trim().length !== 3) return;
            loadAirports(function(d){ applyEscalaAirport(el, d); });
        }
    }, true);

    // Also trigger on uppercase + 3-char keyup for instant feedback
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

    // ── CARRIER AUTOCOMPLETE ──────────────────────────────────────────────────
    var TIPO_CARRIER_MAP = { avion:'aerolinea', tren:'tren', ferry:'ferry', bus:'bus', taxi:null, coche:null, otro:null };

    function openCarrierDropdown(input, carriers) {
        closeDropdowns(); // close others first
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
            item.innerHTML = (c.icono ? '<img src="'+c.icono+'" style="height:18px;object-fit:contain;flex-shrink:0" onerror="this.style.display=\'none\'">' : '') +
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
        badge.className = 'tr-carrier-badge text-success';
        badge.style.cssText = 'display:block;margin-top:2px;font-size:.75em';
        badge.innerHTML = (carrier.icono ? '<img src="'+carrier.icono+'" style="height:14px;margin-right:4px;object-fit:contain" onerror="this.style.display=\'none\'">' : '') +
                          '✓ ' + carrier.nombre;
        input.parentNode.appendChild(badge);
    }

    document.addEventListener('input', function(e) {
        var el = e.target;
        if (!el.matches || !el.matches('.tr-carrier-input')) return;
        // Clear badge on new input
        var badge = el.parentNode.querySelector('.tr-carrier-badge');
        if (badge) badge.remove();
        loadCarriers(function(d){ openCarrierDropdown(el, d); });
    });

    document.addEventListener('focus', function(e) {
        var el = e.target;
        if (!el.matches || !el.matches('.tr-carrier-input')) return;
        if (el.value.trim() === '') {
            loadCarriers(function(d){ openCarrierDropdown(el, d); });
        }
    }, true);

    document.addEventListener('blur', function(e) {
        var el = e.target;
        if (!el.matches || !el.matches('.tr-carrier-input')) return;
        setTimeout(closeDropdowns, 150);

        // On blur, look up full carrier info to show badge
        var code = el.value.trim().toUpperCase();
        if (!code) return;
        loadCarriers(function(carriers) {
            var match = carriers.find(function(c){ return c.codigo.toUpperCase() === code; });
            if (match) showCarrierBadge(el, match);
        });
    }, true);

    document.addEventListener('click', function(e) {
        if (!e.target.matches('.tr-carrier-input')) closeDropdowns();
    });

    // On modal open/type change: restore carrier badge if field already has a value
    document.addEventListener('change', function(e) {
        if (e.target.matches('.tr-tipo-select')) {
            var form = e.target.closest('form');
            if (!form) return;
            var carrierInput = form.querySelector('.tr-carrier-input');
            if (carrierInput && carrierInput.value) {
                var badge = carrierInput.parentNode.querySelector('.tr-carrier-badge');
                if (badge) badge.remove();
            }
        }
    });

    // Show badge on page load for pre-filled carrier inputs
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
