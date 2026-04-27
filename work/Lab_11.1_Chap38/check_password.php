<?php
/**
 * Returns a simple code describing password quality.
 */

header('Content-Type: text/plain; charset=UTF-8');

$password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
$password = is_string($password) ? $password : '';

if ($password === '') {
    echo 'EMPTY';
} elseif (strlen($password) < 6) {
    echo 'ERR_LENGTH';
} elseif (!preg_match('/[A-Z]/', $password)) {
    echo 'ERR_UPPER';
} elseif (!preg_match('/[a-z]/', $password)) {
    echo 'ERR_LOWER';
} elseif (!preg_match('/\d/', $password)) {
    echo 'ERR_DIGIT';
} elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
    echo 'ERR_SYMBOL';
} else {
    echo 'OK';
}
