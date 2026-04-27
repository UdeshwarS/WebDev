<?php
/* 
* File: mark_complete.php
* Description: File containing logic to mark lessons as complete. 
* Group Members: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
* Date: April 22, 2026
*/
session_start();
require_once __DIR__ . "/../config/connect.php";

$booking_id = filter_input(INPUT_POST, "booking_id", FILTER_VALIDATE_INT);

if (!$booking_id) {
    $_SESSION["dashboard_message"] = "Invalid booking.";
    $_SESSION["dashboard_message_type"] = "error";
    header("Location: dashboard.php");
    exit;
}

$stmt = $dbh->prepare("
    UPDATE bookings
    SET status = 'completed'
    WHERE ref_no = ?
");

$stmt->execute([$booking_id]);

$_SESSION["dashboard_message"] = "Lesson marked as completed.";
$_SESSION["dashboard_message_type"] = "success";

header("Location: dashboard.php");
exit;