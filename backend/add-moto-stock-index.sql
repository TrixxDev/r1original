-- Speed up moto tire catalog availability filter: tires.tire_id IN (SELECT tire_id FROM moto_stock ...)
-- Safe to run multiple times only if index does not exist yet.
CREATE INDEX idx_moto_stock_tire_id_quantity ON moto_stock (tire_id, quantity);
