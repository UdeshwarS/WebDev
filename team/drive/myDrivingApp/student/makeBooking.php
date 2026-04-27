<?php
/*
* File: makeBooking.php
* Description: Processes student lesson bookings. Validates input,
* checks availability, creates booking record, and updates availability status.
* Group Members: Udeshwar Singh Sandhu, Anas Hayat, Akil Kanwar, Ayesha Hasan
* Date: April 22, 2026
*/

session_start();
require_once __DIR__ . "/../config/connect.php";

$errors = [];
$success = false;

$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_SPECIAL_CHARS);
$notes = filter_input(INPUT_POST, "notes", FILTER_SANITIZE_SPECIAL_CHARS);
$date = filter_input(INPUT_POST, "date", FILTER_SANITIZE_SPECIAL_CHARS);
$time = filter_input(INPUT_POST, "time", FILTER_SANITIZE_SPECIAL_CHARS);
$student_id_from_get = filter_input(INPUT_GET, "student_id", FILTER_VALIDATE_INT);

/**
 * Finds the student id for the booking request.
 * Uses the logged-in session first, then a testing query parameter, then the submitted email.
 *
 * @param PDO $dbh Database connection.
 * @param string $email Submitted email address.
 * @param int|false|null $student_id_from_get Student id from the query string.
 * @returns int Student user id or 0 when not found.
 */
function get_student_id_for_booking(PDO $dbh, string $email, $student_id_from_get): int
{
    if (isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "student") {
        return (int) $_SESSION["user_id"];
    }

    if ($student_id_from_get) {
        return (int) $student_id_from_get;
    }

    $statement = $dbh->prepare("SELECT `user_id` FROM `users` WHERE `email` = ? AND `role` = 'student'");
    $statement->execute([$email]);
    $student = $statement->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        return (int) $student["user_id"];
    }

    return 0;
}

if (!$name || !$email || !$phone || !$date || !$time) {
    $errors[] = "Name, email, phone, date, and time are required.";
}

$student_id = get_student_id_for_booking($dbh, $email ?? "", $student_id_from_get);

if ($student_id === 0) {
    $errors[] = "Could not find the student account for this booking.";
}

$availability_statement = $dbh->prepare(
    "SELECT `id`, `instructor_id`, `is_booked`
     FROM `availability`
     WHERE `date` = ?
       AND `start_time` = ?
     LIMIT 1"
);
$availability_statement->execute([$date, $time]);
$availability = $availability_statement->fetch(PDO::FETCH_ASSOC);

if (!$availability) {
    $errors[] = "No availability was found for the selected date and time.";
} elseif ((int) $availability["is_booked"] === 1) {
    $errors[] = "That time slot has already been booked.";
}

if (empty($errors)) {
    try {
        $dbh->beginTransaction();

        $insert_statement = $dbh->prepare(
            "INSERT INTO `bookings` (student_id, instructor_id, availability_id, booking_date, booking_time, status, notes)
             VALUES (?, ?, ?, ?, ?, 'confirmed', ?)"
        );
        $insert_statement->execute([
            $student_id,
            $availability["instructor_id"],
            $availability["id"],
            $date,
            $time,
            $notes
        ]);

        $update_statement = $dbh->prepare("UPDATE `availability` SET `is_booked` = 1 WHERE `id` = ?");
        $update_statement->execute([$availability["id"]]);

        $dbh->commit();
        $success = true;
    } catch (Exception $exception) {
        if ($dbh->inTransaction()) {
            $dbh->rollBack();
        }

        $errors[] = "The booking could not be completed. Please try again.";
    }
}

$dashboard_link = "dashboard.php";
if (!(isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "student") && $student_id > 0) {
    $dashboard_link .= "?student_id=" . urlencode((string) $student_id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!--
        Booking result page. Displays either a confirmation message or
        booking errors after the booking form is submitted.
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=400">
    <title>Booking Confirmed</title>
    <link rel="stylesheet" href="../assets/css/student_booking.css">
</head>

<body>
    <div class="card">
        <?php if ($success) { ?>
            <h1>BOOKING CONFIRMED</h1>
            <p class="info"><?= htmlspecialchars($name) ?>, your lesson has been successfully scheduled.</p>
            <p><strong>Date:</strong> <?= htmlspecialchars($date) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($time) ?></p>
            <a href="<?= htmlspecialchars($dashboard_link) ?>" class="yellow-btn">Return to Dashboard</a>
        <?php } else { ?>
            <h1>BOOKING ERROR</h1>
            <p class="info">There was an error processing your booking:</p>
            <ul>
                <?php foreach ($errors as $error_message) { ?>
                    <li><?= htmlspecialchars($error_message) ?></li>
                <?php } ?>
            </ul>
            <a href="<?= htmlspecialchars($dashboard_link) ?>" class="yellow-btn">Return to Dashboard</a>
        <?php } ?>
    </div>
</body>

</html>