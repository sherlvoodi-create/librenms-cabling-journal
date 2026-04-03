<div class="container">
    <br><a href="?" class="btn btn-default">&larr; Назад</a>
    <h2>Шкаф: <?= htmlspecialchars($rack->name) ?></h2>
    
    <div class="control-panel">
        <label><input type="checkbox" id="hideEmptyUnits" checked onchange="toggleEmpty()"> Скрывать пустые юниты</label>
    </div>

    <div class="rack-container col-md-11">
        <?php for ($u = $rack->units; $u >= 1; $u--): 
            $item = $items[$u] ?? null;
            $class = $item ? ($item['type'] == 'device' ? 'occupied' : 'panel') : 'empty-u';
        ?>
        <div class="rack-unit <?= $class ?>">
            <div class="unit-num"><?= $u ?></div>
            <div class="unit-content">
                <?php if ($item): ?>
                    <strong><?= $item['name'] ?></strong>
                    <div class="port-grid">
                        <?php foreach ($item['ports'] as $p): 
                            $st = ($item['type'] == 'device' && $p->ifOperStatus == 'up') ? 'port-up' : 'port-down';
                            $tip = $item['type'] == 'device' ? "Port {$p->ifName} | {$p->ifAlias}" : "Panel Port {$p->port_number}";
                            $color = $item['type'] == 'panel' ? ($p->fiber_color ?: '#666') : '';
                        ?>
                            <div class="port-box <?= $st ?>" style="background:<?= $color ?>" data-tip="<?= $tip ?>"></div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <button class="btn btn-xs btn-default" onclick="openAddModal(<?= $u ?>)">+ Add</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>
