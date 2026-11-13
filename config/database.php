<?php
// Inventory Management System - PostgreSQL connection
// Supports Hostless DATABASE_URL and individual DB_* environment variables.

$config = [
    'host' => getenv('DB_HOST') ?: '',
    'port' => getenv('DB_PORT') ?: '5432',
    'dbname' => getenv('DB_NAME') ?: '',
    'username' => getenv('DB_USER') ?: '',
    'password' => getenv('DB_PASSWORD') ?: '',
    'sslmode' => getenv('DB_SSLMODE') ?: 'require',
];

// Hostless provides DATABASE_URL. Use it when individual DB_* values are not set.
$databaseUrl = getenv('DATABASE_URL') ?: '';
if ($databaseUrl !== '') {
    $parts = parse_url($databaseUrl);
    if (is_array($parts)) {
        $query = [];
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        $config['host'] = $parts['host'] ?? $config['host'];
        $config['port'] = (string)($parts['port'] ?? $config['port']);
        $config['dbname'] = ltrim($parts['path'] ?? '', '/');
        $config['username'] = rawurldecode($parts['user'] ?? '');
        $config['password'] = rawurldecode($parts['pass'] ?? '');
        $config['sslmode'] = $query['sslmode'] ?? 'require';
    }
}

// Local development may use an untracked config file.
$localConfig = __DIR__ . '/database.local.php';
if (file_exists($localConfig)) {
    $local = require $localConfig;
    if (is_array($local)) $config = array_merge($config, $local);
}

foreach (['host', 'dbname', 'username', 'password'] as $required) {
    if ($config[$required] === '') {
        error_log('Inventory database configuration is missing: ' . $required);
        http_response_code(500);
        exit('Database configuration is incomplete. Check the hosting database settings.');
    }
}

function db_conn_value($value) {
    return "'" . str_replace(["\\\\", "'"], ["\\\\\\\\", "\\'"], (string)$value) . "'";
}

$connString = 'host=' . db_conn_value($config['host'])
    . ' port=' . db_conn_value($config['port'])
    . ' dbname=' . db_conn_value($config['dbname'])
    . ' user=' . db_conn_value($config['username'])
    . ' password=' . db_conn_value($config['password'])
    . ' sslmode=' . db_conn_value($config['sslmode']);

$connectionWarning = null;
set_error_handler(function ($severity, $message) use (&$connectionWarning) {
    $connectionWarning = $message;
    return true;
});
$conn = pg_connect($connString);
restore_error_handler();

if (!$conn) {
    error_log('Inventory database connection error: ' . ($connectionWarning ?: 'Unknown PostgreSQL connection error'));
    http_response_code(500);
    exit('Database connection failed. Check the hosting database settings.');
}
