<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$rawEmail = trim((string) filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW));
$birthDate = trim((string) filter_input(INPUT_POST, 'birth_date', FILTER_UNSAFE_RAW));

if ($rawEmail === '' || $birthDate === '') {
    render_simple_page(
        'Login Error',
        '<div class="brand-banner"><img src="imgs/coffee-cart-icon.png" alt="Coffee cart icon"><div><p class="eyebrow">Login Error</p><h1>Missing login details</h1><p class="subtitle">Please go back and enter both your email address and birth date.</p></div></div>' .
        '<div class="page-actions"><a class="primary-button button-link" href="index.php">Return to Login</a></div>'
    );
    exit;
}

if (!valid_assignment_email($rawEmail) || !valid_birth_date($birthDate)) {
    render_simple_page(
        'Login Error',
        '<div class="brand-banner"><img src="imgs/coffee-cart-icon.png" alt="Coffee cart icon"><div><p class="eyebrow">Login Error</p><h1>Invalid form values</h1><p class="subtitle">Use a valid email like name@example.com and a real birth date.</p></div></div>' .
        '<div class="page-actions"><a class="primary-button button-link" href="index.php">Try Again</a></div>'
    );
    exit;
}

try {
    $pdo = get_pdo();

    $selectStatement = $pdo->prepare('SELECT email, birth_date FROM players WHERE email = :email');
    $selectStatement->execute(['email' => $rawEmail]);
    $player = $selectStatement->fetch();

    if ($player !== false) {
        if ($player['birth_date'] !== $birthDate) {
            render_simple_page(
                'Email Already Taken',
                '<div class="brand-banner"><img src="imgs/coffee-cart-icon.png" alt="Coffee cart icon"><div><p class="eyebrow">Login Blocked</p><h1>Email already taken</h1><p class="subtitle">That email is already registered with a different birth date. Please try again.</p></div></div>' .
                '<div class="page-actions"><a class="primary-button button-link" href="index.php">Back to Login</a></div>'
            );
            exit;
        }

        render_simple_page(
            'Welcome Back',
            '<div class="brand-banner"><img src="imgs/coffee-cart-icon.png" alt="Coffee cart icon"><div><p class="eyebrow">Welcome Back</p><h1>Ready for another week?</h1><p class="subtitle">We found your account and you have been here before.</p></div></div>' .
            '<div class="player-chip">Player: ' . h($rawEmail) . '</div>' .
            '<div class="page-actions"><a class="primary-button button-link" href="play.php?email=' . rawurlencode($rawEmail) . '">Go to Play Page</a><a class="secondary-button button-link" href="leaderboard.php?email=' . rawurlencode($rawEmail) . '">View Leaderboard</a></div>'
        );
        exit;
    }

    $insertStatement = $pdo->prepare('INSERT INTO players (email, birth_date) VALUES (:email, :birth_date)');
    $insertStatement->execute([
        'email' => $rawEmail,
        'birth_date' => $birthDate,
    ]);

    render_simple_page(
        'Welcome to Campus Cart Tycoon',
        '<div class="brand-banner"><img src="imgs/coffee-cart-icon.png" alt="Coffee cart icon"><div><p class="eyebrow">New Player</p><h1>Your account is ready</h1><p class="subtitle">Welcome to the game. Your player record has been added to the database.</p></div></div>' .
        '<div class="player-chip">Player: ' . h($rawEmail) . '</div>' .
        '<div class="page-actions"><a class="primary-button button-link" href="play.php?email=' . rawurlencode($rawEmail) . '">Start Your First Run</a><a class="secondary-button button-link" href="index.php">Back to Login</a></div>'
    );
} catch (Throwable $error) {
    render_simple_page(
        'Database Error',
        '<div class="brand-banner"><img src="imgs/coffee-cart-icon.png" alt="Coffee cart icon"><div><p class="eyebrow">Database Error</p><h1>Could not connect to the database</h1><p class="subtitle">Check the credentials in db.php and make sure your tables are imported.</p></div></div>' .
        '<div class="page-actions"><a class="primary-button button-link" href="index.php">Back to Login</a></div>'
    );
}
