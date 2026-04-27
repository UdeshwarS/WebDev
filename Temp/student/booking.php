<?php
/**
 * Name: Udeshwar Singh Sandhu
 * Date Created: 2026-04-03
 * Description: Student booking form page. Displays the booking form with selected date and time
 * and pre-fills student information when a logged-in or testing student is available.
 */

session_start();
require_once __DIR__ . "/../config/connect.php";

$date = filter_input(INPUT_GET, "date", FILTER_SANITIZE_SPECIAL_CHARS);
$time = filter_input(INPUT_GET, "time", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Finds the student whose information should be pre-filled on the booking form.
 * Uses the logged-in session first, then a testing query parameter, then the first student record.
 *
 * @param PDO $dbh Database connection.
 * @returns array Student data with name, email, and phone number.
 */
function get_student_for_booking_form(PDO $dbh): array
{
    if (isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "student") {
        $statement = $dbh->prepare("SELECT user_id, name, email, phone_number FROM users WHERE user_id = ? AND role = 'student'");
        $statement->execute([$_SESSION["user_id"]]);
        $student = $statement->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            return $student;
        }
    }

    $student_id = filter_input(INPUT_GET, "student_id", FILTER_VALIDATE_INT);

    if ($student_id) {
        $statement = $dbh->prepare("SELECT user_id, name, email, phone_number FROM users WHERE user_id = ? AND role = 'student'");
        $statement->execute([$student_id]);
        $student = $statement->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            return $student;
        }
    }

    $statement = $dbh->query("SELECT user_id, name, email, phone_number FROM users WHERE role = 'student' ORDER BY user_id LIMIT 1");
    $student = $statement->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        return $student;
    }

    return [
        "user_id" => 0,
        "name" => "",
        "email" => "",
        "phone_number" => ""
    ];
}

$student = get_student_for_booking_form($dbh);
$query_string = "";

if (!(isset($_SESSION["user_id"], $_SESSION["role"]) && $_SESSION["role"] === "student") && (int) $student["user_id"] > 0) {
    $query_string = "?student_id=" . urlencode((string) $student["user_id"]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!--
        Description: Student booking form markup. Collects booking details and submits
        them to the booking handler.
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=400">
    <title>Book Lesson</title>
    <link rel="stylesheet" href="../assets/css/bookingStyle.css">
    <script src="../assets/js/validateForm.js"></script>
</head>

<body>
    <div class="card">
        <h1>BOOK LESSON</h1>

        <form action="makeBooking.php<?= $query_string ?>" method="post" id="myForm">
            <div class="field">
                <p>NAME:</p>
                <input name="name" id="name" type="text" placeholder="Enter Full Name" required
                    value="<?= htmlspecialchars($student["name"] ?? "") ?>">
            </div>

            <div class="field">
                <p>EMAIL:</p>
                <input id="email" name="email" type="email" placeholder="johnDoe@example.com" required
                    value="<?= htmlspecialchars($student["email"] ?? "") ?>">
            </div>

            <div class="field">
                <p>PHONE:</p>
                <input id="phone" name="phone" type="tel" placeholder="(xxx) xxx-xxxx" required
                    value="<?= htmlspecialchars($student["phone_number"] ?? "") ?>">
            </div>

            <div class="field">
                <p>DATE:</p>
                <input id="date" name="date" type="date" required value="<?= htmlspecialchars($date ?? '') ?>" readonly>
            </div>

            <div class="field">
                <p>TIME:</p>
                <input id="time" name="time" type="time" required value="<?= htmlspecialchars($time ?? '') ?>" readonly>
            </div>

            <div class="field">
                <p>NOTES:</p>
                <textarea id="notes" name="notes" placeholder="Optional notes for the instructor"></textarea>
            </div>

            <button id="sbmt" type="submit" class="btn">BOOK NOW</button>
            <div id="error-message">
                <p></p>
            </div>
        </form>
    </div>
</body>

</html>