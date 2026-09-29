-- Inventory Management System
-- DBMS Project
-- Author: Kainat Jameel
-- PostgreSQL Transaction Example

BEGIN;

-- Create a temporary category for transaction testing
INSERT INTO categories (category_name)
VALUES ('Transaction Test Category');

-- Create a temporary product
INSERT INTO products (
    category_id,
    product_name,
    sku,
    quantity,
    reorder_level,
    unit_price
)
SELECT
    category_id,
    'Transaction Test Product',
    'TEST-001',
    10,
    5,
    100.00
FROM categories
WHERE category_name = 'Transaction Test Category';

-- Increase product quantity
UPDATE products
SET quantity = quantity + 5
WHERE sku = 'TEST-001';

-- Check the updated quantity
SELECT
    product_id,
    product_name,
    quantity
FROM products
WHERE sku = 'TEST-001';

-- ROLLBACK cancels all changes made in this transaction
ROLLBACK;
