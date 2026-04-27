<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$email = trim((string) filter_input(INPUT_GET, 'email', FILTER_UNSAFE_RAW));
$errorMessage = '';

if ($email === '' || !valid_assignment_email($email)) {
    $errorMessage = 'A valid email address was not supplied. Please return to the login page and sign in again.';
} else {
    try {
        $pdo = get_pdo();
        $statement = $pdo->prepare('SELECT email FROM players WHERE email = :email');
        $statement->execute(['email' => $email]);

        if ($statement->fetch() === false) {
            $errorMessage = 'That player account was not found. Please return to the login page first.';
        }
    } catch (Throwable $error) {
        $errorMessage = 'The database connection is not working yet. Update db.php and import the SQL tables before playing.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Cart Tycoon Play</title>
    <link rel="icon" href="imgs/coffee-cup-icon.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/server.css">
    <script src="js/app.js" defer></script>
</head>

<body>
    <?php if ($errorMessage !== ''): ?>
        <main class="server-shell">
            <section class="panel server-panel">
                <div class="server-stack">
                    <div class="brand-banner">
                        <img src="imgs/coffee-cart-icon.png" alt="Coffee cart icon">
                        <div>
                            <p class="eyebrow">Play Page Error</p>
                            <h1>Unable to open the game</h1>
                            <p class="subtitle"><?= h($errorMessage) ?></p>
                        </div>
                    </div>
                    <div class="page-actions">
                        <a class="primary-button button-link" href="index.php">Back to Login</a>
                    </div>
                </div>
            </section>
        </main>
    <?php else: ?>
        <main class="app-shell">
            <section id="splash-screen" class="screen active" aria-label="Splash screen">
                <div class="panel splash-panel">
                    <p class="eyebrow">PHP Assignment Build</p>
                    <h1>Campus Cart Tycoon</h1>
                    <p class="subtitle">Brew smart. Price smart. Sell smart.</p>
                    <p class="player-chip">Logged in as <?= h($email) ?></p>
                    <canvas id="splash-canvas" width="360" height="220" aria-label="Animated title banner"></canvas>
                    <p id="splash-hint" class="small-note">Loading the cart...</p>
                    <button id="start-button" class="primary-button hidden" type="button">
                        Start Game
                    </button>
                </div>
            </section>

            <section id="game-screen" class="screen" aria-label="Game screen">
                <div class="panel game-panel">
                    <header class="top-bar">
                        <div class="brand-box">
                            <img class="brand-icon" src="imgs/coffee-cart-icon.png" alt="Coffee cart icon">
                            <div class="brand-copy">
                                <p class="eyebrow">Turn-Based Tycoon</p>
                                <h2>Campus Cart Tycoon</h2>
                                <p class="small-note muted-note">Player: <?= h($email) ?></p>
                            </div>
                        </div>
                        <div class="toolbar">
                            <button id="sound-button" class="secondary-button toolbar-button" type="button">
                                Sound On
                            </button>
                            <button id="help-button" class="secondary-button toolbar-button" type="button">
                                Help
                            </button>
                        </div>
                    </header>

                    <section class="status-grid" aria-label="Business status">
                        <article class="stat-card">
                            <span class="stat-label">Cash</span>
                            <span id="cash-value" class="stat-value">$0</span>
                        </article>
                        <article class="stat-card">
                            <span class="stat-label">Supplies</span>
                            <span id="supplies-value" class="stat-value">0 / 0</span>
                        </article>
                        <article class="stat-card">
                            <span class="stat-label">Popularity</span>
                            <span id="popularity-value" class="stat-value">0</span>
                        </article>
                        <article class="stat-card">
                            <span class="stat-label">Cart Level</span>
                            <span id="cart-value" class="stat-value">1</span>
                        </article>
                        <article class="stat-card weather-card">
                            <span class="stat-label">Weather</span>
                            <span id="weather-value" class="stat-value">Sunny</span>
                            <span id="weather-note" class="weather-note">A good day for sales.</span>
                        </article>
                    </section>

                    <section class="progress-section week-progress-section" aria-label="Week progress">
                        <div class="progress-label-row">
                            <span>Week Progress</span>
                            <span id="week-label">Day 1 of 7</span>
                        </div>
                        <div class="week-progress-track" aria-hidden="true">
                            <span class="week-step active"></span>
                            <span class="week-step"></span>
                            <span class="week-step"></span>
                            <span class="week-step"></span>
                            <span class="week-step"></span>
                            <span class="week-step"></span>
                            <span class="week-step"></span>
                        </div>
                        <div class="week-progress-labels" aria-hidden="true">
                            <span>D1</span>
                            <span>D2</span>
                            <span>D3</span>
                            <span>D4</span>
                            <span>D5</span>
                            <span>D6</span>
                            <span>D7</span>
                        </div>
                    </section>

                    <section class="daily-layout" aria-label="Daily actions and manager report">
                        <section class="report-section report-desktop" aria-label="Manager report">
                            <div class="report-heading-row">
                                <p class="section-title">Manager Report</p>
                                <img class="report-icon" src="imgs/coin-icon.png" alt="Coin icon">
                            </div>
                            <p id="report-text-desktop" class="report-text">
                                Welcome to campus. Set your price and prepare your first day.
                            </p>
                            <div class="mini-stats">
                                <span id="customers-value">Last customers: --</span>
                                <span id="revenue-value">Last revenue: $0</span>
                            </div>
                        </section>

                        <div class="controls-column">
                            <section class="price-section" aria-label="Price controls">
                                <div class="section-copy">
                                    <div class="section-heading-row">
                                        <img class="inline-icon" src="imgs/coffee-cup-icon.png" alt="Coffee cup icon">
                                        <p class="section-title">Set Drink Price</p>
                                    </div>
                                    <p class="small-note">Higher prices earn more, but fewer students buy.</p>
                                </div>
                                <div class="price-controls">
                                    <button id="price-down-button" class="secondary-button price-button" type="button">
                                        -
                                    </button>
                                    <span id="price-value" class="price-value">$6</span>
                                    <button id="price-up-button" class="secondary-button price-button" type="button">
                                        +
                                    </button>
                                </div>
                            </section>

                            <section class="actions-section" aria-label="Actions">
                                <p class="section-title actions-heading">Choose your moves for today</p>
                                <div class="action-grid">
                                    <button id="buy-button" class="action-button" type="button">
                                        <span class="action-name">Buy Supplies</span>
                                        <span id="buy-detail" class="action-detail">+8 supplies for $24</span>
                                    </button>
                                    <button id="advertise-button" class="action-button" type="button">
                                        <span class="action-name">Advertise</span>
                                        <span class="action-detail">+1 popularity for $18</span>
                                    </button>
                                    <button id="upgrade-button" class="action-button" type="button">
                                        <span class="action-name">Upgrade Cart</span>
                                        <span id="upgrade-cost" class="action-detail">Cost: $60</span>
                                    </button>
                                    <section class="report-section report-mobile" aria-label="Compact manager report">
                                        <div class="report-heading-row">
                                            <p class="section-title">Manager Report</p>
                                            <img class="report-icon" src="imgs/coin-icon.png" alt="Coin icon">
                                        </div>
                                        <p id="report-text-mobile" class="report-text">
                                            Welcome to campus. Set your price and prepare your first day.
                                        </p>
                                    </section>
                                    <button id="open-button" class="primary-button open-button" type="button">
                                        Open For The Day
                                    </button>
                                </div>
                            </section>
                        </div>
                    </section>
                </div>
            </section>

            <section id="end-screen" class="screen" aria-label="Results screen">
                <div class="panel end-panel">
                    <p class="eyebrow">Campaign Complete</p>
                    <h2>Week Summary</h2>
                    <p id="final-message" class="final-message">Great run.</p>

                    <section class="final-stats" aria-label="Final stats">
                        <article class="stat-card">
                            <span class="stat-label">Final Cash</span>
                            <span id="final-cash" class="stat-value">$0</span>
                        </article>
                        <article class="stat-card">
                            <span class="stat-label">Cash Submitted</span>
                            <span id="best-cash" class="stat-value">$0</span>
                        </article>
                        <article class="stat-card">
                            <span class="stat-label">Final Popularity</span>
                            <span id="final-popularity" class="stat-value">0</span>
                        </article>
                        <article class="stat-card">
                            <span class="stat-label">Cart Level</span>
                            <span id="final-cart-level" class="stat-value">Lv. 1</span>
                        </article>
                    </section>

                    <section id="history-open-button" class="history-section hidden" aria-hidden="true">
                        <span class="history-title">Local history hidden</span>
                        <span class="history-note">This assignment version saves results to the database instead.</span>
                        <ul id="history-list" class="history-list">
                            <li>No previous history yet.</li>
                        </ul>
                    </section>

                    <form id="leaderboard-form" class="final-actions hidden" action="leaderboard.php" method="post">
                        <input type="hidden" name="save_result" value="1">
                        <input type="hidden" name="email" id="result-email" data-email="<?= h($email) ?>"
                            value="<?= h($email) ?>">
                        <input type="hidden" name="final_cash" id="result-cash" value="0">
                        <input type="hidden" name="cart_level" id="result-cart-level" value="1">
                        <input type="hidden" name="popularity" id="result-popularity" value="0">
                        <button class="primary-button" type="submit">Save Result and View Leaderboard</button>
                        <button id="play-again-button" class="secondary-button" type="button">Play Again</button>
                    </form>
                </div>
            </section>
        </main>

        <div id="help-modal" class="modal-overlay hidden" aria-hidden="true">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="help-title">
                <button id="close-help-button" class="icon-button modal-close" type="button" aria-label="Close help">
                    ×
                </button>
                <p class="eyebrow">Need a quick refresher?</p>
                <h3 id="help-title">How to play</h3>
                <ul class="help-list">
                    <li>You have 7 days to grow your cart.</li>
                    <li>Buy supplies, advertise, upgrade, and adjust price before opening.</li>
                    <li>Your cart level raises demand and also increases max supply capacity.</li>
                    <li>Weather changes demand every day.</li>
                    <li>If you do not have enough cash or cart space, the error sound plays.</li>
                    <li>Finish the week, then save your score to the leaderboard.</li>
                </ul>
            </div>
        </div>

        <div id="history-modal" class="modal-overlay hidden" aria-hidden="true">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="history-title">
                <button id="close-history-button" class="icon-button modal-close" type="button"
                    aria-label="Close run history">
                    ×
                </button>
                <p class="eyebrow">Database Mode</p>
                <h3 id="history-title">Result Storage</h3>
                <div class="modal-scroll">
                    <ul id="history-modal-list" class="history-list history-modal-list">
                        <li>Finished runs are saved through leaderboard.php instead of localStorage.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="asset-preload" aria-hidden="true">
            <img id="canvas-cart-image" src="imgs/coffee-cart-icon.png" alt="">
            <img id="canvas-coin-image" src="imgs/coin-icon.png" alt="">
            <audio id="ding-sound" preload="auto">
                <source src="audio/cash-register-ding.mp3" type="audio/mpeg">
            </audio>
            <audio id="coin-sound" preload="auto">
                <source src="audio/coin-pickup.mp3" type="audio/mpeg">
            </audio>
            <audio id="upgrade-sound" preload="auto">
                <source src="audio/upgrade.mp3" type="audio/mpeg">
            </audio>
            <audio id="error-sound" preload="auto">
                <source src="audio/error.mp3" type="audio/mpeg">
            </audio>
            <audio id="day-start-sound" preload="auto">
                <source src="audio/day-start.mp3" type="audio/mpeg">
            </audio>
            <audio id="day-end-sound" preload="auto">
                <source src="audio/game-end.mp3" type="audio/mpeg">
            </audio>
            <audio id="ambience-sound" preload="auto" loop>
                <source src="audio/background music.m4a" type="audio/mp4">
            </audio>
        </div>
    <?php endif; ?>
</body>

</html>