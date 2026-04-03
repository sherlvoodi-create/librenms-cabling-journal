<!-- resources/views/modals.php -->
<div id="addPanelModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Добавить в Unit <span id="modal-u-num"></span></h4>
        </div>
        <div class="modal-body">
            <input type="hidden" name="action" value="add_item">
            <input type="hidden" name="rack_id" value="<?= intval($rack_id) ?>">
            <input type="hidden" name="start_unit" id="modal-u-input">

            <div class="form-group">
                <label>Тип:</label>
                <select name="item_type" class="form-control" onchange="toggleFormFields(this.value)">
                    <option value="panel">Патч-панель / Муфта</option>
                    <option value="device">Устройство из LibreNMS</option>
                </select>
            </div>

            <div id="fields-panel">
                <div class="form-group"><label>Название:</label><input type="text" name="panel_name" class="form-control" placeholder="Напр. ODF-01"></div>
                <div class="form-group">
                    <label>Тип:</label>
                    <select name="panel_kind" class="form-control">
                        <option value="Patch-Panel">Patch-Panel</option>
                        <option value="Splice-Closure">Splice-Closure (Муфта)</option>
                    </select>
                </div>
                <div class="form-group"><label>Портов:</label><input type="number" name="port_count" class="form-control" value="24"></div>
            </div>

            <div id="fields-device" style="display:none;">
                <div class="form-group">
                    <label>Устройство:</label>
                    <select name="device_id" class="form-control">
                        <?php 
                        $all_devices = DB::select("SELECT device_id, hostname FROM devices ORDER BY hostname");
                        foreach($all_devices as $d) echo "<option value='{$d->device_id}'>{$d->hostname}</option>"; 
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Сохранить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
function toggleFormFields(val) {
    document.getElementById('fields-panel').style.display = (val === 'panel') ? 'block' : 'none';
    document.getElementById('fields-device').style.display = (val === 'device') ? 'block' : 'none';
}
</script>
