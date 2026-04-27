<?php
/*
    File: db.php
    Description: Establishes a connection to the MySQL database using PDO.
                 This file is included in other PHP scripts to allow database access.
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/

//variables that define host, database name, username, and password
$dbname = 'sandhu3_db';
$username = 'sandhu3_local';
$password = ')ZMaX&FY';

try {
    //connects to database
    $dbh = new PDO(
        "mysql:host=localhost;dbname=$dbname",
        $username,
        $password
    );
} catch (Exception $e) {

    // Stops execution if database connection fails
    die("Database connection failed: " . $e->getMessage());
}
?>