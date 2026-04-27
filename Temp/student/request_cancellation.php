<?php
/**
 * Name: Udeshwar Singh Sandhu
 * Date: 2026-04-03
 * Description: Student cancellation request handler. Updates a confirmed booking to
 * cancel_requested so the instructor can review it.
 */

session_start();
require_once __DIR__ . "/../config/connect.php";

/**
 * Finds the student whose dashboard actions should be processed.
 * Uses the logged-in session first, then a testing query parameter, then the first student record.
 *
 * @param PDO $dbh Database connection.
 * @returns array Student data with user_id and name.
 */
function get_student_for_request(PDO $dbh): array
{
    if (isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "student") {
        $statement = $dbh->prepare("SELECT user_id, name FROM users WHERE user_id = ? AND role = 'student'");
        $statement->execute([$_SESSION["user_id"]]);
        $student = $statement->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            return $student;
        }
    }

    $student_id = filter_input(INPUT_GET, "student_id", FILTER_VALIDATE_INT);

    if ($student_id) {
        $statement = $dbh->prepare("SELECT user_id, name FROM users WHERE user_id = ? AND role = 'student'");
        $statement->execute([$student_id]);
        $student = $statement->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            return $student;
        }
    }

    $statement = $dbh->query("SELECT user_id, name FROM users WHERE role = 'student' ORDER BY user_id LIMIT 1");
    $student = $statement->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        return $student;
    }

    return [
        "user_id" => 0,
        "name" => "Student"
    ];
}

/**
 * Redirects back to the correct student dashboard.
 *
 * @param int $student_id Student user id.
 * @returns void
 */
function redirect_to_student_dashboard(int $student_id): void
{
    $location = "dashboard.php";

    if (!(isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "student")) {
        $location .= "?student_id=" . urlencode((string) $student_id);
    }

    header("Location: " . $location);
    exit;
}

$student = get_student_for_request($dbh);
$booking_id = filter_input(INPUT_POST, "booking_id", FILTER_VALIDATE_INT);

if (!$booking_id) {
    $_SESSION["dashboard_message"] = "Invalid booking selected.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_student_dashboard((int) $student["user_id"]);
}

$statement = $dbh->prepare(
    "SELECT ref_no, status
     FROM bookings
     WHERE ref_no = ?
       AND student_id = ?"
);
$statement->execute([$booking_id, $student["user_id"]]);
$booking = $statement->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    $_SESSION["dashboard_message"] = "That booking could not be found.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_student_dashboard((int) $student["user_id"]);
}

if ($booking["status"] !== "confirmed") {
    $_SESSION["dashboard_message"] = "Only confirmed lessons can be requested for cancellation.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_student_dashboard((int) $student["user_id"]);
}

$update_statement = $dbh->prepare("UPDATE bookings SET status = 'cancel_requested' WHERE ref_no = ?");
$update_statement->execute([$booking_id]);

$_SESSION["dashboard_message"] = "Cancellation request sent to the instructor.";
$_SESSION["dashboard_message_type"] = "success";
redirect_to_student_dashboard((int) $student["user_id"]);
