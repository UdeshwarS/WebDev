<?php
/**
 * Name: Udeshwar Singh Sandhu
 * Date Created: 2026-04-03
 * Description: Instructor dashboard page. Displays today's lessons, upcoming lessons,
 * availability slots, cancellation requests, and the instructor availability form.
 */

session_start();
require_once __DIR__ . "/../config/connect.php";

/**
 * Finds the instructor whose dashboard should be displayed.
 * Uses the logged-in session first, then a testing query parameter, then the first instructor record.
 *
 * @param PDO $dbh Database connection.
 * @returns array Instructor data with user_id and name.
 */
function get_instructor_for_dashboard(PDO $dbh): array
{
    if (isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "instructor") {
        $statement = $dbh->prepare("SELECT user_id, name, email FROM users WHERE user_id = ? AND role = 'instructor'");
        $statement->execute([$_SESSION["user_id"]]);
        $instructor = $statement->fetch(PDO::FETCH_ASSOC);

        if ($instructor) {
            return $instructor;
        }
    }

    $instructor_id = filter_input(INPUT_GET, "instructor_id", FILTER_VALIDATE_INT);

    if ($instructor_id) {
        $statement = $dbh->prepare("SELECT user_id, name, email FROM users WHERE user_id = ? AND role = 'instructor'");
        $statement->execute([$instructor_id]);
        $instructor = $statement->fetch(PDO::FETCH_ASSOC);

        if ($instructor) {
            return $instructor;
        }
    }

    $statement = $dbh->query("SELECT user_id, name, email FROM users WHERE role = 'instructor' ORDER BY user_id LIMIT 1");
    $instructor = $statement->fetch(PDO::FETCH_ASSOC);

    if ($instructor) {
        return $instructor;
    }

    return [
        "user_id" => 1,
        "name" => "Instructor",
        "email" => ""
    ];
}

/**
 * Retrieves today's non-cancelled lessons for the instructor.
 *
 * @param PDO $dbh Database connection.
 * @param int $instructor_id Instructor user id.
 * @returns array List of lesson rows.
 */
