const monthYear = document.getElementById("monthYear");
const calendarDates = document.getElementById("calendarDates");
const selectedDateTitle = document.getElementById("selectedDateTitle");
const timeSlots = document.getElementById("timeSlots");
const prevMonth = document.getElementById("prevMonth");
const nextMonth = document.getElementById("nextMonth");

let currentDate = new Date();
let selectedCell = null;

// const availability = {
//     "2026-03-28": {
//         "10:00 AM": true,
//         "12:00 PM": false,
//         "2:00 PM": true,
//         "4:00 PM": false
//     },
//     "2026-03-29": {
//         "9:00 AM": false,
//         "11:00 AM": true,
//         "1:00 PM": true,
//         "3:00 PM": false
//     },
//     "2026-03-30": {
//         "10:00 AM": true,
//         "12:00 PM": true,
//         "2:00 PM": false,
//         "5:00 PM": true
//     }
// };

function formatDate(year, month, day) {
    const mm = String(month + 1).padStart(2, "0");
    const dd = String(day).padStart(2, "0");
    return `${year}-${mm}-${dd}`;
}

function getBookings() {
    const saved = localStorage.getItem("bookings");
    return saved ? JSON.parse(saved) : [];
}

function isAlreadyBooked(date, time) {
    const bookings = getBookings();

    return bookings.some(function (booking) {
        return booking.date === date && booking.time === time;
    });
}

function saveBooking(date, time) {
    let bookings = getBookings();

    const alreadyExists = bookings.some(function (booking) {
        return booking.date === date && booking.time === time;
    });

    if (alreadyExists) {
        alert("This time is already booked.");
        return false;
    }

    bookings.push({
        date: date,
        time: time
    });

    localStorage.setItem("bookings", JSON.stringify(bookings));
    return true;
}

function renderCalendar() {
    calendarDates.innerHTML = "";

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    monthYear.textContent = `${monthNames[month]} ${year}`;

    for (let i = 0; i < firstDay; i++) {
        const emptyDiv = document.createElement("div");
        emptyDiv.classList.add("date-cell", "empty");
        calendarDates.appendChild(emptyDiv);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dateDiv = document.createElement("div");
        dateDiv.classList.add("date-cell");
        dateDiv.textContent = day;

        const fullDate = formatDate(year, month, day);

        dateDiv.addEventListener("click", function () {
            if (selectedCell) {
                selectedCell.classList.remove("selected-date");
            }

            dateDiv.classList.add("selected-date");
            selectedCell = dateDiv;

            showTimes(fullDate);
        });

        calendarDates.appendChild(dateDiv);
    }
}

// function showTimes(date) {
//     selectedDateTitle.textContent = `Times for ${date}`;
//     timeSlots.innerHTML = "";

//     if (!availability[date]) {
//         timeSlots.innerHTML = "<p>No times posted for this date.</p>";
//         return;
//     }

//     for (const time in availability[date]) {
//         const btn = document.createElement("button");
//         btn.classList.add("time-slot");

//         const instructorAvailable = availability[date][time];
//         const booked = isAlreadyBooked(date, time);

//         if (instructorAvailable === true && !booked) {
//             btn.textContent = time;
//             btn.classList.add("available");

//             btn.addEventListener("click", function () {
//                 const confirmed = confirm(`Confirm booking for ${date} at ${time}?`);

//                 if (confirmed) {
//                     const saved = saveBooking(date, time);

//                     if (saved) {
//                         alert("Booking confirmed!");
//                         window.location.href = "dashboard.html";
//                     }
//                 }
//             });
//         } else {
//             btn.classList.add("unavailable");
//             btn.disabled = true;

//             if (booked) {
//                 btn.textContent = `${time} - Booked`;
//             } else {
//                 btn.textContent = `${time} - Unavailable`;
//             }
//         }

//         timeSlots.appendChild(btn);
//     }
// }

function showTimes(date) {
    selectedDateTitle.innerHTML = `Times for ${date}`;
    timeSlots.innerHTML = "<p>Loading available times...</p>";

    fetch(`get_availability.php?date=${date}`)
        .then(response => response.json())
        .then(data => {
            timeSlots.innerHTML = "";

            if (data.error) {
                timeSlots.innerHTML = `<p>${data.error}</p>`;
                return;
            }

            if (Object.keys(data).length === 0) {
                timeSlots.innerHTML = "<p>No times posted for this date.</p>";
                return;
            }

            for (const time in data) {
                const btn = document.createElement("button");
                btn.classList.add("time-slot");

                if (data[time]) {
                    btn.innerHTML = time;
                    btn.classList.add("available");

                    btn.addEventListener("click", () => {
                        // Redirect to booking form with date and time pre-filled
                        const url = "/team/DrivingApp/student/booking.php?date=" + encodeURIComponent(date) + "&time=" + encodeURIComponent(time);
                        window.location.href = url;
                    });
                } else {
                    btn.textContent = `${time} - Booked/Unavailable`;
                    btn.classList.add("unavailable");
                    btn.disabled = true;
                }

                timeSlots.appendChild(btn);
            }
        })
        .catch(err => {
            timeSlots.innerHTML = "<p>Error loading times. Try again.</p>";
            console.error(err);
        });
}

prevMonth.addEventListener("click", function () {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
    timeSlots.innerHTML = "<p>Please click a date to see available times.</p>";
    selectedDateTitle.textContent = "Select a date";
    selectedCell = null;
});

nextMonth.addEventListener("click", function () {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
    timeSlots.innerHTML = "<p>Please click a date to see available times.</p>";
    selectedDateTitle.textContent = "Select a date";
    selectedCell = null;
});

renderCalendar();