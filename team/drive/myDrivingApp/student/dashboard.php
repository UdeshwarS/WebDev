<?php
/*
* File: dashboard.php
* Description: Student dashboard page. Displays upcoming bookings, total driving hours,
* booking status, and allows students to request lesson cancellations.
* Group Members: Udeshwar Singh Sandhu, Anas Hayat, Akil Kanwar, Ayesha Hasan
* Date: April 22, 2026
*/

session_start();
require_once __DIR__ . "/../config/connect.php";

/**
 * Finds the student whose dashboard should be displayed.
 * Uses the logged-in session first, then a testing query parameter, then the first student record.
 *
 * @param PDO $dbh Database connection.
 * @returns array Student data with user_id and name.
 */
function get_student_for_dashboard(PDO $dbh): array
{
    if (isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "student") {
        $statement = $dbh->prepare("SELECT `user_id`, `name`, `email`, `phone_number` FROM `users` WHERE `user_id` = ? AND `role` = 'student'");
        $statement->execute([$_SESSION["user_id"]]);
        $student = $statement->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            return $student;
        }
    }

    $student_id = filter_input(INPUT_GET, "student_id", FILTER_VALIDATE_INT);

    if ($student_id) {
        $statement = $dbh->prepare("SELECT `user_id`, `name`, `email`, `phone_number` FROM `users` WHERE `user_id` = ? AND `role` = 'student'");
        $statement->execute([$student_id]);
        $student = $statement->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            return $student;
        }
    }

    $statement = $dbh->query("SELECT `user_id`, `name`, `email`, `phone_number` FROM `users` WHERE `role` = 'student' ORDER BY `user_id` LIMIT 1");
    $student = $statement->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        return $student;
    }

    return [
        "user_id" => 0,
        "name" => "Student",
        "email" => "",
        "phone_number" => ""
    ];
}

/**
 * Retrieves all upcoming bookings for the selected student.
 *
 * @param PDO $dbh Database connection.
 * @param int $student_id Student user id.
 * @returns array List of upcoming booking rows.
 */
