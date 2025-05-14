-- Очистка существующих данных
TRUNCATE TABLE movement_events CASCADE;
TRUNCATE TABLE planned_routes CASCADE;
TRUNCATE TABLE material_receipts CASCADE;
TRUNCATE TABLE documents CASCADE;
TRUNCATE TABLE material_statuses CASCADE;
TRUNCATE TABLE materials CASCADE;
TRUNCATE TABLE rfid_tags CASCADE;
TRUNCATE TABLE route_points CASCADE;
TRUNCATE TABLE users CASCADE;

-- Создание пользователей
INSERT INTO users (id, name, role, password_hash) VALUES
(1, 'Иван Петров', 'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, 'Анна Сидорова', 'supervisor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'Павел Николаев', 'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Создание точек маршрута
INSERT INTO route_points (id, name, type) VALUES
(1, 'Склад сырья', 'warehouse'),
(2, 'Участок подготовки', 'preparation'),
(3, 'Линия сборки А1', 'assembly'),
(4, 'Линия сборки А2', 'assembly'),
(5, 'Контроль качества', 'quality'),
(6, 'Склад готовой продукции', 'warehouse');

-- Создание материалов и RFID-меток
INSERT INTO materials (id, name, amount, type) VALUES
(1, 'Корпус А-101', 50, 'raw_material'),
(2, 'Плата электронная B-201', 100, 'component'),
(3, 'Блок питания C-301', 75, 'component'),
(4, 'Готовое изделие X-1', 25, 'finished_product');

-- Создание RFID-меток
INSERT INTO rfid_tags (id, material_id, tag_uid, is_active, assigned_at) VALUES
(1, 1, 'RFID-A101-001', true, NOW()),
(2, 2, 'RFID-B201-001', true, NOW()),
(3, 3, 'RFID-C301-001', true, NOW()),
(4, 4, 'RFID-X001-001', true, NOW());

-- Обновление материалов с RFID-метками
UPDATE materials SET rfid_tag_id = 1 WHERE id = 1;
UPDATE materials SET rfid_tag_id = 2 WHERE id = 2;
UPDATE materials SET rfid_tag_id = 3 WHERE id = 3;
UPDATE materials SET rfid_tag_id = 4 WHERE id = 4;

-- Создание статусов материалов
INSERT INTO material_statuses (material_id, current_point_id, status, updated_at) VALUES
(1, 1, 'in_stock', NOW()),
(2, 2, 'in_progress', NOW()),
(3, 3, 'in_progress', NOW()),
(4, 5, 'quality_check', NOW());

-- Создание плановых маршрутов
INSERT INTO planned_routes (id, material_id, route_point_id, sequence, expected_at) VALUES
-- Маршрут для Корпуса А-101
(1, 1, 1, 1, NOW()),
(2, 1, 2, 2, NOW() + INTERVAL '2 hours'),
(3, 1, 3, 3, NOW() + INTERVAL '4 hours'),
(4, 1, 5, 4, NOW() + INTERVAL '6 hours'),
-- Маршрут для Платы B-201
(5, 2, 2, 1, NOW()),
(6, 2, 3, 2, NOW() + INTERVAL '3 hours'),
(7, 2, 5, 3, NOW() + INTERVAL '5 hours'),
-- Маршрут для Блока питания C-301
(8, 3, 3, 1, NOW()),
(9, 3, 4, 2, NOW() + INTERVAL '2 hours'),
(10, 3, 5, 3, NOW() + INTERVAL '4 hours');

-- Создание событий перемещения
INSERT INTO movement_events (id, material_id, route_point_id, scanned_by, scanned_at, is_deviation, note) VALUES
(1, 1, 1, 1, NOW() - INTERVAL '1 hour', false, 'Принято на склад'),
(2, 2, 2, 1, NOW() - INTERVAL '2 hours', false, 'Начало подготовки'),
(3, 3, 3, 2, NOW() - INTERVAL '3 hours', false, 'Поступление на сборку'),
(4, 4, 5, 3, NOW() - INTERVAL '1 hour', false, 'Передано на проверку качества');

-- Создание документов
INSERT INTO documents (id, material_id, type, file_path, created_at) VALUES
(1, 1, 'receipt', '/documents/receipts/A101-001.pdf', NOW()),
(2, 2, 'quality_cert', '/documents/certs/B201-001.pdf', NOW()),
(3, 3, 'technical_spec', '/documents/specs/C301-001.pdf', NOW()),
(4, 4, 'inspection_report', '/documents/reports/X1-001.pdf', NOW());

-- Создание записей о приёмке материалов
INSERT INTO material_receipts (id, material_id, received_by, supplier_name, received_at) VALUES
(1, 1, 1, 'ООО "ТехноПоставка"', NOW() - INTERVAL '1 day'),
(2, 2, 2, 'АО "ЭлектроКомплект"', NOW() - INTERVAL '2 days'),
(3, 3, 1, 'ООО "ЭнергоСистемы"', NOW() - INTERVAL '3 days'),
(4, 4, 3, 'ЗАО "ПромКомплект"', NOW() - INTERVAL '1 day'); 