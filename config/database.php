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
    if (is_array($local)) {
        $config = array_merge($config, $local);
    }
}

foreach (['host', 'dbname', 'username', 'password'] as $required) {
    if ($config[$required] === '') {
        http_response_code(500);
        exit('Database configuration is incomplete. Set the required environment variables.');
    }
}

$connString = sprintf(
    'host=%s port=%s dbname=%s user=%s password=%s sslmode=%s',
    str_replace(['\\', "'"], ['\\\\', "\\'"], $config['host']),
    $config['port'],
    str_replace(['\\', "'"], ['\\\\', "\\'"], $config['dbname']),
    str_replace(['\\', "'"], ['\\\\', "\\'"], $config['username']),
    str_replace(['\\', "'"], ['\\\\', "\\'"], $config['password']),
    $config['sslmode']
);

$conn = @pg_connect($connString);
if (!$conn) {
    http_response_code(500);
    exit('Database connection failed. Check the hosting database settings.');
}
