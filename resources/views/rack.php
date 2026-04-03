<!-- resources/views/rack.php -->
<div class="container">
    <br>
    <a href="?" class="btn btn-default">&larr; Назад к списку</a>
    <h2>Шкаф: <?= htmlspecialchars($rack->name) ?> (<?= $rack->units ?>U)</h2>

    <div class="control-panel" style="margin-bottom: 15px; background: #fff; padding: 15px; border-radius: 4px; border: 1px solid #ddd;">
        <label style="cursor:pointer; user-select:none; margin:0;">
            <input type="checkbox" id="hideEmptyUnits" checked onchange="toggleEmpty()"> 
            <span style="margin-left:5px;">Скрывать пустые юниты</span>
        </label>
    </div>

    <div class="rack-container col-md-11">
        <?php for ($u = $rack->units; $u >= 1; $u--): ?>
            <?php 
                $item = $items[$u] ?? null; 
                $class = $item ? ($item['type'] == 'device' ? 'occupied' : 'panel') : 'empty-u';
            ?>
            <div class="rack-unit <?= $class ?>">
                <div class="unit-num"><?= $u ?></div>
                <div class="unit-content">
                    <?php if ($item): ?>
                        <div class="item-header">
                            <?php if ($item['type'] == 'device'): ?>
                                <a href="/device/device=<?= $item['id'] ?>/" target="_blank">
                                    <i class="glyphicon glyphicon-hdd"></i> <strong><?= $item['name'] ?></strong>
                                </a>
                            <?php else: ?>
                                <i class="glyphicon glyphicon-list-alt"></i> <strong><?= $item['name'] ?></strong>
                            <?php endif; ?>
                        </div>
                        <div class="port-grid-wrapper" style="display:flex; align-items: flex-end;">
                            <?= $item['ports_html'] ?>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-xs btn-default add-btn" onclick="openAddModal(<?= $u ?>)">
                            <i class="glyphicon glyphicon-plus"></i> Add
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Блок внешних узлов -->
    <?php if (!empty($external_nodes)): ?>
        <div class="col-md-11" style="margin-top:30px; padding:0;">
            <h3><i class="glyphicon glyphicon-transfer"></i> Внешние узлы</h3>
            <?php foreach ($external_nodes as $node): ?>
                <div class="well-ext">
                    <h4><?= $node->name ?> <small>(<?= $node->type ?>) — <?= $node->distance_from_rack ?>м</small></h4>
                    <div class="fiber-row"><?= $node->ports_html ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
