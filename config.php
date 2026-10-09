<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error.log');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (Throwable $exception): void {
    error_log(sprintf(
        "[%s] %s in %s on line %d\nStack trace:\n%s\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    ));

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }

    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Application Error</title></head><body style="font-family:Arial,Helvetica,sans-serif;padding:2rem;line-height:1.6;">';
    echo '<h1>Application Error</h1>';
    echo '<p>Something went wrong. Please try again later or contact support.</p>';
    echo '</body></html>';
    exit;
});

function render_error(string $message, int $statusCode = 500): void
{
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=utf-8');
    }

    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title></head><body style="font-family:Arial,Helvetica,sans-serif;padding:2rem;line-height:1.6;">';
    echo '<h1>Error</h1>';
    echo '<p>' . htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>';
    echo '<p><a href="javascript:history.back()">Go back</a></p>';
    echo '</body></html>';
    exit;
}

$host = "localhost";
$username = "root";
$password = "";
$database = "electricity_system";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    render_error("Database connection failed. Please contact support.");
}

$conn->set_charset("utf8mb4");

?>