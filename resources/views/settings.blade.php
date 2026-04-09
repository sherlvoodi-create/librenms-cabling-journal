<div class="panel panel-default">
    <div class="panel-heading">Добавить модель в справочник</div>
    <div class="panel-body">
        {{-- Форма добавления в справочник данных --}}
        <form method="POST" class="form-inline">
            @csrf
            <input type="hidden" name="settings[model_presets]" id="hidden_settings_field">

            <div class="well well-sm">
                <div class="row">
                    <div class="col-md-2">
                        <label>Тип:</label>
                        <select id="in_type" class="form-control">
                            <option value="link" selected>Кабель</option>
                            <option value="rack">Шкаф (Rack)</option>
                            <option value="panel">Медная панель</option>
                            <option value="fiber">Оптический кросс</option>
                            <option value="ups">ИБП (UPS)</option>
                            <option value="socket220">Блок розеток (PDU)</option>
                            <option value="other">Другой тип</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Модель / Марка:</label>
                        <input type="text" id="in_name" class="form-control"
                            placeholder="напр. Hyperline TWB-FC-1245">
                    </div>
                    <div class="col-md-2">
                        <label>Высота (U):</label>
                        <input type="number" id="in_u" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-2">
                        <label>Порты/Жилы:</label>
                        <input type="number" id="in_ports" class="form-control" value="24" min="0">
                    </div>
                    <div class="col-md-2">
                        <label>Примечание:</label>
                        <input type="text" id="in_note" class="form-control" placeholder="напр. ВхШхГ 12U 600x450">
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-success btn-block" onclick="addModel()">Добавить</button>
                    </div>
                </div>
            </div>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr class="active">
                        <th>Тип</th>
                        <th>Модель (артикул)</th>
                        <th>Высота (U)</th>
                        <th>Портов/Жил</th>
                        <th>Notes</th>
                        <th style="width: 40px"></th>
                    </tr>
                </thead>
                <tbody id="models_tbody"></tbody>
            </table>

            <div class="text-right">
                <hr>
                <button type="submit" class="btn btn-primary btn-lg">Сохранить всё в БД LibreNMS</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Загружаем существующие настройки. 
    // Если в БД NULL, инициализируем пустой массив.
    let currentData = [];
    const rawSettings = @json($settings ?? []);
    try {
        // В БД это лежит как {"model_presets": "[{...}]"}
        // Поэтому сначала проверяем наличие ключа, а потом парсим строку внутри него
        if (rawSettings && rawSettings.model_presets) {
            let data = rawSettings.model_presets;

            // Если это строка (JSON), парсим её
            if (typeof data === 'string') {
                currentData = JSON.parse(data);
            }
            // Если это уже массив (бывает при некоторых преобразованиях Laravel)
            else if (Array.isArray(data)) {
                currentData = data;
            }
        }
    } catch (e) {
        console.error("Ошибка парсинга model_presets:", e);
        currentData = [];
    }

    // Функция обновления таблицы и скрытого поля
    function renderTable() {
        const tbody = document.getElementById('models_tbody');
        tbody.innerHTML = '';

        currentData.forEach((item, index) => {
            const row = `<tr>
                <td>${item.type || '-'}</td>
                <td><strong>${item.name}</strong></td>
                <td>${item.unit}</td>
                <td>${item.ports}</td>
                <td>${item.note}</td>   
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-xs" onclick="deleteModel(${index})">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        // Сериализуем массив в JSON для отправки в LibreNMS
        document.getElementById('hidden_settings_field').value = JSON.stringify(currentData);
    }

    function addModel() {
        const nameInput = document.getElementById('in_name');
        const techInput = document.getElementById('in_type');
        const portsInput = document.getElementById('in_ports');
        const UnitsInput = document.getElementById('in_u');
        const NoteInput = document.getElementById('in_note');

        if (!nameInput.value.trim()) {
            alert('Укажите название модели!');
            return;
        }

        // Добавляем объект в массив
        currentData.push({
            name: nameInput.value.trim(),
            type: techInput.value.trim(),
            ports: portsInput.value,
            unit: UnitsInput.value,
            note: NoteInput.value.trim()
        });

        renderTable();

        // Очищаем поля ввода для следующей записи
        nameInput.value = '';
        techInput.value = '';
        UnitsInput.value = '';
        NoteInput.value = '';

    }

    function deleteModel(index) {
        currentData.splice(index, 1);
        renderTable();
    }

    // Запуск при загрузке страницы
    document.addEventListener('DOMContentLoaded', renderTable);
</script>
