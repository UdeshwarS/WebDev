<!--
    File: calendar.php
    Description: Displays an interactive booking calendar that allows students
                 to select a date and view available lesson time slots.
                 Navigation buttons allow switching between months, and JavaScript
                 dynamically loads availability data.

    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
-->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Lesson</title>
    <!-- External CSS stylesheets for layout and calendar styling -->
    <link rel="stylesheet" href="../assets/css/landingPageStyle.css">
    <link rel="stylesheet" href="../assets/css/calendarStyle.css">
    <script src="../assets/js/booking.js"></script>

</head>

<body>

    <div class="nav-container">
        <div class="nav-left">
            <a href="../student/dashboard.php"><button class="yellow-btn">BACK</button></a>
        </div>

        <div class="nav-right">
            <a href="../auth/logout.php" class="blue-btn">Logout</a>
        </div>
    </div>

    <h1 class="dashboard-title">BOOK A LESSON</h1>

    <section class="booking-page">
        <div class="calendar-box">
            <div class="calendar-header">
                <button id="prevMonth" class="blue-btn small-btn">←</button>
                <h2 id="monthYear"></h2>
                <button id="nextMonth" class="blue-btn small-btn">→</button>
            </div>

            <div class="calendar-days">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>

            <div id="calendarDates" class="calendar-dates"></div>
        </div>

        <div class="times-box">
            <h2 id="selectedDateTitle">Select a date</h2>
            <div id="timeSlots" class="time-slots">
                <p>Please click a date to see available times.</p>
            </div>
        </div>
    </section>
</body>

</html>