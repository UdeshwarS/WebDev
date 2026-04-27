<?php

header("Content-Type: application/json");
session_start();
require_once __DIR__ . "/../config/connect.php";

$date = filter_input(INPUT_GET, "date", FILTER_SANITIZE_SPECIAL_CHARS);

if (!$date) {
    echo json_encode(["error" => "Invalid date selected."]);
    exit;
}

$statement = $dbh->prepare(
    "SELECT id, start_time, is_booked
     FROM availability
     WHERE date = ?
     ORDER BY start_time"
);
$statement->execute([$date]);
$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    echo json_encode([]);
    exit;
}

$times = [];

foreach ($rows as $row) {
    $times[$row["start_time"]] = ((int) $row["is_booked"] === 0);
}

echo json_encode($times);
