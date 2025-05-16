-- Cleanup existing data
TRUNCATE TABLE movement_events CASCADE;
TRUNCATE TABLE planned_routes CASCADE;
TRUNCATE TABLE material_receipts CASCADE;
TRUNCATE TABLE documents CASCADE;
TRUNCATE TABLE material_statuses CASCADE;
TRUNCATE TABLE materials CASCADE;
TRUNCATE TABLE rfid_tags CASCADE;
TRUNCATE TABLE route_points CASCADE;
TRUNCATE TABLE users CASCADE;

-- Reset sequences
ALTER SEQUENCE materials_id_seq RESTART WITH 1;
ALTER SEQUENCE rfid_tags_id_seq RESTART WITH 1;
ALTER SEQUENCE movement_events_id_seq RESTART WITH 1;
ALTER SEQUENCE planned_routes_id_seq RESTART WITH 1;
ALTER SEQUENCE documents_id_seq RESTART WITH 1;
ALTER SEQUENCE material_receipts_id_seq RESTART WITH 1;
ALTER SEQUENCE users_id_seq RESTART WITH 1;
ALTER SEQUENCE route_points_id_seq RESTART WITH 1;

-- Create users
INSERT INTO users (name, role, password_hash) VALUES
('John Smith', 'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Anna Johnson', 'supervisor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Paul Wilson', 'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Create route points
INSERT INTO route_points (name, type) VALUES
('Raw Materials Warehouse', 'warehouse'),
('Preparation Area', 'preparation'),
('Assembly Line A1', 'assembly'),
('Assembly Line A2', 'assembly'),
('Quality Control', 'quality'),
('Finished Goods Warehouse', 'warehouse');

-- Create level 1 materials (Main assemblies)
INSERT INTO materials (name, amount, type, part_number, parent_id, rfid_tag_id) VALUES
('Main Assembly X', 1, 'assembly', '0000.0000.0000.0000.0001', NULL, NULL),
('Main Assembly Y', 1, 'assembly', '0000.0000.0000.0000.0002', NULL, NULL),
('Main Assembly Z', 1, 'assembly', '0000.0000.0000.0000.0003', NULL, NULL);

-- Create RFID tags for level 1
INSERT INTO rfid_tags (material_id, tag_uid, is_active, assigned_at)
SELECT id, CONCAT('TAG', LPAD(id::text, 6, '0')), true, NOW() 
FROM materials 
WHERE parent_id IS NULL;

-- Update RFID references for level 1
UPDATE materials m 
SET rfid_tag_id = r.id 
FROM rfid_tags r 
WHERE m.id = r.material_id AND m.parent_id IS NULL;

-- Create level 2 materials
INSERT INTO materials (name, amount, type, parent_id, part_number, rfid_tag_id) VALUES
-- Sub-materials for Assembly X
('Pallet X1', 1, 'pallet', 1, '0000.0000.0000.0001.0001', NULL),
('Package X2', 1, 'package', 1, '0000.0000.0000.0001.0002', NULL),
('Blank X3', 1, 'blank', 1, '0000.0000.0000.0001.0003', NULL),
-- Sub-materials for Assembly Y
('Pallet Y1', 1, 'pallet', 2, '0000.0000.0000.0002.0001', NULL),
('Package Y2', 1, 'package', 2, '0000.0000.0000.0002.0002', NULL),
('Blank Y3', 1, 'blank', 2, '0000.0000.0000.0002.0003', NULL),
-- Sub-materials for Assembly Z
('Pallet Z1', 1, 'pallet', 3, '0000.0000.0000.0003.0001', NULL),
('Package Z2', 1, 'package', 3, '0000.0000.0000.0003.0002', NULL),
('Blank Z3', 1, 'blank', 3, '0000.0000.0000.0003.0003', NULL);

-- Create RFID tags for level 2
INSERT INTO rfid_tags (material_id, tag_uid, is_active, assigned_at)
SELECT id, CONCAT('TAG', LPAD(id::text, 6, '0')), true, NOW() 
FROM materials 
WHERE parent_id IN (1, 2, 3);

-- Update RFID references for level 2
UPDATE materials m 
SET rfid_tag_id = r.id 
FROM rfid_tags r 
WHERE m.id = r.material_id AND m.parent_id IN (1, 2, 3);

-- Create level 3 materials
INSERT INTO materials (name, amount, type, parent_id, part_number, rfid_tag_id) VALUES
-- Components for Pallet X1
('Part X1-1', 1, 'part', 4, '0000.0000.0001.0001.0001', NULL),
('Consumable X1-2', 1, 'consumable', 4, '0000.0000.0001.0001.0002', NULL),
-- Components for Package X2
('Part X2-1', 1, 'part', 5, '0000.0000.0001.0002.0001', NULL),
('Consumable X2-2', 1, 'consumable', 5, '0000.0000.0001.0002.0002', NULL),
-- Components for Blank X3
('Part X3-1', 1, 'part', 6, '0000.0000.0001.0003.0001', NULL),
('Consumable X3-2', 1, 'consumable', 6, '0000.0000.0001.0003.0002', NULL),
-- Components for Pallet Y1
('Part Y1-1', 1, 'part', 7, '0000.0000.0002.0001.0001', NULL),
('Consumable Y1-2', 1, 'consumable', 7, '0000.0000.0002.0001.0002', NULL),
-- Components for Package Y2
('Part Y2-1', 1, 'part', 8, '0000.0000.0002.0002.0001', NULL),
('Consumable Y2-2', 1, 'consumable', 8, '0000.0000.0002.0002.0002', NULL),
-- Components for Blank Y3
('Part Y3-1', 1, 'part', 9, '0000.0000.0002.0003.0001', NULL),
('Consumable Y3-2', 1, 'consumable', 9, '0000.0000.0002.0003.0002', NULL),
-- Components for Pallet Z1
('Part Z1-1', 1, 'part', 10, '0000.0000.0003.0001.0001', NULL),
('Consumable Z1-2', 1, 'consumable', 10, '0000.0000.0003.0001.0002', NULL),
-- Components for Package Z2
('Part Z2-1', 1, 'part', 11, '0000.0000.0003.0002.0001', NULL),
('Consumable Z2-2', 1, 'consumable', 11, '0000.0000.0003.0002.0002', NULL),
-- Components for Blank Z3
('Part Z3-1', 1, 'part', 12, '0000.0000.0003.0003.0001', NULL),
('Consumable Z3-2', 1, 'consumable', 12, '0000.0000.0003.0003.0002', NULL);

-- Create RFID tags for level 3
INSERT INTO rfid_tags (material_id, tag_uid, is_active, assigned_at)
SELECT id, CONCAT('TAG', LPAD(id::text, 6, '0')), true, NOW() 
FROM materials 
WHERE parent_id BETWEEN 4 AND 12;

-- Update RFID references for level 3
UPDATE materials m 
SET rfid_tag_id = r.id 
FROM rfid_tags r 
WHERE m.id = r.material_id AND m.parent_id BETWEEN 4 AND 12;

-- Create level 4 materials
INSERT INTO materials (name, amount, type, parent_id, part_number, rfid_tag_id) VALUES
-- Parts for Part X1-1
('Assembly X1-1-1', 1, 'assembly', 13, '0000.0001.0001.0001.0001', NULL),
('Package X1-1-2', 1, 'package', 13, '0000.0001.0001.0001.0002', NULL),
('Blank X1-1-3', 1, 'blank', 13, '0000.0001.0001.0001.0003', NULL),
-- Parts for Consumable X1-2
('Assembly X1-2-1', 1, 'assembly', 14, '0000.0001.0001.0002.0001', NULL),
('Package X1-2-2', 1, 'package', 14, '0000.0001.0001.0002.0002', NULL),
('Blank X1-2-3', 1, 'blank', 14, '0000.0001.0001.0002.0003', NULL),
-- Parts for Part X2-1
('Assembly X2-1-1', 1, 'assembly', 15, '0000.0001.0002.0001.0001', NULL),
('Package X2-1-2', 1, 'package', 15, '0000.0001.0002.0001.0002', NULL),
('Blank X2-1-3', 1, 'blank', 15, '0000.0001.0002.0001.0003', NULL),
-- Parts for Consumable X2-2
('Assembly X2-2-1', 1, 'assembly', 16, '0000.0001.0002.0002.0001', NULL),
('Package X2-2-2', 1, 'package', 16, '0000.0001.0002.0002.0002', NULL),
('Blank X2-2-3', 1, 'blank', 16, '0000.0001.0002.0002.0003', NULL),
-- Parts for Part X3-1
('Assembly X3-1-1', 1, 'assembly', 17, '0000.0001.0003.0001.0001', NULL),
('Package X3-1-2', 1, 'package', 17, '0000.0001.0003.0001.0002', NULL),
('Blank X3-1-3', 1, 'blank', 17, '0000.0001.0003.0001.0003', NULL),
-- Parts for Consumable X3-2
('Assembly X3-2-1', 1, 'assembly', 18, '0000.0001.0003.0002.0001', NULL),
('Package X3-2-2', 1, 'package', 18, '0000.0001.0003.0002.0002', NULL),
('Blank X3-2-3', 1, 'blank', 18, '0000.0001.0003.0002.0003', NULL),
-- Parts for Part Y1-1
('Assembly Y1-1-1', 1, 'assembly', 19, '0000.0002.0001.0001.0001', NULL),
('Package Y1-1-2', 1, 'package', 19, '0000.0002.0001.0001.0002', NULL),
('Blank Y1-1-3', 1, 'blank', 19, '0000.0002.0001.0001.0003', NULL),
-- Parts for Consumable Y1-2
('Assembly Y1-2-1', 1, 'assembly', 20, '0000.0002.0001.0002.0001', NULL),
('Package Y1-2-2', 1, 'package', 20, '0000.0002.0001.0002.0002', NULL),
('Blank Y1-2-3', 1, 'blank', 20, '0000.0002.0001.0002.0003', NULL),
-- Parts for Part Y2-1
('Assembly Y2-1-1', 1, 'assembly', 21, '0000.0002.0002.0001.0001', NULL),
('Package Y2-1-2', 1, 'package', 21, '0000.0002.0002.0001.0002', NULL),
('Blank Y2-1-3', 1, 'blank', 21, '0000.0002.0002.0001.0003', NULL),
-- Parts for Consumable Y2-2
('Assembly Y2-2-1', 1, 'assembly', 22, '0000.0002.0002.0002.0001', NULL),
('Package Y2-2-2', 1, 'package', 22, '0000.0002.0002.0002.0002', NULL),
('Blank Y2-2-3', 1, 'blank', 22, '0000.0002.0002.0002.0003', NULL),
-- Parts for Part Y3-1
('Assembly Y3-1-1', 1, 'assembly', 23, '0000.0002.0003.0001.0001', NULL),
('Package Y3-1-2', 1, 'package', 23, '0000.0002.0003.0001.0002', NULL),
('Blank Y3-1-3', 1, 'blank', 23, '0000.0002.0003.0001.0003', NULL),
-- Parts for Consumable Y3-2
('Assembly Y3-2-1', 1, 'assembly', 24, '0000.0002.0003.0002.0001', NULL),
('Package Y3-2-2', 1, 'package', 24, '0000.0002.0003.0002.0002', NULL),
('Blank Y3-2-3', 1, 'blank', 24, '0000.0002.0003.0002.0003', NULL),
-- Parts for Part Z1-1
('Assembly Z1-1-1', 1, 'assembly', 25, '0000.0003.0001.0001.0001', NULL),
('Package Z1-1-2', 1, 'package', 25, '0000.0003.0001.0001.0002', NULL),
('Blank Z1-1-3', 1, 'blank', 25, '0000.0003.0001.0001.0003', NULL),
-- Parts for Consumable Z1-2
('Assembly Z1-2-1', 1, 'assembly', 26, '0000.0003.0001.0002.0001', NULL),
('Package Z1-2-2', 1, 'package', 26, '0000.0003.0001.0002.0002', NULL),
('Blank Z1-2-3', 1, 'blank', 26, '0000.0003.0001.0002.0003', NULL),
-- Parts for Part Z2-1
('Assembly Z2-1-1', 1, 'assembly', 27, '0000.0003.0002.0001.0001', NULL),
('Package Z2-1-2', 1, 'package', 27, '0000.0003.0002.0001.0002', NULL),
('Blank Z2-1-3', 1, 'blank', 27, '0000.0003.0002.0001.0003', NULL),
-- Parts for Consumable Z2-2
('Assembly Z2-2-1', 1, 'assembly', 28, '0000.0003.0002.0002.0001', NULL),
('Package Z2-2-2', 1, 'package', 28, '0000.0003.0002.0002.0002', NULL),
('Blank Z2-2-3', 1, 'blank', 28, '0000.0003.0002.0002.0003', NULL),
-- Parts for Part Z3-1
('Assembly Z3-1-1', 1, 'assembly', 29, '0000.0003.0003.0001.0001', NULL),
('Package Z3-1-2', 1, 'package', 29, '0000.0003.0003.0001.0002', NULL),
('Blank Z3-1-3', 1, 'blank', 29, '0000.0003.0003.0001.0003', NULL),
-- Parts for Consumable Z3-2
('Assembly Z3-2-1', 1, 'assembly', 30, '0000.0003.0003.0002.0001', NULL),
('Package Z3-2-2', 1, 'package', 30, '0000.0003.0003.0002.0002', NULL),
('Blank Z3-2-3', 1, 'blank', 30, '0000.0003.0003.0002.0003', NULL);

-- Create RFID tags for level 4
INSERT INTO rfid_tags (material_id, tag_uid, is_active, assigned_at)
SELECT id, CONCAT('TAG', LPAD(id::text, 6, '0')), true, NOW() 
FROM materials 
WHERE parent_id >= 13;

-- Update RFID references for level 4
UPDATE materials m 
SET rfid_tag_id = r.id 
FROM rfid_tags r 
WHERE m.id = r.material_id AND m.parent_id >= 13;

-- Create material statuses
INSERT INTO material_statuses (material_id, current_point_id, status, updated_at)
SELECT id, 1, 'created', NOW() FROM materials;

-- Create planned routes
INSERT INTO planned_routes (material_id, route_point_id, sequence, expected_at) VALUES
-- Route for Assembly X
(1, 1, 1, NOW()),
(1, 2, 2, NOW() + INTERVAL '2 hours'),
(1, 3, 3, NOW() + INTERVAL '4 hours'),
(1, 5, 4, NOW() + INTERVAL '6 hours'),
-- Route for Assembly Y
(2, 2, 1, NOW()),
(2, 3, 2, NOW() + INTERVAL '3 hours'),
(2, 5, 3, NOW() + INTERVAL '5 hours'),
-- Route for Assembly Z
(3, 3, 1, NOW()),
(3, 4, 2, NOW() + INTERVAL '2 hours'),
(3, 5, 3, NOW() + INTERVAL '4 hours');

-- Create movement events
INSERT INTO movement_events (material_id, route_point_id, scanned_by, scanned_at, is_deviation, note) VALUES
(1, 1, 1, NOW() - INTERVAL '1 hour', false, 'Received at warehouse'),
(2, 2, 1, NOW() - INTERVAL '2 hours', false, 'Started preparation'),
(3, 3, 2, NOW() - INTERVAL '3 hours', false, 'Assembly started'),
(4, 5, 3, NOW() - INTERVAL '1 hour', false, 'Quality check initiated');

-- Create documents
INSERT INTO documents (id, material_id, type, file_path, created_at) VALUES
(1, 1, 'receipt', '/documents/receipts/X-001.pdf', NOW()),
(2, 2, 'quality_cert', '/documents/certs/Y-001.pdf', NOW()),
(3, 3, 'technical_spec', '/documents/specs/Z-001.pdf', NOW()),
(4, 4, 'inspection_report', '/documents/reports/X1-001.pdf', NOW());

-- Create material receipts
INSERT INTO material_receipts (id, material_id, received_by, supplier_name, received_at) VALUES
(1, 1, 1, 'TechSupply LLC', NOW() - INTERVAL '1 day'),
(2, 2, 2, 'ElectroComponents Inc', NOW() - INTERVAL '2 days'),
(3, 3, 1, 'PowerSystems Corp', NOW() - INTERVAL '3 days'),
(4, 4, 3, 'IndustrialParts Ltd', NOW() - INTERVAL '1 day'); 