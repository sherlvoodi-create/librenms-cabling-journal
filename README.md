# LibreNMS Cabling Journal Plugin

This plugin extends the capabilities of **LibreNMS** by adding a full-fledged log of cable paths, optical couplings and patch panels linked to real ports of active equipment.

## Main features
- **Rack Explorer**: Visualization of racks and cabinets with display of occupied units.
- **Interactive Ports**: Displays the status of ports (Up/Down) in real time on the device diagram.
- **Optical log**: Accounting of fibers in couplings with support for color labeling (Red, Green, Blue, etc.).
- **Tracing**: Communication between the passive ports of the panels and the active ports of the switches.
- **Scalability**: Support for external assemblies (couplings) located at a distance from the main cabinet.

---

## Database structure

The plugin uses 5 custom tables in the LibreNMS database:

###1. `custom_racks' (Cabinets and niches)
Stores information about equipment locations. It is linked to the `locations' system table.
- `location_id': The site ID from LibreNMS.
- `units': The height of the cabinet in units (e.g. 42).

###2. `custom_rack_devices' (Active hardware)
Connects devices from LibreNMS (`devices`) to specific units in the cabinet.
- `device_id': The device ID from the `devices` table.
- `start_unit': The unit number in which the device is installed.

### 3. `custom_panels' (Passive nodes)
Describes patch panels, crosses, and couplings.
- `type`: Node type (`Patch-Panel', `Splice-Closure', `Phone-Box').
- `distance_from_rack': The distance from the cabinet (0 for panels in the rack, >0 for couplings in trays/wells).

###4. `custom_panel_ports' (Ports and fibers)
The internal structure of the panels and couplings.
- `fiber_color': Fiber color (supports HEX or HTML color names).
- `tube_color': The color of the module (tube).

###5. `custom_links' (Cable Magazine)
The connecting link of the whole system. Describes the physical connections.
- `cable_spec`: Marking of the cable (e.g. `LSZH 9/125 G.652.D').
- `cable_label': Physical tag on the cable.
- `side_a_type` / `side_b_type': Side type (`Panel_Port` or `Libre_Port').

---

## Installation

1. **Creating tables**:
Execute SQL queries from the 'install.sql` file in your LibreNMS database.

2. **File Placement**:
Clone the repository to the plugins folder:
``bash
cd /opt/librenms/html/plugins/
git clone https://github.com
------------------------------------------------------------------------------------------------------------------------
# LibreNMS Cabling Journal Plugin

Этот плагин расширяет возможности **LibreNMS**, добавляя полноценный журнал учета кабельных трасс, оптических муфт и патч-панелей с привязкой к реальным портам активного оборудования.

## Основные возможности
- **Rack Explorer**: Визуализация стоек и шкафов с отображением занятых юнитов.
- **Интерактивные порты**: Отображение статуса портов (Up/Down) в реальном времени на схеме устройства.
- **Оптический журнал**: Учет волокон в муфтах с поддержкой цветовой маркировки (Red, Green, Blue и т.д.).
- **Трассировка**: Связь между пассивными портами панелей и активными портами коммутаторов.
- **Масштабируемость**: Поддержка внешних узлов (муфт), находящихся на дистанции от основного шкафа.

---

## Структура базы данных

Плагин использует 5 кастомных таблиц в базе данных LibreNMS:

### 1. `custom_racks` (Шкафы и ниши)
Хранит информацию о местах размещения оборудования. Привязана к системной таблице `locations`.
- `location_id`: ID площадки из LibreNMS.
- `units`: Высота шкафа в юнитах (напр. 42).

### 2. `custom_rack_devices` (Активное оборудование)
Связывает устройства из LibreNMS (`devices`) с конкретными юнитами в шкафу.
- `device_id`: ID устройства из таблицы `devices`.
- `start_unit`: Номер юнита, в котором установлено устройство.

### 3. `custom_panels` (Пассивные узлы)
Описывает патч-панели, кроссы и муфты.
- `type`: Тип узла (`Patch-Panel`, `Splice-Closure`, `Phone-Box`).
- `distance_from_rack`: Дистанция от шкафа (0 для панелей в стойке, >0 для муфт в лотках/колодцах).

### 4. `custom_panel_ports` (Порты и волокна)
Внутренняя структура панелей и муфт.
- `fiber_color`: Цвет волокна (поддерживает HEX или названия цветов HTML).
- `tube_color`: Цвет модуля (трубки).

### 5. `custom_links` (Кабельный журнал)
Связующее звено всей системы. Описывает физические соединения.
- `cable_spec`: Маркировка кабеля (напр. `LSZH 9/125 G.652.D`).
- `cable_label`: Физическая бирка на кабеле.
- `side_a_type` / `side_b_type`: Тип стороны (`Panel_Port` или `Libre_Port`).

---

## Установка

1. **Создание таблиц**:
Выполните SQL-запросы из файла `install.sql` в вашей базе данных LibreNMS.

2. **Размещение файлов**:
Склонируйте репозиторий в папку плагинов:
```bash
cd /opt/librenms/html/plugins/
git clone https://github.com

```
3. **Активация**:
Включите плагин в базе данных:
```bash
INSERT INTO `plugins` (`plugin_name`, `plugin_enabled`, `plugin_order`) VALUES ('CablingJournal', 1, 10);

```
