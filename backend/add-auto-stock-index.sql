-- Speed up auto tire catalog availability filter: tires.tire_id IN (SELECT tire_id FROM auto_stock ...)
-- Safe to run multiple times only if index does not exist yet.
CREATE INDEX idx_auto_stock_tire_id_quantity ON auto_stock (tire_id, quantity);