function get_today_lessons(PDO $dbh, int $instructor_id): array
{
    $statement = $dbh->prepare(
        "SELECT b.ref_no,
                student.name AS student_name,
                b.booking_date,
                b.booking_time,
                b.status,
                b.notes,
                a.end_time
         FROM bookings b
         INNER JOIN users student ON b.student_id = student.user_id
         LEFT JOIN availability a ON b.availability_id = a.id
         WHERE b.instructor_id = ?
           AND b.booking_date = CURDATE()
           AND b.status <> 'cancelled'
         ORDER BY b.booking_time"
    );
    $statement->execute([$instructor_id]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Retrieves future lessons for the instructor.
 *
 * @param PDO $dbh Database connection.
 * @param int $instructor_id Instructor user id.
 * @returns array List of upcoming lesson rows.
 */
function get_upcoming_lessons(PDO $dbh, int $instructor_id): array
{
    $statement = $dbh->prepare(
        "SELECT b.ref_no,
                student.name AS student_name,
                b.booking_date,
                b.booking_time,
                b.status,
                b.notes,
                a.end_time
         FROM bookings b
         INNER JOIN users student ON b.student_id = student.user_id
         LEFT JOIN availability a ON b.availability_id = a.id
         WHERE b.instructor_id = ?
           AND b.booking_date > CURDATE()
           AND b.status <> 'cancelled'
         ORDER BY b.booking_date, b.booking_time"
    );
    $statement->execute([$instructor_id]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Retrieves all availability slots for the instructor.
 *
 * @param PDO $dbh Database connection.
 * @param int $instructor_id Instructor user id.
 * @returns array List of availability rows.
 */
function get_instructor_availability(PDO $dbh, int $instructor_id): array
{
    $statement = $dbh->prepare(
        "SELECT id, date, start_time, end_time, is_booked
         FROM availability
         WHERE instructor_id = ?
         ORDER BY date, start_time"
    );
    $statement->execute([$instructor_id]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Retrieves all cancellation requests waiting for instructor approval.
 *
 * @param PDO $dbh Database connection.
 * @param int $instructor_id Instructor user id.
 * @returns array List of cancellation request rows.
 */
function get_cancellation_requests(PDO $dbh, int $instructor_id): array
{
    $statement = $dbh->prepare(
        "SELECT b.ref_no,
                b.availability_id,
                student.name AS student_name,
                b.booking_date,
                b.booking_time,
                b.notes
         FROM bookings b
         INNER JOIN users student ON b.student_id = student.user_id
         WHERE b.instructor_id = ?
           AND b.status = 'cancel_requested'
         ORDER BY b.booking_date, b.booking_time"
    );
    $statement->execute([$instructor_id]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Builds the correct redirect query string for testing without login.
 *
 * @param int $instructor_id Instructor user id.
 * @returns string Query string to append to links when needed.
 */
function get_instructor_query_string(int $instructor_id): string
{
    if (isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "instructor") {
        return "";
    }

    return "?instructor_id=" . urlencode((string) $instructor_id);
}

$instructor = get_instructor_for_dashboard($dbh);
$today_lessons = get_today_lessons($dbh, (int) $instructor["user_id"]);
$upcoming_lessons = get_upcoming_lessons($dbh, (int) $instructor["user_id"]);
$availability_slots = get_instructor_availability($dbh, (int) $instructor["user_id"]);
$cancellation_requests = get_cancellation_requests($dbh, (int) $instructor["user_id"]);
$query_string = get_instructor_query_string((int) $instructor["user_id"]);

$dashboard_message = $_SESSION["dashboard_message"] ?? "";
$dashboard_message_type = $_SESSION["dashboard_message_type"] ?? "success";
unset($_SESSION["dashboard_message"], $_SESSION["dashboard_message_type"]);

$logout_path = file_exists(__DIR__ . "/../auth/logout.php") ? "../auth/logout.php" : "../index.html";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!--
        Description: Instructor dashboard page markup. Displays lesson tables,
        availability management, and cancellation request controls.
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard</title>
    <link rel="stylesheet" href="../assets/css/landingPageStyle.css">
    <link rel="stylesheet" href="../assets/css/dashboardStyle.css">
</head>

<body>
    <div class="nav-container">
        <div class="nav-left">
            <a href="dashboard.php<?= $query_string ?>" class="yellow-btn">DASHBOARD</a>
        </div>

        <div class="nav-right">
            <a href="<?= htmlspecialchars($logout_path) ?>" class="blue-btn">LOGOUT</a>
        </div>
    </div>

    <h1 class="dashboard-title">INSTRUCTOR DASHBOARD - <?= htmlspecialchars(strtoupper($instructor["name"])) ?></h1>

    <?php if ($dashboard_message !== "") { ?>
        <div class="dashboard-message <?= $dashboard_message_type === 'error' ? 'error-message' : 'success-message' ?>">
            <p><?= htmlspecialchars($dashboard_message) ?></p>
        </div>
    <?php } ?>

    <section class="dashboard-grid">
        <div class="dashboard-card wide-card">
            <h2>Today's Lessons</h2>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>STUDENT</th>
                        <th>DATE</th>
                        <th>TIME</th>
                        <th>STATUS</th>
                        <th>NOTES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($today_lessons) === 0) { ?>
                        <tr>
                            <td colspan="5" class="empty-table-message">No lessons scheduled for today.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($today_lessons as $lesson) { ?>
                            <tr>
                                <td><?= htmlspecialchars($lesson["student_name"]) ?></td>
                                <td><?= htmlspecialchars($lesson["booking_date"]) ?></td>
                                <td><?= htmlspecialchars(substr($lesson["booking_time"], 0, 5)) ?></td>
                                <td>
                                    <span class="status-badge status-<?= htmlspecialchars($lesson["status"]) ?>">
                                        <?= htmlspecialchars(str_replace("_", " ", strtoupper($lesson["status"]))) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($lesson["notes"] ?: "-") ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-card wide-card">
            <h2>Upcoming Lessons</h2>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>STUDENT</th>
                        <th>DATE</th>
                        <th>TIME</th>
                        <th>STATUS</th>
                        <th>NOTES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($upcoming_lessons) === 0) { ?>
                        <tr>
                            <td colspan="5" class="empty-table-message">No future lessons scheduled.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($upcoming_lessons as $lesson) { ?>
                            <tr>
                                <td><?= htmlspecialchars($lesson["student_name"]) ?></td>
                                <td><?= htmlspecialchars($lesson["booking_date"]) ?></td>
                                <td><?= htmlspecialchars(substr($lesson["booking_time"], 0, 5)) ?></td>
                                <td>
                                    <span class="status-badge status-<?= htmlspecialchars($lesson["status"]) ?>">
                                        <?= htmlspecialchars(str_replace("_", " ", strtoupper($lesson["status"]))) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($lesson["notes"] ?: "-") ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-card form-card">
            <h2>Add Availability</h2>
            <form action="add_availability.php<?= $query_string ?>" method="post" class="dashboard-form">
                <label for="availability_date">Date</label>
                <input type="date" id="availability_date" name="availability_date" required>

                <label for="start_time">Start Time</label>
                <input type="time" id="start_time" name="start_time" required>

                <label for="end_time">End Time</label>
                <input type="time" id="end_time" name="end_time" required>

                <button type="submit" class="yellow-btn full-width-btn">ADD SLOT</button>
            </form>
        </div>

        <div class="dashboard-card wide-card">
            <h2>Availability Slots</h2>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>DATE</th>
                        <th>START</th>
                        <th>END</th>
                        <th>BOOKED</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($availability_slots) === 0) { ?>
                        <tr>
                            <td colspan="5" class="empty-table-message">No availability slots added yet.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($availability_slots as $slot) { ?>
                            <tr>
                                <td><?= htmlspecialchars($slot["date"]) ?></td>
                                <td><?= htmlspecialchars(substr($slot["start_time"], 0, 5)) ?></td>
                                <td><?= htmlspecialchars(substr($slot["end_time"], 0, 5)) ?></td>
                                <td><?= $slot["is_booked"] ? "Yes" : "No" ?></td>
                                <td>
                                    <?php if ((int) $slot["is_booked"] === 0) { ?>
                                        <form action="delete_availability.php<?= $query_string ?>" method="post" class="inline-form"
                                            onsubmit="return confirm('Delete this availability slot?');">
                                            <input type="hidden" name="availability_id"
                                                value="<?= htmlspecialchars((string) $slot["id"]) ?>">
                                            <button type="submit" class="table-action-btn delete-btn">DELETE</button>
                                        </form>
                                    <?php } else { ?>
                                        <span class="pending-label">Booked slot</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="dashboard-card wide-card">
            <h2>Cancellation Requests</h2>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>STUDENT</th>
                        <th>DATE</th>
                        <th>TIME</th>
                        <th>NOTES</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($cancellation_requests) === 0) { ?>
                        <tr>
                            <td colspan="5" class="empty-table-message">No cancellation requests waiting.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($cancellation_requests as $request) { ?>
                            <tr>
                                <td><?= htmlspecialchars($request["student_name"]) ?></td>
                                <td><?= htmlspecialchars($request["booking_date"]) ?></td>
                                <td><?= htmlspecialchars(substr($request["booking_time"], 0, 5)) ?></td>
                                <td><?= htmlspecialchars($request["notes"] ?: "-") ?></td>
                                <td>
                                    <form action="approve_cancellation.php<?= $query_string ?>" method="post"
                                        class="inline-form" onsubmit="return confirm('Approve this cancellation request?');">
                                        <input type="hidden" name="booking_id"
                                            value="<?= htmlspecialchars((string) $request["ref_no"]) ?>">
                                        <button type="submit" class="table-action-btn approve-btn">APPROVE</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</body>

</html>