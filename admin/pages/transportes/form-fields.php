<div class="form-group">
    <label>Tipo</label>
    <select name="tipo" class="form-control">
        <?php foreach ($tipos as $k => $v): ?>
        <option value="<?= $k ?>"><?= $v ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <label>Fecha</label>
    <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
</div>
<div class="form-row">
    <div class="form-group col-6">
        <label>Origen</label>
        <input type="text" name="origen" class="form-control" placeholder="MAD" required>
    </div>
    <div class="form-group col-6">
        <label>Salida</label>
        <input type="time" name="hora_salida" class="form-control">
    </div>
</div>
<div class="form-row">
    <div class="form-group col-6">
        <label>Destino</label>
        <input type="text" name="destino" class="form-control" placeholder="NRT" required>
    </div>
    <div class="form-group col-6">
        <label>Llegada</label>
        <input type="time" name="hora_llegada" class="form-control">
    </div>
</div>
<div class="form-group">
    <label>Nº vuelo / referencia</label>
    <input type="text" name="numero" class="form-control" placeholder="IB1234">
</div>
<div class="form-group">
    <label>Notas</label>
    <textarea name="notas" class="form-control" rows="2" placeholder="Terminal, asiento, equipaje…"></textarea>
</div>
