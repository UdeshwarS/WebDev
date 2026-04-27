/*
    File: booking.js
    Description: JavaScript code that handles calendar rendering, displaying available booking time slots, etc. 
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/

window.addEventListener("load", function () {


    //DOM Elements
    const monthYear = document.getElementById("monthYear");
    const calendarDates = document.getElementById("calendarDates");
    const selectedDateTitle = document.getElementById("selectedDateTitle");
    const timeSlots = document.getElementById("timeSlots");
    const prevMonth = document.getElementById("prevMonth");
    const nextMonth = document.getElementById("nextMonth");

    //Variables 
    let currentDate = new Date(); //current date
    let selectedCell = null; //selected date cell

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

    /**
     * Converts a given year, month, and day into a formatted date string.
     *
     * @param {number} year - Full year (e.g., 2026)
     * @param {number} month - Month index (0–11)
     * @param {number} day - Day of the month
     * @returns {string} Date formatted as YYYY-MM-DD
     */
    function formatDate(year, month, day) {
        const mm = String(month + 1).padStart(2, "0");
        const dd = String(day).padStart(2, "0");
        return `${year}-${mm}-${dd}`;
    }

    /**
     * Retrieves all saved bookings from localStorage.
     *
     * @returns {Array<Object>} Array of booking objects containing date and time
     */
    function getBookings() {
        const saved = localStorage.getItem("bookings");
        return saved ? JSON.parse(saved) : [];
    }

    /**
     * Checks whether a specific date and time is already booked.
     *
     * @param {string} date - Booking date
     * @param {string} time - Booking time 
     * @returns {boolean} True if the booking exists, otherwise false
     */
    function isAlreadyBooked(date, time) {
        const bookings = getBookings();

        return bookings.some(function (booking) {
            return booking.date === date && booking.time === time;
        });
    }

    /**
     * Saves a booking if it does not already exist.
     *
     * @param {string} date - Booking date
     * @param {string} time - Booking time
     * @returns {boolean} True if saved successfully, false if already booked
     */
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

    /**
     * Renders the calendar and disables past/current dates.
     *
     * @returns {void}
     */
    function renderCalendar() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        calendarDates.innerHTML = "";

        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        //Display current month + year
        monthYear.textContent = `${monthNames[month]} ${year}`;

        for (let i = 0; i < firstDay; i++) {
            const emptyDiv = document.createElement("div");
            emptyDiv.classList.add("date-cell", "empty");
            calendarDates.appendChild(emptyDiv);
        }

        //create date boxes
        for (let day = 1; day <= daysInMonth; day++) {
            const dateDiv = document.createElement("div");
            dateDiv.classList.add("date-cell");
            dateDiv.textContent = day;

            const fullDate = formatDate(year, month, day);
            const thisDate = new Date(year, month, day);

            //disable past and current dates
            if (thisDate <= today) {
                dateDiv.classList.add("disabled-date");
            }
            else {

                dateDiv.addEventListener("click", function () {
                    if (selectedCell) {
                        selectedCell.classList.remove("selected-date");
                    }


                    dateDiv.classList.add("selected-date");
                    selectedCell = dateDiv;

                    showTimes(fullDate);
                });

            }

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


    /**
     * Fetches available time slots for a selected date, and displays them as clickable buttons.
     *
     * @param {string} date
     * @returns {void}
     */
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
                            const url = `../student/booking.php?date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`;
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

    //Navigate to prev month 
    prevMonth.addEventListener("click", function () {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
        timeSlots.innerHTML = "<p>Please click a date to see available times.</p>";
        selectedDateTitle.textContent = "Select a date";
        selectedCell = null;
    });

    //Navigate to next month
    nextMonth.addEventListener("click", function () {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
        timeSlots.innerHTML = "<p>Please click a date to see available times.</p>";
        selectedDateTitle.textContent = "Select a date";
        selectedCell = null;
    });

    //Load calendar 
    renderCalendar();

});