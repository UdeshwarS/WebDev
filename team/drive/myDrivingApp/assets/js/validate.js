/*
    File: validate.js
    Description: Provides client-side validation for signup and login forms,
                 displaying error messages for invalid or missing input fields.
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/


/**
 * Validates the signup form before submission.
 * Ensures all required fields are filled and correctly formatted.
 *
 * @param {Event} event - Form submission event
 * @returns {void}
 */
function validateSignup(event) {
    let valid = true;

    const name = document.getElementById('name');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const role = document.getElementById('role');
    const phone = document.getElementById('phone_number');
    const license = document.getElementById('license_type');

    // Reset error messages
    document.querySelectorAll('.error-msg').forEach(el => { el.style.display = 'none' });

    //Name validation
    if (name.value.trim() === '') {
        showError('name-error', 'Name is required.');
        valid = false;
    }

    //Checks email 
    if (email.value.trim() === '') {
        showError('email-error', 'Email is required.');
        valid = false;
    } else if (!isValidEmail(email.value.trim())) {
        showError('email-error', 'Please enter a valid email address.');
        valid = false;
    }

    //checks password length
    if (password.value.length < 6) {
        showError('password-error', 'Password must be at least 6 characters.');
        valid = false;
    }

    //checks if phone number is entered
    if (phone.value.trim() === '') {
        showError('phone_number-error', 'Phone number is required.');
        valid = false;
    }

    //checks if role is not selected
    if (role.value === '') {
        showError('role-error', 'Please select a role.');
        valid = false;
    }

    //checks license type for students
    if (role.value === 'student' && license.value === '') {
        showError('license_type-error', 'License type is required.');
        valid = false;
    }

    //prevents form submission if invalid inputs
    if (!valid) {
        event.preventDefault();
    }
}

/**
 * Validates the login form before submission.
 * Ensures email and password fields are not empty.
 *
 * @param {Event} event - Form submission event
 * @returns {void}
 */
function validateLogin(event) {
    let valid = true;

    const email = document.getElementById('email');
    const password = document.getElementById('password');

    document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');

    if (email.value.trim() === '') {
        showError('email-error', 'Email is required.');
        valid = false;
    }

    if (password.value.trim() === '') {
        showError('password-error', 'Password is required.');
        valid = false;
    }

    if (!valid) {
        event.preventDefault();
    }
}

/**
 * Displays an error message inside a specified HTML element.
 *
 * @param {string} id - The ID of the error message element
 * @param {string} message - The error message to display
 * @returns {void}
 */
function showError(id, message) {
    const el = document.getElementById(id);
    el.textContent = message;
    el.style.display = 'block';
}

/**
 * Validates whether a string is in proper email format.
 *
 * @param {string} email - Email address to validate
 * @returns {boolean} True if email format is valid, otherwise false
 */
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Automatically hides the server success message after a short delay.
 */
document.addEventListener("DOMContentLoaded", function () {
    const msg = document.querySelector(".server-success");

    if (msg) {
        setTimeout(() => {
            msg.style.display = "none";
        }, 3000); // hides after 3 seconds
    }
});