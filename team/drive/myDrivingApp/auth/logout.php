<?php
/*
    File: logout.php
    Description: Logs the user out by destroying the current session, and redirects the user back to the homepage.
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/
session_start();
session_unset();
session_destroy();
header("Location: ../index.html");
exit;
?>
