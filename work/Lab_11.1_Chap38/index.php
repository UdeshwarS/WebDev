<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Checker</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
</head>
<body>
    <main class="wrapper">
        <section class="card">
            <h1>Password Checker</h1>
            <p>Type a password below. It must be at least 6 characters long and include uppercase letters, lowercase letters, digits, and symbols.</p>

            <label for="password">Password</label>
            <input id="password" type="password" autocomplete="off">
            <p id="status" class="status neutral">Waiting for input...</p>

            <ul class="rules">
                <li>At least 6 characters</li>
                <li>At least 1 uppercase letter</li>
                <li>At least 1 lowercase letter</li>
                <li>At least 1 digit</li>
                <li>At least 1 symbol</li>
            </ul>
        </section>
    </main>
</body>
</html>
