<?php
/*
    File: db.php
    Description: Establishes a connection to the MySQL database using PDO.
                 This file is included in other PHP files to allow 
                 database access. 
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/

try {
    $dbh = new PDO("mysql:host=localhost;dbname=sandhu3_db", "sandhu3_local", ")ZMaX&FY");
} catch (Exception $e) {
    die("ERROR: Couldn't connect. {$e->getMessage()}");
}
?>