-- Rename the existing customer and order audit columns without losing data.
USE pet_portrait_prints;

ALTER TABLE customers
    CHANGE COLUMN created_at date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHANGE COLUMN updated_at last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CHANGE COLUMN updated_by last_updated_by INT;

ALTER TABLE pet_portrait_orders
    CHANGE COLUMN created_at date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHANGE COLUMN updated_at last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CHANGE COLUMN updated_by last_updated_by INT;
