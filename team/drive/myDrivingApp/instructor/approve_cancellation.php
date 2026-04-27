<?php
/*
* File: approve_cancellation.php
* Description: Handles instructor approval of cancellation requests.
* Updates booking status and reopens availability slot.
* Group Members: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
* Date: April 22, 2026
*/

session_start();
require_once __DIR__ . "/../config/connect.php";

/**
 * Finds the instructor whose dashboard action should be processed.
 * Uses the logged-in session first, then a testing query parameter, then the first instructor record.
 *
 * @param PDO $dbh Database connection.
 * @returns array Instructor data with user_id and name.
 */
function get_instructor_for_action(PDO $dbh): array
{
    if (isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "instructor") {
        $statement = $dbh->prepare("SELECT user_id, name FROM users WHERE user_id = ? AND role = 'instructor'");
        $statement->execute([$_SESSION["user_id"]]);
        $instructor = $statement->fetch(PDO::FETCH_ASSOC);

        if ($instructor) {
            return $instructor;
        }
    }

    $instructor_id = filter_input(INPUT_GET, "instructor_id", FILTER_VALIDATE_INT);

    if ($instructor_id) {
        $statement = $dbh->prepare("SELECT user_id, name FROM users WHERE user_id = ? AND role = 'instructor'");
        $statement->execute([$instructor_id]);
        $instructor = $statement->fetch(PDO::FETCH_ASSOC);

        if ($instructor) {
            return $instructor;
        }
    }

    $statement = $dbh->query("SELECT user_id, name FROM users WHERE role = 'instructor' ORDER BY user_id LIMIT 1");
    $instructor = $statement->fetch(PDO::FETCH_ASSOC);

    if ($instructor) {
        return $instructor;
    }

    return [
        "user_id" => 1,
        "name" => "Instructor"
    ];
}

/**
 * Redirects back to the correct instructor dashboard.
 *
 * @param int $instructor_id Instructor user id.
 * @returns void
 */
function redirect_to_instructor_dashboard(int $instructor_id): void
{
    $location = "dashboard.php";

    if (!(isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "instructor")) {
        $location .= "?instructor_id=" . urlencode((string) $instructor_id);
    }

    header("Location: " . $location);
    exit;
}

$instructor = get_instructor_for_action($dbh);
$booking_id = filter_input(INPUT_POST, "booking_id", FILTER_VALIDATE_INT);

if (!$booking_id) {
    $_SESSION["dashboard_message"] = "Invalid cancellation request selected.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

$booking_statement = $dbh->prepare(
    "SELECT ref_no, availability_id, status
     FROM bookings
     WHERE ref_no = ?
       AND instructor_id = ?"
);
$booking_statement->execute([$booking_id, $instructor["user_id"]]);
$booking = $booking_statement->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    $_SESSION["dashboard_message"] = "That cancellation request could not be found.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

if ($booking["status"] !== "cancel_requested") {
    $_SESSION["dashboard_message"] = "Only pending cancellation requests can be approved.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

try {
    $dbh->beginTransaction();

    $update_booking_statement = $dbh->prepare("UPDATE bookings SET status = 'cancelled' WHERE ref_no = ?");
    $update_booking_statement->execute([$booking_id]);

    $update_availability_statement = $dbh->prepare("UPDATE availability SET is_booked = 0 WHERE id = ?");
    $update_availability_statement->execute([$booking["availability_id"]]);

    $dbh->commit();

    $_SESSION["dashboard_message"] = "Cancellation approved and slot reopened.";
    $_SESSION["dashboard_message_type"] = "success";
} catch (Exception $exception) {
    if ($dbh->inTransaction()) {
        $dbh->rollBack();
    }

    $_SESSION["dashboard_message"] = "The cancellation request could not be approved.";
    $_SESSION["dashboard_message_type"] = "error";
}

redirect_to_instructor_dashboard((int) $instructor["user_id"]);
