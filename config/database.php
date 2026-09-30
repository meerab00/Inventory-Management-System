<?php

// Inventory Management System
// DBMS Project
// Author: Kainat Jameel

$localConfig = __DIR__ . "/database.local.php";

if (!file_exists($localConfig)) {
    die("Database configuration file is missing.");
}

$config = require $localConfig;

$conn = pg_connect(
    "host={$config['host']}
     port={$config['port']}
     dbname={$config['dbname']}
     user={$config['username']}
     password={$config['password']}"
);

if (!$conn) {
    die("Database connection failed.");
}
?>
