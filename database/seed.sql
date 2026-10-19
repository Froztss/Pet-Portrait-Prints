-- Seed data for pet_portrait_prints
USE pet_portrait_prints;

-- Clear existing data (optional, for fresh seed)
DELETE FROM pet_portrait_orders;
DELETE FROM customers;
ALTER TABLE pet_portrait_orders AUTO_INCREMENT = 1;
ALTER TABLE customers AUTO_INCREMENT = 1;

-- Sample Customers
INSERT INTO customers (customer_name, email, phone) VALUES
('Sarah Johnson', 'sarah.johnson@email.com', '555-0123'),
('Michael Chen', 'michael.chen@email.com', '555-0456'),
('Emily Rodriguez', 'emily.rodriguez@email.com', '555-0789'),
('David Park', 'david.park@email.com', '555-1011'),
('Lisa Thompson', 'lisa.thompson@email.com', '555-1213');

-- Sample Orders
INSERT INTO pet_portrait_orders (customer_id, pet_name, pet_type, portrait_style, price, order_status) VALUES
(1, 'Buddy', 'Dog', 'Watercolor', 75.00, 'Completed'),
(1, 'Whiskers', 'Cat', 'Digital Sketch', 50.00, 'Pending'),
(2, 'Max', 'Dog', 'Oil Painting', 120.00, 'In Progress'),
(3, 'Luna', 'Cat', 'Charcoal', 65.00, 'Completed'),
(3, 'Rocky', 'Dog', 'Watercolor', 80.00, 'Shipped'),
(4, 'Bella', 'Rabbit', 'Digital Illustration', 55.00, 'Pending'),
(5, 'Oliver', 'Cat', 'Pastel', 70.00, 'Completed');