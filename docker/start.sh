#!/bin/sh
set -e

export LOG_CHANNEL=stderr
export LOG_STACK=stderr
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync

APP_URL="$(php -r '
$url = trim(getenv("APP_URL") ?: "");

if ($url === "" || ! preg_match("#^https?://#i", $url)) {
    $url = trim(getenv("RENDER_EXTERNAL_URL") ?: "http://localhost");
}

$url = rtrim($url, "/");
$parts = parse_url($url);
$valid = is_array($parts)
    && in_array(strtolower($parts["scheme"] ?? ""), ["http", "https"], true)
    && ! empty($parts["host"])
    && ! preg_match("/\s/", $url);

if (! $valid) {
    fwrite(STDERR, "Invalid APP_URL detected; falling back to http://localhost\n");
    $url = "http://localhost";
}

echo $url;
')"
export APP_URL

if [ ! -f .env ]; then
    cp .env.example .env
fi

DB_URL="$(php -r '
$dbUrl = trim(getenv("DB_URL") ?: getenv("DATABASE_URL") ?: "");

if ($dbUrl !== "") {
    $parts = parse_url($dbUrl);
    $valid = is_array($parts)
        && ! empty($parts["scheme"])
        && ! empty($parts["host"]);

    if (! $valid) {
        fwrite(STDERR, "Invalid database URL detected; ignoring it\n");
        $dbUrl = "";
    }
}

echo $dbUrl;
')"
export DB_URL

HAS_DATABASE_CONFIG="$(php -r '
$keys = ["DB_HOST", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD"];

foreach ($keys as $key) {
    if (trim(getenv($key) ?: "") !== "") {
        echo "1";
        exit;
    }
}

echo "0";
')"

if [ "$HAS_DATABASE_CONFIG" = "1" ]; then
    DB_URL=""
    export DB_URL
fi

if [ -n "$DB_URL" ]; then
    DB_CONNECTION="$(php -r '
    $scheme = strtolower((string) (parse_url(getenv("DB_URL") ?: "", PHP_URL_SCHEME) ?: ""));

    echo match ($scheme) {
        "postgres", "postgresql" => "pgsql",
        "mysql", "mariadb" => $scheme,
        "sqlite" => "sqlite",
        default => getenv("DB_CONNECTION") ?: "mysql",
    };
    ')"
    export DB_CONNECTION
else
    if [ "$HAS_DATABASE_CONFIG" = "1" ]; then
        DB_CONNECTION="$(php -r '
        $connection = strtolower(getenv("DB_CONNECTION") ?: "mysql");
        $connection = preg_replace("/[^A-Za-z0-9_-]/", "", $connection);

        echo $connection ?: "mysql";
        ')"
    else
        DB_CONNECTION="sqlite"
        DB_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
        touch "$DB_DATABASE"
        export DB_DATABASE
    fi

    export DB_CONNECTION
fi

php -r '
$file = ".env";
$url = getenv("APP_URL") ?: "http://localhost";
$contents = file_exists($file) ? file_get_contents($file) : "";

if (preg_match("/^APP_URL=.*$/m", $contents)) {
    $contents = preg_replace("/^APP_URL=.*$/m", "APP_URL=".$url, $contents);
} else {
    $contents = rtrim($contents).PHP_EOL."APP_URL=".$url.PHP_EOL;
}

file_put_contents($file, $contents);
'

php -r '
$file = ".env";
$dbUrl = getenv("DB_URL") ?: "";
$host = getenv("DB_HOST") ?: "";
$port = getenv("DB_PORT") ?: "";
$database = getenv("DB_DATABASE") ?: "";
$username = getenv("DB_USERNAME") ?: "";
$password = getenv("DB_PASSWORD") ?: "";
$connection = getenv("DB_CONNECTION") ?: "mysql";
$sessionDriver = getenv("SESSION_DRIVER") ?: "file";
$cacheStore = getenv("CACHE_STORE") ?: "file";
$queueConnection = getenv("QUEUE_CONNECTION") ?: "sync";
$contents = file_exists($file) ? file_get_contents($file) : "";

foreach ([
    "DB_URL" => $dbUrl,
    "DB_CONNECTION" => $connection,
    "DB_HOST" => $host,
    "DB_PORT" => $port,
    "DB_DATABASE" => $database,
    "DB_USERNAME" => $username,
    "DB_PASSWORD" => $password,
    "SESSION_DRIVER" => $sessionDriver,
    "CACHE_STORE" => $cacheStore,
    "QUEUE_CONNECTION" => $queueConnection,
] as $key => $value) {
    if ($value === "" && $key !== "DB_URL") {
        continue;
    }

    if (preg_match("/^".$key."=.*$/m", $contents)) {
        $contents = preg_replace("/^".$key."=.*$/m", $key."=".$value, $contents);
    } else {
        $contents = rtrim($contents).PHP_EOL.$key."=".$value.PHP_EOL;
    }
}

file_put_contents($file, $contents);
'

APP_KEY="$(php -r '
$key = trim(getenv("APP_KEY") ?: "");

if (! preg_match("/^base64:[A-Za-z0-9+\/=]{40,}$/", $key)) {
    $key = "base64:".base64_encode(random_bytes(32));
}

echo $key;
')"
export APP_KEY

php -r '
$file = ".env";
$key = getenv("APP_KEY") ?: "";
$contents = file_exists($file) ? file_get_contents($file) : "";

if (preg_match("/^APP_KEY=.*$/m", $contents)) {
    $contents = preg_replace("/^APP_KEY=.*$/m", "APP_KEY=".$key, $contents);
} else {
    $contents = rtrim($contents).PHP_EOL."APP_KEY=".$key.PHP_EOL;
}

file_put_contents($file, $contents);
'

php artisan optimize:clear
php artisan migrate --force || true
php artisan migrate --path=database/migrations/2026_05_27_010000_ensure_laravel_infrastructure_tables.php --force || true
php artisan app:repair-schema --no-interaction
php artisan db:seed --force
php artisan storage:link || true
php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
