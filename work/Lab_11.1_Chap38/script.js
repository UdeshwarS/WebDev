document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const status = document.getElementById('status');
    let timerId = null;

    const messages = {
        EMPTY: 'Waiting for input...',
        ERR_LENGTH: 'Password must be at least 6 characters long.',
        ERR_UPPER: 'Password needs an uppercase letter.',
        ERR_LOWER: 'Password needs a lowercase letter.',
        ERR_DIGIT: 'Password needs a digit.',
        ERR_SYMBOL: 'Password needs a symbol.',
        OK: 'Password looks good.'
    };

    function setState(code) {
        passwordInput.classList.remove('good', 'bad', 'neutral');
        status.classList.remove('good', 'bad', 'neutral', 'loading');
        status.textContent = messages[code] || 'Unable to check the password right now.';

        if (code === 'OK') {
            passwordInput.classList.add('good');
            status.classList.add('good');
        } else if (code === 'EMPTY') {
            passwordInput.classList.add('neutral');
            status.classList.add('neutral');
        } else {
            passwordInput.classList.add('bad');
            status.classList.add('bad');
        }
    }

    function checkPassword() {
        const password = passwordInput.value;

        if (password === '') {
            setState('EMPTY');
            return;
        }

        status.classList.remove('good', 'bad', 'neutral');
        status.classList.add('loading');
        status.textContent = 'Checking password...';

        fetch('check_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: new URLSearchParams({ password: password })
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (code) {
                setState(code.trim());
            })
            .catch(function () {
                status.classList.remove('loading');
                status.classList.add('bad');
                status.textContent = 'Error checking password.';
                passwordInput.classList.remove('good', 'neutral');
                passwordInput.classList.add('bad');
            });
    }

    passwordInput.addEventListener('input', function () {
        clearTimeout(timerId);
        timerId = setTimeout(checkPassword, 200);
    });
});
