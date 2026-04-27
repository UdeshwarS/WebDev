document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('tip-form');
    const email1 = document.getElementById('email1');
    const email2 = document.getElementById('email2');
    const feedback = document.getElementById('email-feedback');

    function updateEmailFeedback() {
        if (email1.value === '' || email2.value === '') {
            feedback.textContent = '';
            email2.setCustomValidity('');
            return;
        }

        if (email1.value !== email2.value) {
            feedback.textContent = 'Email addresses must match.';
            email2.setCustomValidity('Email addresses do not match.');
        } else {
            feedback.textContent = 'Email addresses match.';
            email2.setCustomValidity('');
        }
    }

    email1.addEventListener('input', updateEmailFeedback);
    email2.addEventListener('input', updateEmailFeedback);

    form.addEventListener('submit', function (event) {
        updateEmailFeedback();
        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
        }
    });
});
