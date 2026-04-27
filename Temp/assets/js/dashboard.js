const bookingsBody = document.getElementById("bookingsBody");

function getBookings() {
    const saved = localStorage.getItem("bookings");
    return saved ? JSON.parse(saved) : [];
}

function renderBookings() {
    const bookings = getBookings();
    bookingsBody.innerHTML = "";

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

    bookings.forEach(function (booking, index) {
        const row = document.createElement("tr");

        row.innerHTML = `
            <td>${booking.date}</td>
            <td>${booking.time}</td>
            <td><button class="cancel-btn" data-index="${index}">X</button></td>
        `;

        bookingsBody.appendChild(row);
    });

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