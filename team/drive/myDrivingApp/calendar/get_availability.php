<?php
/*
    File: get_availability.php
    Description: Returns available lesson time slots for a selected date in JSON format.
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/

header("Content-Type: application/json");
session_start();
require_once __DIR__ . "/../config/connect.php"; //Database connection

//Retrieve and validate the requested date from GET request
$date = filter_input(INPUT_GET, "date", FILTER_SANITIZE_SPECIAL_CHARS);

//If no valid date is provided, return JSON error and stop execution
if (!$date) {
    echo json_encode(["error" => "Invalid date selected."]);
    exit;
}

//Prevent booking for today or past dates
$today = date("Y-m-d");

if ($date <= $today) {
    echo json_encode(["error" => "Cannot book date."]);
    exit;
}

//Query database for availability on selected date
$statement = $dbh->prepare(
    "SELECT `id`, `start_time`, `is_booked`
     FROM `availability`
     WHERE `date` = ?
     ORDER BY `start_time`"
);
$statement->execute([$date]);
$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

//If no time slots exist, return empty JSON
if (!$rows) {
    echo json_encode([]);
    exit;
}

/*
    Format response:
    start_time => availability (true = free, false = booked)
*/
$times = [];

foreach ($rows as $row) {
    $times[$row["start_time"]] = ((int) $row["is_booked"] === 0);
}

echo json_encode($times);
