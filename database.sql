CREATE TABLE product (
  id SERIAL PRIMARY KEY,
  uid varchar NOT NULL,
  name varchar NOT NULL,
  description text DEFAULT NULL,
  amount int NOT NULL
);

CREATE TABLE carrier (
  id SERIAL PRIMARY KEY,
  uid varchar NOT NULL,
  name varchar NOT NULL
);

CREATE TABLE workshop (
  id SERIAL PRIMARY KEY,
  name varchar NOT NULL
);

CREATE TABLE transport_log (
  id SERIAL PRIMARY KEY,
  product_id int NOT NULL,
  carrier_id int NOT NULL,
  workshop_id int NOT NULL,
  scanned_at timestamp DEFAULT CURRENT_TIMESTAMP,
  
);