function get_upcoming_student_bookings(PDO $dbh, int $student_id): array
{
    $statement = $dbh->prepare(
        "SELECT b.ref_no,
                b.booking_date,
                b.booking_time,
                b.status,
                b.notes,
                instructor.name AS instructor_name,
                a.end_time
         FROM `bookings` b
         LEFT JOIN `users` instructor ON b.instructor_id = instructor.user_id
         LEFT JOIN `availability` a ON b.availability_id = a.id
         WHERE b.student_id = ?
           AND b.booking_date >= CURDATE()
         ORDER BY b.booking_date, b.booking_time"
    );
    $statement->execute([$student_id]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Calculates the student's total confirmed driving hours from non-cancelled bookings.
 *
 * @param PDO $dbh Database connection.
 * @param int $student_id Student user id.
 * @returns float Total driving hours.
 */
function get_total_driving_hours(PDO $dbh, int $student_id): float
{
    $statement = $dbh->prepare(
        "SELECT COALESCE(SUM(TIMESTAMPDIFF(MINUTE, a.start_time, a.end_time)), 0) AS total_minutes
         FROM bookings b
         INNER JOIN `availability` a ON b.availability_id = a.id
         WHERE b.student_id = ?
           AND b.status = 'completed'"
    );
    $statement->execute([$student_id]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return 0.0;
    }

    return ((float) $row["total_minutes"]) / 60;
}

/**
 * Builds the correct redirect query string for testing without login.
 *
 * @param int $student_id Student user id.
 * @returns string Query string to append to links when needed.
 */
function get_student_query_string(int $student_id): string
{
    if (isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "student") {
        return "";
    }

    return "?student_id=" . urlencode((string) $student_id);
}

$student = get_student_for_dashboard($dbh);
$upcoming_bookings = get_upcoming_student_bookings($dbh, (int) $student["user_id"]);
$total_hours_driven = get_total_driving_hours($dbh, (int) $student["user_id"]);
$query_string = get_student_query_string((int) $student["user_id"]);

$dashboard_message = $_SESSION["dashboard_message"] ?? "";
$dashboard_message_type = $_SESSION["dashboard_message_type"] ?? "success";
unset($_SESSION["dashboard_message"], $_SESSION["dashboard_message_type"]);

$logout_path = file_exists(__DIR__ . "/../auth/logout.php") ? "../auth/logout.php" : "../index.html";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!--
        Description: Student dashboard page markup. Displays upcoming bookings,
        total driving hours, and request cancellation controls.
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/landingPageStyle.css">
    <link rel="stylesheet" href="../assets/css/dashboardStyle.css">
</head>

<body>
    <div class="nav-container">
        <div class="nav-left">
            <a href="../calendar/calendar.php<?= $query_string ?>" class="yellow-btn">BOOK</a>
        </div>

        <div class="nav-right">
            <a href="<?= htmlspecialchars($logout_path) ?>" class="blue-btn">LOGOUT</a>
        </div>
    </div>

    <h1 class="dashboard-title">HELLO, <?= htmlspecialchars(strtoupper($student["name"])) ?>!</h1>

    <?php if ($dashboard_message !== "") { ?>
        <div class="dashboard-message <?= $dashboard_message_type === 'error' ? 'error-message' : 'success-message' ?>">
            <p><?= htmlspecialchars($dashboard_message) ?></p>
        </div>
    <?php } ?>

    <section class="dashboard student-dashboard-layout">
        <div class="dashboard-card bookings-card">
            <h2>My Bookings</h2>

            <table class="bookings-table dashboard-table">
                <thead>
                    <tr>
                        <th>DATE</th>
                        <th>TIME</th>
                        <th>INSTRUCTOR</th>
                        <th>STATUS</th>
                        <th>CANCEL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($upcoming_bookings) === 0) { ?>
                        <tr>
                            <td colspan="5" class="empty-table-message">No upcoming lessons found.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($upcoming_bookings as $booking) { ?>
                            <tr>
                                <td><?= htmlspecialchars($booking["booking_date"]) ?></td>
                                <td><?= htmlspecialchars(substr($booking["booking_time"], 0, 5)) ?></td>
                                <td><?= htmlspecialchars($booking["instructor_name"] ?? "Instructor") ?></td>
                                <td>
                                    <span class="status-badge status-<?= htmlspecialchars($booking["status"]) ?>">
                                        <?= htmlspecialchars(str_replace("_", " ", strtoupper($booking["status"]))) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $bookingDateTime = $booking["booking_date"] . " " . $booking["booking_time"];
                                    $isFuture = strtotime($bookingDateTime) > time();
                                    ?>

                                    <?php if ($booking["status"] === "confirmed" && $isFuture) { ?>
                                        <form action="request_cancellation.php<?= $query_string ?>" method="post"
                                            class="inline-form"
                                            onsubmit="return confirm('Send a cancellation request for this lesson?');">
                                            <input type="hidden" name="booking_id"
                                                value="<?= htmlspecialchars((string) $booking["ref_no"]) ?>">
                                            <button type="submit" class="table-action-btn warning-btn">REQUEST</button>
                                        </form>
                                    <?php } elseif ($booking["status"] === "cancel_requested") { ?>
                                        <span class="pending-label">Pending approval</span>

                                    <?php } elseif ($booking["status"] === "completed") { ?>
                                        <span class="completed-label"></span>

                                    <?php } elseif ($booking["status"] === "cancelled") { ?>
                                        <span class="cancelled-label">Cancelled</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-card hours-card">
            <h2>Total Hours Driven</h2>
            <p class="hours-number"><?= htmlspecialchars(number_format($total_hours_driven, 1)) ?></p>
            <p class="hours-subtitle">Based on all completed lessons</p>
        </div>
    </section>
</body>

</html>