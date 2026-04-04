<?php
namespace App\Plugins\CablingJournal;

use App\Plugins\Hooks\PageHook;
use Illuminate\Contracts\Auth\Authenticatable;

class Page extends PageHook
{
    // Путь к шаблону относительно resources/views
    public string $view = 'plugins.CablingJournal.resources.views.page';
    private string $dbPath;

    public function __construct()
    {
        // Путь к файлу базы данных внутри плагина
        $this->dbPath = __DIR__ . '/data/database.php';
    }
    
    /**
     * Разрешаем доступ всем авторизованным пользователям
     */
    public function authorize(Authenticatable $user): bool
    {
        return true;
    }

    /**
     * Основная логика: обработка ввода и подготовка данных для таблицы
     */
    public function data(): array
    {
       $request = request();

        // 1. Загружаем данные из файла (или создаем пустой массив)
        $data = file_exists($this->dbPath) ? include $this->dbPath : ['racks' => []];

        // 2. Обработка сохранения (POST)
        if ($request->isMethod('post')) {
            $newRack = [
                'id'          => time(), // Простой ID на основе времени
                'name'        => $request->input('name'),
                'location'    => $request->input('location'),
                'units'       => $request->input('units', 42),
                'description' => $request->input('description'),
            ];

            $data['racks'][] = $newRack;

            // Сохраняем обратно в файл через var_export
            $code = "<?php\nreturn " . var_export($data, true) . ";";
            file_put_contents($this->dbPath, $code);

            session()->flash('status', 'Запись сохранена в файл!');
        }

        return [
            'racks' => $data['racks'],
            'title' => 'Кабельный журнал (File DB)'
        ];
    }
}
