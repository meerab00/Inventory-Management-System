<?php
// Inventory Management System - PostgreSQL connection
// Author: Kainat Jameel

$config = [
    'host' => getenv('DB_HOST') ?: '',
    'port' => getenv('DB_PORT') ?: '5432',
    'dbname' => getenv('DB_NAME') ?: '',
    'username' => getenv('DB_USER') ?: '',
    'password' => getenv('DB_PASSWORD') ?: '',
    'sslmode' => getenv('DB_SSLMODE') ?: 'prefer',
];

// Local development may use an untracked config file.
$localConfig = __DIR__ . '/database.local.php';
if (file_exists($localConfig)) {
    $local = require $localConfig;
    if (is_array($local)) $config = array_merge($config, $local);
}

foreach (['host', 'dbname', 'username', 'password'] as $required) {
    if ($config[$required] === '') {
        http_response_code(500);
        exit('Database configuration is incomplete. Set the required environment variables.');
    }
}

function db_conn_value($value) {
    return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], (string)$value) . "'";
}

$connString = 'host=' . db_conn_value($config['host'])
    . ' port=' . db_conn_value($config['port'])
    . ' dbname=' . db_conn_value($config['dbname'])
    . ' user=' . db_conn_value($config['username'])
    . ' password=' . db_conn_value($config['password'])
    . ' sslmode=' . db_conn_value($config['sslmode']);

$conn = @pg_connect($connString);
if (!$conn) {
    http_response_code(500);
    exit('Database connection failed. Check the hosting database settings.');
}
