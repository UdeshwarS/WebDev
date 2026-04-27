/*
    Udeshwar Singh Sandhu
    April 3, 2026
    Client-side validation for the Campus Cart Tycoon login form.
*/
window.addEventListener("load", function () {
    const form = document.getElementById("login-form");
    const emailInput = document.getElementById("email");
    const feedback = document.getElementById("email-feedback");

    if (!form || !emailInput || !feedback) {
        return;
    }

    function emailLooksValid(value) {
        const trimmedValue = value.trim();
        const emailPattern = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)+$/;
        return emailPattern.test(trimmedValue);
    }

    function updateFeedback() {
        if (emailInput.value.trim() === "") {
            feedback.textContent = "Enter the email you want to use for your cart.";
            feedback.classList.remove("feedback-error");
            return true;
        }

        if (!emailLooksValid(emailInput.value)) {
            feedback.textContent = "Use a full email like name@example.com. It needs a dot after the @ symbol.";
            feedback.classList.add("feedback-error");
            return false;
        }

        feedback.textContent = "Email format looks good.";
        feedback.classList.remove("feedback-error");
        return true;
    }

    emailInput.addEventListener("input", updateFeedback);

    form.addEventListener("submit", function (event) {
        if (!updateFeedback()) {
            event.preventDefault();
            emailInput.focus();
        }
    });

    updateFeedback();
});
