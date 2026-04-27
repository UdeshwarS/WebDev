<?php
try {
    $dbh = new PDO("mysql:host=localhost;dbname=hasana51_db", "hasana51_local", "m!a}ckpi");
} catch (Exception $e) {
    die("ERROR: Couldn't connect. {$e->getMessage()}");
}
?>