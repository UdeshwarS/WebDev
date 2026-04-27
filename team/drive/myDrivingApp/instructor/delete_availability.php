<?php
/* 
* File: delete_availability.php
* Description: Handles deletion of instructor availability slots.
* Prevents deletion of booked slots and removes only valid unbooked entries.
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
$availability_id = filter_input(INPUT_POST, "availability_id", FILTER_VALIDATE_INT);

if (!$availability_id) {
    $_SESSION["dashboard_message"] = "Invalid availability slot selected.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

$slot_statement = $dbh->prepare(
    "SELECT `id`, `is_booked`
     FROM `availability`
     WHERE `id` = ? 
     AND `instructor_id` = ?"
);
$slot_statement->execute([$availability_id, $instructor["user_id"]]);
$slot = $slot_statement->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    $_SESSION["dashboard_message"] = "That availability slot could not be found.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

if ((int) $slot["is_booked"] === 1) {
    $_SESSION["dashboard_message"] = "Booked availability slots cannot be deleted.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

$delete_statement = $dbh->prepare(
    "DELETE FROM `availability`
     WHERE `id` = ?
     AND `instructor_id` = ?"
);
$delete_statement->execute([$availability_id, $instructor["user_id"]]);

$_SESSION["dashboard_message"] = "Availability slot deleted successfully.";
$_SESSION["dashboard_message_type"] = "success";
redirect_to_instructor_dashboard((int) $instructor["user_id"]);
