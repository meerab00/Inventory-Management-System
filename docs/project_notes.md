# Inventory Management System - Project Notes

## Project Information

**Project Name:** Inventory Management System  
**Course:** DBMS  
**Author:** Kainat Jameel  
**Database:** PostgreSQL  
**Database Tool:** pgAdmin 4  
**Backend:** PHP  
**Frontend:** HTML, CSS, JavaScript  

## Project Purpose

The Inventory Management System is designed to manage products, categories, suppliers, purchases, sales, and stock information.

The main purpose is to keep inventory records organized and make it easier to identify low-stock products.

## Main Tables

The database contains the following main tables:

1. Roles
2. Users
3. Categories
4. Products
5. Suppliers
6. Purchases
7. Purchase Items
8. Sales
9. Sale Items
10. Stock Adjustments

## Important DBMS Concepts

The project demonstrates:

- Primary Keys
- Foreign Keys
- NOT NULL constraints
- UNIQUE constraints
- CHECK constraints
- SQL queries
- JOIN operations
- Transactions
- Normalization
- Relational database design

## Database Workflow

1. Categories are created.
2. Products are added under categories.
3. Suppliers are stored in the database.
4. Purchases are recorded.
5. Stock quantity can be increased.
6. Sales are recorded.
7. Stock quantity can be decreased.
8. Low-stock products are identified using a SQL query.

## Running the Database

The PostgreSQL database is managed through pgAdmin 4.

SQL files are stored in the `database` folder.

The main database setup file is:

```text
database/schema.sql
