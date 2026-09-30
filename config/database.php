<?php

// Inventory Management System
// DBMS Project
// Author: Kainat Jameel
// PostgreSQL Database Connection

$host = "localhost";
$port = "5432";
$dbname = "inventory_management";
$username = "postgres";
$password = "YOUR_POSTGRES_PASSWORD";

$conn = pg_connect(
    "host=$host port=$port dbname=$dbname user=$username password=$password"
);

if (!$conn) {
    die("Database connection failed.");
}

?>

