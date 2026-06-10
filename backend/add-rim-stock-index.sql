-- Speed up rim catalog availability filter: rims.rim_id IN (SELECT rim_id FROM rim_stock ...)
-- Safe to run multiple times only if index does not exist yet.
CREATE INDEX idx_rim_stock_rim_id_quantity ON rim_stock (rim_id, quantity);
