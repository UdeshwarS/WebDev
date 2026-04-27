<?php
declare(strict_types=1);


const DB_HOST = 'localhost';
const DB_NAME = 'sandhu3_db';
const DB_USER = 'sandhu3_local';
const DB_PASS = ')ZMaX&FY';

function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: DB_HOST;
    $dbName = getenv('DB_NAME') ?: DB_NAME;
    $user = getenv('DB_USER') ?: DB_USER;
    $pass = getenv('DB_PASS') ?: DB_PASS;

    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4';

    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function valid_birth_date(string $birthDate): bool
{
    $date = DateTime::createFromFormat('Y-m-d', $birthDate);
    return $date instanceof DateTime && $date->format('Y-m-d') === $birthDate;
}

function valid_assignment_email(string $email): bool
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    return preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)+$/', $email) === 1;
}

function render_simple_page(string $title, string $content): void
{
    echo '<!DOCTYPE html>';
    echo '<html lang="en"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . h($title) . '</title>';
    echo '<link rel="icon" href="imgs/coffee-cup-icon.png" type="image/png">';
    echo '<link rel="stylesheet" href="css/style.css">';
    echo '<link rel="stylesheet" href="css/server.css"></head><body class="server-page">';
    echo '<main class="server-shell"><section class="panel server-panel"><div class="server-stack">';
    echo $content;
    echo '</div></section></main></body></html>';
}
