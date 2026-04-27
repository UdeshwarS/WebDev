/*
    File: validateForm.js
    Description: Provides client-side validation for the booking form.
                 Ensures required fields are filled and email format is valid
                 before allowing form submission.
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/

window.addEventListener("load", function () {

    const myForm = document.getElementById("myForm");
    const errorDisplay = document.getElementById("error-message");

    myForm.addEventListener("submit", function (event) {

        let valid = true;

        // Required fields
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const date = document.getElementById("date").value.trim();
        const time = document.getElementById("time").value.trim();

        // Reset error display
        errorDisplay.childNodes[0].innerHTML = "";
        errorDisplay.style.display = "none";

        // Email validation
        const atPosition = email.indexOf("@");
        const dotPosition = email.lastIndexOf(".");
        const emailLength = email.length;

        if (
            email === "" ||
            atPosition < 1 ||
            dotPosition === -1 ||
            dotPosition < atPosition + 2 ||
            dotPosition === emailLength - 1
        ) {
            showError("Email must have format a@b.c<br>Example: username@domain.tld");
            valid = false;
        }

        // Required field validation
        if (name === "") {
            showError("Name is required.");
            valid = false;
        }

        if (phone === "") {
            showError("Phone number is required.");
            valid = false;
        }

        if (date === "") {
            showError("Date is required.");
            valid = false;
        }

        if (time === "") {
            showError("Time is required.");
            valid = false;
        }

        // Prevent submission if invalid
        if (!valid) {
            event.preventDefault();
        }

        /**
         * Displays error message in error box
         *
         * @param {string} message - Error message to display
         * @returns {void}
         */
        function showError(message) {
            errorDisplay.childNodes[0].innerHTML = message;
            errorDisplay.style.display = "block";
        }
    });

});