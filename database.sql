CREATE TABLE rfid_tags (
    id SERIAL PRIMARY KEY,
    material_id INT NOT NULL,
    tag_uid VARCHAR NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    assigned_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE route_points (
    id SERIAL PRIMARY KEY,
    name VARCHAR NOT NULL,
    type VARCHAR NOT NULL
);

CREATE TABLE materials (
    id SERIAL PRIMARY KEY,
    rfid_tag_id INT NOT NULL,
    name VARCHAR NOT NULL,
    amount INT NOT NULL,
    type VARCHAR NOT NULL
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR NOT NULL,
    role VARCHAR NOT NULL,
    password_hash VARCHAR NOT NULL
);

CREATE TABLE material_statuses (
    material_id SERIAL PRIMARY KEY,
    current_point_id INT NOT NULL,
    status VARCHAR NOT NULL,
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE documents (
    id SERIAL PRIMARY KEY,
    material_id INT NOT NULL,
    type VARCHAR NOT NULL,
    file_path VARCHAR NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE material_receipts (
    id SERIAL PRIMARY KEY,
    material_id INT NOT NULL,
    received_by INT NOT NULL,
    supplier_name VARCHAR NOT null,
    received_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE planned_routes (
    id SERIAL PRIMARY KEY,
    material_id INT NOT NULL,
    route_point_id INT NOT NULL,
    sequence FLOAT NOT NULL,
    expected_at TIMESTAMP
);

CREATE TABLE movement_events (
    id SERIAL PRIMARY KEY,
    material_id INT NOT NULL,
    route_point_id INT NOT NULL,
    scanned_by INT NOT NULL,
    scanned_at TIMESTAMP DEFAULT NOW(),
    is_deviation BOOLEAN NOT NULL,
    note VARCHAR NOT NULL
);

ALTER TABLE rfid_tags
ADD CONSTRAINT fk_rfid_tags_materials
FOREIGN KEY (material_id) REFERENCES materials(id);

ALTER TABLE materials
ADD CONSTRAINT fk_materials_rfid_tags
FOREIGN KEY (rfid_tag_id) REFERENCES rfid_tags(id);

ALTER TABLE material_statuses
ADD CONSTRAINT fk_material_status_route_points
FOREIGN KEY (current_point_id) REFERENCES route_points(id);

ALTER TABLE documents
ADD CONSTRAINT fk_documents_materials
FOREIGN KEY (material_id) REFERENCES materials(id);

ALTER TABLE material_receipts
ADD CONSTRAINT fk_material_receipts_materials
FOREIGN KEY (material_id) REFERENCES materials(id);

ALTER TABLE material_receipts
ADD CONSTRAINT fk_material_receipts_users
FOREIGN KEY (received_by) REFERENCES users(id);

ALTER TABLE planned_routes
ADD CONSTRAINT fk_planned_routes_materials
FOREIGN KEY (material_id) REFERENCES materials(id);

ALTER TABLE planned_routes
ADD CONSTRAINT fk_planned_routes_route_points
FOREIGN KEY (route_point_id) REFERENCES route_points(id);

ALTER TABLE movement_events
ADD CONSTRAINT fk_movement_events_route_points
FOREIGN KEY (route_point_id) REFERENCES route_points(id);

ALTER TABLE movement_events
ADD CONSTRAINT fk_movement_events_materials
FOREIGN KEY (material_id) REFERENCES materials(id);

ALTER TABLE movement_events
ADD CONSTRAINT fk_movement_events_users
FOREIGN KEY (scanned_by) REFERENCES users(id);

-- ------------------------------ --
-- CREATE TABLE product (
--   id SERIAL PRIMARY KEY,
--   uid varchar NOT NULL,
--   name varchar NOT NULL,
--   description text DEFAULT NULL,
--   amount int NOT NULL
-- );

-- CREATE TABLE carrier (
--   id SERIAL PRIMARY KEY,
--   uid varchar NOT NULL,
--   name varchar NOT NULL
-- );

-- CREATE TABLE workshop (
--   id SERIAL PRIMARY KEY,
--   name varchar NOT NULL
-- );

-- CREATE TABLE transport_log (
--   id SERIAL PRIMARY KEY,
--   product_id int NOT NULL,
--   carrier_id int NOT NULL,
--   workshop_id int NOT NULL,
--   scanned_at timestamp DEFAULT CURRENT_TIMESTAMP,
  
-- );
-- ------------------------------ --
