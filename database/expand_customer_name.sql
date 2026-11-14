-- Allow complete HTML demo content in customer names on existing installations.
USE pet_portrait_prints;
ALTER TABLE customers MODIFY customer_name TEXT NOT NULL;
