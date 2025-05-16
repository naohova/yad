-- Отключаем проверку внешних ключей на время выполнения скрипта
SET session_replication_role = 'replica';

-- Очистка таблиц
TRUNCATE TABLE movement_events CASCADE;
TRUNCATE TABLE material_statuses CASCADE;
TRUNCATE TABLE planned_routes CASCADE;
TRUNCATE TABLE materials CASCADE;
TRUNCATE TABLE route_points CASCADE;
TRUNCATE TABLE rfid_tags CASCADE;
TRUNCATE TABLE documents CASCADE;
TRUNCATE TABLE users CASCADE;

-- Сброс автоинкрементных последовательностей
ALTER SEQUENCE materials_id_seq RESTART WITH 1;
ALTER SEQUENCE route_points_id_seq RESTART WITH 1;
ALTER SEQUENCE rfid_tags_id_seq RESTART WITH 1;
ALTER SEQUENCE documents_id_seq RESTART WITH 1;
ALTER SEQUENCE users_id_seq RESTART WITH 1;
ALTER SEQUENCE movement_events_id_seq RESTART WITH 1;

-- Создание тестовых пользователей
INSERT INTO users (name, password_hash, role, created_at) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW()),
('operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operator', NOW()),
('viewer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'viewer', NOW());

-- Создание точек маршрута
INSERT INTO route_points (name, type) VALUES
('Склад сырья', 'warehouse'),
('Участок подготовки', 'preparation'),
('Линия сборки №1', 'assembly'),
('Линия сборки №2', 'assembly'),
('Контроль качества', 'quality'),
('Склад готовой продукции', 'warehouse');

-- Создание материалов
INSERT INTO materials (name, amount, type, part_number, created_at, updated_at) VALUES
('Корпус редуктора A1', 200, 'raw_material', 'CORP-001', NOW(), NOW()),
('Плата электронная B-201', 100, 'component', 'PCB-201', NOW(), NOW()),
('Блок питания C-301', 75, 'component', 'PSU-301', NOW(), NOW()),
('Готовое изделие X-1', 25, 'finished_product', 'PROD-X1', NOW(), NOW());

-- Создание RFID меток
INSERT INTO rfid_tags (tag_uid, material_id, is_active, assigned_at) VALUES
('RFID-001', 1, true, NOW()),
('RFID-002', 2, true, NOW()),
('RFID-003', 3, true, NOW()),
('RFID-004', 4, true, NOW());

-- Создание статусов материалов
INSERT INTO material_statuses (material_id, current_point_id, status, updated_at) VALUES
(1, 1, 'created', NOW()),
(2, 2, 'in_progress', NOW()),
(3, 3, 'in_progress', NOW()),
(4, 5, 'quality_check', NOW());

-- Создание плановых маршрутов
INSERT INTO planned_routes (material_id, route_point_id, sequence, expected_at) VALUES
(1, 1, 1, NOW() + interval '1 day'),
(1, 2, 2, NOW() + interval '2 days'),
(1, 3, 3, NOW() + interval '3 days'),
(2, 2, 1, NOW() + interval '1 day'),
(2, 3, 2, NOW() + interval '2 days'),
(3, 3, 1, NOW() + interval '1 day'),
(3, 5, 2, NOW() + interval '2 days'),
(4, 5, 1, NOW() + interval '1 day'),
(4, 6, 2, NOW() + interval '2 days');

-- Создание событий перемещения
INSERT INTO movement_events (material_id, route_point_id, scanned_by, scanned_at, is_deviation, note) VALUES
(1, 1, 1, NOW() - interval '2 hours', false, 'Принят на склад'),
(2, 2, 1, NOW() - interval '1 hour', false, 'Передан в производство'),
(3, 3, 2, NOW() - interval '30 minutes', false, 'Начата сборка'),
(4, 5, 2, NOW(), false, 'На проверке качества');

-- Включаем обратно проверку внешних ключей
SET session_replication_role = 'origin'; 