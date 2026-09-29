-- Inventory Management System
-- DBMS Project
-- Author: Kainat Jameel
-- PostgreSQL Queries

-- 1. Show all products
SELECT *
FROM products
ORDER BY product_id;


-- 2. Show all products with their category
SELECT
    p.product_id,
    p.product_name,
    p.sku,
    c.category_name,
    p.quantity,
    p.reorder_level,
    p.unit_price
FROM products p
JOIN categories c
    ON p.category_id = c.category_id
ORDER BY p.product_id;


-- 3. Show low-stock products
SELECT
    product_id,
    product_name,
    sku,
    quantity,
    reorder_level
FROM products
WHERE quantity <= reorder_level
ORDER BY quantity ASC;


-- 4. Show all suppliers
SELECT *
FROM suppliers
ORDER BY supplier_id;


-- 5. Show purchase information
SELECT
    p.purchase_id,
    s.supplier_name,
    p.purchase_date,
    p.total_amount
FROM purchases p
JOIN suppliers s
    ON p.supplier_id = s.supplier_id
ORDER BY p.purchase_date DESC;


-- 6. Show sales information
SELECT
    sale_id,
    sale_date,
    total_amount
FROM sales
ORDER BY sale_date DESC;


-- 7. Calculate total stock value
SELECT
    SUM(quantity * unit_price) AS total_stock_value
FROM products;


-- 8. Count products in each category
SELECT
    c.category_name,
    COUNT(p.product_id) AS total_products
FROM categories c
LEFT JOIN products p
    ON c.category_id = p.category_id
GROUP BY c.category_id, c.category_name
ORDER BY c.category_name;


-- 9. Show purchase summary by supplier
SELECT
    s.supplier_name,
    COUNT(p.purchase_id) AS total_purchases,
    COALESCE(SUM(p.total_amount), 0) AS total_purchase_amount
FROM suppliers s
LEFT JOIN purchases p
    ON s.supplier_id = p.supplier_id
GROUP BY s.supplier_id, s.supplier_name
ORDER BY s.supplier_name;


-- 10. Show total sales amount
SELECT
    COALESCE(SUM(total_amount), 0) AS total_sales
FROM sales;
