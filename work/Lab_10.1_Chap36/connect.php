<?php
/**
 * Database connection for the poll voting app.
 * Fill in your real database password before uploading/testing.
 */

$dbh = null;
$connectError = '';

$host = 'localhost';
$dbname = 'sandhu3_db';
$user = 'sandhu3_local';
$password = ')ZMaX&FY';

try {
    $dbh = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    $connectError = "Couldn't connect to the database.";
}
