/*
    File: dashboard.js
    Description: Handles displaying user bookings in the dashboard and allows
                 users to cancel existing bookings.
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/

//DOM Element
const bookingsBody = document.getElementById("bookingsBody");

/**
 * Retrieves all saved bookings.
 *
 * @returns {Array<Object>} Array of booking objects containing date and time
 */
function getBookings() {
    const saved = localStorage.getItem("bookings");
    return saved ? JSON.parse(saved) : [];
}


/**
 * Renders the bookings table in the dashboard.
 * Displays existing bookings and provides cancel buttons.
 * If fewer than 5 bookings exist, empty rows are added for layout consistency.
 *
 * @returns {void}
 */
function renderBookings() {
    const bookings = getBookings();
    bookingsBody.innerHTML = "";

     // If no bookings exist, display 5 empty rows
    if (bookings.length === 0) {
        for (let i = 0; i < 5; i++) {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td></td>
                <td></td>
                <td></td>
            `;
            bookingsBody.appendChild(row);
        }
        return;
    }

    //make a table with the bookings
    bookings.forEach(function (booking, index) {
        const row = document.createElement("tr");

        row.innerHTML = `
            <td>${booking.date}</td>
            <td>${booking.time}</td>
            <td><button class="cancel-btn" data-index="${index}">X</button></td>
        `;

        bookingsBody.appendChild(row);
    });

    //fill remaining remow for layout
    if (bookings.length < 5) {
        for (let i = bookings.length; i < 5; i++) {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td></td>
                <td></td>
                <td></td>
            `;
            bookingsBody.appendChild(row);
        }
    }


    //add event listener to cancel button so that when pressed, it 
    // removes the booking from the array, updates the bookings and 
    // re-renders table to reflect changes. 
    const cancelButtons = document.querySelectorAll(".cancel-btn");

    cancelButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            const index = button.getAttribute("data-index");
            let bookings = getBookings();

            bookings.splice(index, 1);
            localStorage.setItem("bookings", JSON.stringify(bookings));

            renderBookings();
        });
    });
}

renderBookings();