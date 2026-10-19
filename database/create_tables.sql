-- ============================================================
-- DATABASE: pet_portrait_prints
-- MySQL / MariaDB compatible
-- ============================================================

CREATE DATABASE IF NOT EXISTS pet_portrait_prints
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE pet_portrait_prints;

-- ----------------------------------------------------------
-- TABLE: customers   (Parent Entity)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS customers (
    customer_id    INT          NOT NULL AUTO_INCREMENT,
    customer_name  VARCHAR(100) NOT NULL,
    email          VARCHAR(150) NOT NULL UNIQUE,
    phone          VARCHAR(20),
    created_at     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- TABLE: pet_portrait_orders  (Child Entity)
-- One Customer MAY have MANY PetPortraitOrders
-- Foreign key: customer_id  (ON DELETE CASCADE)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pet_portrait_orders (
    order_id       INT           NOT NULL AUTO_INCREMENT,
    customer_id    INT           NOT NULL,
    pet_name       VARCHAR(100)  NOT NULL,
    pet_type       VARCHAR(50)   NOT NULL,
    portrait_style VARCHAR(100)  NOT NULL,
    price          DECIMAL(10,2) NOT NULL,
    order_status   VARCHAR(30)   NOT NULL DEFAULT 'Pending',
    created_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (order_id),
    CONSTRAINT fk_order_customer
        FOREIGN KEY (customer_id)
        REFERENCES customers(customer_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
