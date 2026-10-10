<?php
function loadEnv($path)
{
    if (!file_exists($path)) {
        die('.env file not found at ' . htmlspecialchars($path));
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

loadEnv(dirname(__DIR__) . '/.env');

$serverName = getenv('DB_SERVER') ?: ($_ENV['DB_SERVER'] ?? '');
$connectionOptions = array(
    'Database' => getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? ''),
    'Uid' => getenv('DB_UID') ?: ($_ENV['DB_UID'] ?? ''),
    'PWD' => getenv('DB_PWD') ?: ($_ENV['DB_PWD'] ?? ''),
    'TrustServerCertificate' => filter_var(
        getenv('DB_TRUST_CERT') ?: ($_ENV['DB_TRUST_CERT'] ?? 'true'),
        FILTER_VALIDATE_BOOLEAN
    ),
);

if (empty($serverName) || empty($connectionOptions['Database'])) {
    die('Missing DB configuration in .env (DB_SERVER / DB_DATABASE).');
}

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}
