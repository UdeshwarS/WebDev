<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Cart Tycoon Login</title>
    <link rel="icon" href="imgs/coffee-cup-icon.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/server.css">
    <script src="js/login.js" defer></script>
</head>

<body class="server-page">
    <main class="server-shell">
        <section class="panel server-panel">
            <div class="server-stack">
                <div class="brand-banner">
                    <img src="imgs/coffee-cart-icon.png" alt="Coffee cart icon">
                    <div>
                        <p class="eyebrow">Server-side Assignment</p>
                        <h1>Campus Cart Tycoon</h1>
                        <p class="subtitle">Log in with your email and birth date to start your 7-day cart run.</p>
                    </div>
                </div>

                <section class="notice-card">
                    <p class="section-title">How this version works</p>
                    <p class="small-note">New players are registered automatically. Returning players can log back in
                        with the same email and birth date, then their finished run is saved to the leaderboard.</p>
                </section>

                <section class="form-card">
                    <p class="section-title">Player login</p>
                    <form id="login-form" action="login.php" method="post">
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="email">Email address</label>
                                <input id="email" name="email" type="email" maxlength="255" autocomplete="email"
                                    placeholder="name@example.com" required>
                                <p id="email-feedback" class="feedback-text">Enter the email you want to use for your
                                    cart.</p>
                            </div>

                            <div class="form-field">
                                <label for="birth_date">Birth date</label>
                                <input id="birth_date" name="birth_date" type="date" max="<?= h(date('Y-m-d')) ?>"
                                    required>
                                <p class="feedback-text">This assignment uses birth date as the password.</p>
                            </div>
                        </div>

                        <button class="primary-button" type="submit">Continue to Login</button>
                    </form>
                </section>
            </div>
        </section>
    </main>
</body>

</html>