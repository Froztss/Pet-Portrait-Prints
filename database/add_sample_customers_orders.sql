-- Add five sample customers and one order for each without clearing existing data.
USE pet_portrait_prints;

INSERT INTO customers (customer_name, email, phone) VALUES
    ('Avery Morgan', 'avery.morgan@example.test', '555-0201'),
    ('Noah Patel', 'noah.patel@example.test', '555-0202'),
    ('Grace Kim', 'grace.kim@example.test', '555-0203'),
    ('Mateo Rivera', 'mateo.rivera@example.test', '555-0204'),
    ('Chloe Bennett', 'chloe.bennett@example.test', '555-0205')
ON DUPLICATE KEY UPDATE
    customer_name = VALUES(customer_name),
    phone = VALUES(phone);

INSERT INTO pet_portrait_orders
    (customer_id, pet_name, pet_type, portrait_style, price, order_status)
SELECT
    c.customer_id,
    sample_orders.pet_name,
    sample_orders.pet_type,
    sample_orders.portrait_style,
    sample_orders.price,
    sample_orders.order_status
FROM (
    SELECT 'avery.morgan@example.test' AS customer_email, 'Mochi' AS pet_name,
        'Dog' AS pet_type, 'Watercolor' AS portrait_style, 78.00 AS price, 'Pending' AS order_status
    UNION ALL
    SELECT 'noah.patel@example.test', 'Pepper', 'Cat', 'Digital Sketch', 62.00, 'In Progress'
    UNION ALL
    SELECT 'grace.kim@example.test', 'Clover', 'Rabbit', 'Pastel', 91.00, 'Completed'
    UNION ALL
    SELECT 'mateo.rivera@example.test', 'Sunny', 'Bird', 'Pop Art', 54.00, 'Pending'
    UNION ALL
    SELECT 'chloe.bennett@example.test', 'Atlas', 'Dog', 'Oil Painting', 125.00, 'Shipped'
) AS sample_orders
JOIN customers c ON c.email = sample_orders.customer_email
WHERE NOT EXISTS (
    SELECT 1
    FROM pet_portrait_orders existing
    WHERE existing.customer_id = c.customer_id
      AND existing.pet_name = sample_orders.pet_name
);
