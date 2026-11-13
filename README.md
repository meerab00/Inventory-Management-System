# Inventory Management System

**Course:** DBMS  
**Author:** Kainat Jameel  
**Database:** PostgreSQL

## Project Description

A web-based inventory system for managing products, categories, suppliers, purchases, sales, and stock levels.

## Current Working Features

- Product management: add, view, edit, and delete products
- Supplier management: add, view, edit, and delete suppliers
- Purchase entry: record supplier purchases and automatically increase product stock
- Sales entry: record sales and automatically decrease stock
- Stock validation: a sale is rejected when available stock is insufficient
- Stock adjustment history for purchases and sales
- Dashboard summary: product count, supplier count, low-stock count, and inventory value
- Low-stock report
- Responsive layout for desktop and mobile

## Technology Stack

- Frontend: HTML, CSS, JavaScript
- Backend: PHP 8.2
- Database: PostgreSQL
- Database administration: pgAdmin 4
- Web server: Apache (Docker)

## Database Setup

1. Create a PostgreSQL database named `inventory_management`.
2. Open `database/schema.sql` in pgAdmin Query Tool and execute it.
3. Configure the application using environment variables:

| Variable | Example / purpose |
|---|---|
| `DB_HOST` | PostgreSQL server host |
| `DB_PORT` | `5432` |
| `DB_NAME` | `inventory_management` |
| `DB_USER` | Database username |
| `DB_PASSWORD` | Database password |
| `DB_SSLMODE` | Usually `require` for hosted databases |

For local development, you may create an untracked `config/database.local.php` file that returns an array with the keys `host`, `port`, `dbname`, `username`, and `password`. This file is ignored by Git and must never be committed.

## Run Locally

1. Install PHP 8.2 with the PostgreSQL extension and PostgreSQL.
2. Create the database and execute `database/schema.sql`.
3. Set the database environment variables, or create the local config file described above.
4. Point Apache's document root to the repository folder, or run PHP's development server from the repository root.
5. Open the local site in your browser.

## Deployment Notes

The repository includes a Dockerfile for a PHP 8.2 Apache container. A hosted deployment needs:
- A PHP/Docker web service that builds from this repository's `Dockerfile`
- A hosted PostgreSQL database
- The six database environment variables listed above configured in the web service
- The SQL schema executed once in the hosted database

Do not commit database passwords, API keys, or other secrets. If a database password has ever been committed to a public repository, rotate it in the database provider even after deleting the file, because old Git history may still contain it.

## DBMS Concepts Demonstrated

- Primary and foreign keys
- UNIQUE, NOT NULL, and CHECK constraints
- Relational database design
- JOIN queries and aggregate functions
- Transactions for purchase and sale workflows
- Data integrity and stock validation

## Database Tables

Roles, Users, Categories, Products, Suppliers, Purchases, Purchase Items, Sales, Sale Items, and Stock Adjustments.
