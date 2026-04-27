<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Lesson</title>
    <link rel="stylesheet" href="../assets/css/landingPageStyle.css">
    <link rel="stylesheet" href="../assets/css/calendarStyle.css">
    
</head>
<body>

    <div class="nav-container">
        <div class="nav-left">
            <a href="../student/dashboard.php"><button class="yellow-btn">BACK</button></a>
        </div>

        <div class="nav-right">
            <button class="blue-btn">LOGOUT</button>
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

    <script src="../assets/js/booking.js"></script>
</body>
</html>