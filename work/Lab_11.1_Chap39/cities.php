<?php
require_once 'connect.php';

header('Content-Type: application/json; charset=UTF-8');

$minRaw = filter_input(INPUT_GET, 'min', FILTER_UNSAFE_RAW);
$maxRaw = filter_input(INPUT_GET, 'max', FILTER_UNSAFE_RAW);

$minText = is_string($minRaw) ? trim($minRaw) : '';
$maxText = is_string($maxRaw) ? trim($maxRaw) : '';

$min = filter_var($minText, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
$max = filter_var($maxText, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

if ($dbh === null) {
    echo json_encode([
        'ok' => false,
        'error' => $connectError !== '' ? $connectError : "Couldn't connect to the database.",
    ]);
    exit;
}

if ($min === false || $max === false) {
    echo json_encode([
        'ok' => false,
        'error' => 'Please enter whole-number min and max values of 0 or more.',
    ]);
    exit;
}

if ($min > $max) {
    echo json_encode([
        'ok' => false,
        'error' => 'The min value must be less than or equal to the max value.',
    ]);
    exit;
}

try {
    $stmt = $dbh->prepare(
        'SELECT CONVERT(Name USING utf8mb4) AS city_name,
                CountryCode AS country_code,
                Population AS population
         FROM City
         WHERE Population BETWEEN ? AND ?
         ORDER BY Population ASC, Name ASC'
    );
    $stmt->execute([$min, $max]);
    $cities = $stmt->fetchAll();

    echo json_encode([
        'ok' => true,
        'count' => count($cities),
        'cities' => $cities,
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'ok' => false,
        'error' => 'Could not query the city table.',
    ]);
}
