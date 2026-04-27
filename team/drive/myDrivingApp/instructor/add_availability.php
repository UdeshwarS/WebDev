<?php
/*
    File: add_availability.php
    Description: Handles instructor availability submission from the dashboard.
                 Validates and sanitizes input using filter_input, prevents invalid
                 or duplicate time slots, and inserts a new availability record
                 into the database using PDO prepared statements.

    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
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
        $statement = $dbh->prepare(
            "SELECT `user_id`, `name`
             FROM `users`
             WHERE `user_id` = ? AND `role` = 'instructor'"
        );
        $statement->execute([$_SESSION["user_id"]]);
        $instructor = $statement->fetch(PDO::FETCH_ASSOC);

        if ($instructor) {
            return $instructor;
        }
    }

    $instructor_id = filter_input(INPUT_GET, "instructor_id", FILTER_VALIDATE_INT);

    if ($instructor_id) {
        $statement = $dbh->prepare(
            "SELECT `user_id`, `name`
             FROM `users`
             WHERE `user_id` = ? AND `role` = 'instructor'"
        );
        $statement->execute([$instructor_id]);
        $instructor = $statement->fetch(PDO::FETCH_ASSOC);

        if ($instructor) {
            return $instructor;
        }
    }

    $statement = $dbh->query(
        "SELECT `user_id`, `name`
         FROM `users`
         WHERE `role` = 'instructor'
         ORDER BY `user_id`
         LIMIT 1"
    );
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
$availability_date = filter_input(INPUT_POST, "availability_date", FILTER_SANITIZE_SPECIAL_CHARS);
$start_time = filter_input(INPUT_POST, "start_time", FILTER_SANITIZE_SPECIAL_CHARS);
$end_time = filter_input(INPUT_POST, "end_time", FILTER_SANITIZE_SPECIAL_CHARS);

if (!$availability_date || !$start_time || !$end_time) {
    $_SESSION["dashboard_message"] = "Please fill in date, start time, and end time.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

$current_date = date("Y-m-d");

if ($availability_date < $current_date) {
    $_SESSION["dashboard_message"] = "Please choose today or a future date.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

if ($end_time <= $start_time) {
    $_SESSION["dashboard_message"] = "End time must be later than start time.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

$duplicate_statement = $dbh->prepare(
    "SELECT `id`
     FROM `availability`
     WHERE `instructor_id` = ?
       AND `date` = ?
       AND `start_time` < ?
       AND end_time > ?"
);
$duplicate_statement->execute([
    $instructor["user_id"],
    $availability_date,
    $end_time,     // new end compared to existing start
    $start_time    // new start compared to existing end
]);
$overlapping_slot = $duplicate_statement->fetch(PDO::FETCH_ASSOC);
if ($overlapping_slot) {
    $_SESSION["dashboard_message"] = "This time overlaps with an existing availability slot.";
    $_SESSION["dashboard_message_type"] = "error";
    redirect_to_instructor_dashboard((int) $instructor["user_id"]);
}

$insert_statement = $dbh->prepare(
    "INSERT INTO `availability`
     (`instructor_id`, `date`, `start_time`, `end_time`, `is_booked`)
     VALUES (?, ?, ?, ?, 0)"
);
$insert_statement->execute([$instructor["user_id"], $availability_date, $start_time, $end_time]);

$_SESSION["dashboard_message"] = "Availability slot added successfully.";
$_SESSION["dashboard_message_type"] = "success";
redirect_to_instructor_dashboard((int) $instructor["user_id"]);
