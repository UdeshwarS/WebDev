<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$message = '';
$statusClass = 'status-success';
$email = trim((string) filter_input(INPUT_GET, 'email', FILTER_UNSAFE_RAW));

try {
    $pdo = get_pdo();

    if (
        filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_UNSAFE_RAW) === 'POST' &&
        filter_input(INPUT_POST, 'save_result', FILTER_UNSAFE_RAW) === '1'
    ) {
        $postedEmail = trim((string) filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW));
        $postedCash = filter_input(INPUT_POST, 'final_cash', FILTER_VALIDATE_INT);
        $postedCartLevel = filter_input(INPUT_POST, 'cart_level', FILTER_VALIDATE_INT);
        $postedPopularity = filter_input(INPUT_POST, 'popularity', FILTER_VALIDATE_INT);

        if (
            $postedEmail === '' || !valid_assignment_email($postedEmail) ||
            $postedCash === false || $postedCash === null ||
            $postedCartLevel === false || $postedCartLevel === null ||
            $postedPopularity === false || $postedPopularity === null
        ) {
            throw new RuntimeException('The result data was incomplete or invalid.');
        }

        $checkPlayer = $pdo->prepare('SELECT email FROM players WHERE email = :email');
        $checkPlayer->execute(['email' => $postedEmail]);

        if ($checkPlayer->fetch() === false) {
            throw new RuntimeException('The player account was not found. Please log in again.');
        }

        $insertStatement = $pdo->prepare(
            'INSERT INTO results (email, final_cash, cart_level, popularity, played_date, played_time)
             VALUES (:email, :final_cash, :cart_level, :popularity, CURDATE(), CURTIME())'
        );
        $insertStatement->execute([
            'email' => $postedEmail,
            'final_cash' => $postedCash,
            'cart_level' => $postedCartLevel,
            'popularity' => $postedPopularity,
        ]);

        header('Location: leaderboard.php?email=' . rawurlencode($postedEmail) . '&saved=1');
        exit;
    }

    if ($email !== '' && !valid_assignment_email($email)) {
        throw new RuntimeException('The email parameter is not valid.');
    }

    if (filter_input(INPUT_GET, 'saved', FILTER_UNSAFE_RAW) === '1') {
        $message = 'Your latest run was saved successfully.';
    }

    $userSummary = null;
    $recentRuns = [];

    if ($email !== '') {
        $summaryStatement = $pdo->prepare(
            'SELECT
                COUNT(*) AS games_played,
                MAX(final_cash) AS best_cash,
                ROUND(AVG(final_cash), 1) AS average_cash,
                MAX(cart_level) AS best_cart_level,
                MAX(popularity) AS best_popularity
             FROM results
             WHERE email = :email'
        );
        $summaryStatement->execute(['email' => $email]);
        $userSummary = $summaryStatement->fetch();

        $recentRunsStatement = $pdo->prepare(
            'SELECT final_cash, cart_level, popularity, played_date, played_time
             FROM results
             WHERE email = :email
             ORDER BY played_date DESC, played_time DESC, result_id DESC
             LIMIT 8'
        );
        $recentRunsStatement->execute(['email' => $email]);
        $recentRuns = $recentRunsStatement->fetchAll();
    }

    $leaderboardStatement = $pdo->query(
        'SELECT
            email,
            MAX(final_cash) AS best_cash,
            ROUND(AVG(final_cash), 1) AS average_cash,
            COUNT(*) AS total_runs,
            MAX(cart_level) AS best_cart_level,
            MAX(popularity) AS best_popularity
         FROM results
         GROUP BY email
         ORDER BY best_cash DESC, average_cash DESC, total_runs DESC, email ASC
         LIMIT 5'
    );
    $topPlayers = $leaderboardStatement->fetchAll();
} catch (Throwable $error) {
    $message = $error->getMessage();
    $statusClass = 'status-error';
    $userSummary = null;
    $recentRuns = [];
    $topPlayers = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Cart Tycoon Leaderboard</title>
    <link rel="icon" href="imgs/coffee-cup-icon.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/server.css">
</head>

<body class="server-page">
    <main class="server-shell">
        <section class="panel server-panel">
            <div class="server-stack">
                <div class="brand-banner">
                    <img src="imgs/coffee-cart-icon.png" alt="Coffee cart icon">
                    <div>
                        <p class="eyebrow">Leaderboard</p>
                        <h1>Campus Cart Tycoon Results</h1>
                        <p class="subtitle">Track your own cart stats and compare them with the current top 5 players.
                        </p>
                    </div>
                </div>

                <?php if ($message !== ''): ?>
                    <div class="status-banner <?= h($statusClass) ?>"><?= h($message) ?></div>
                <?php endif; ?>

                <?php if ($email !== ''): ?>
                    <div class="player-chip">Viewing stats for <?= h($email) ?></div>
                <?php endif; ?>

                <section class="summary-card">
                    <p class="section-title">Your stats</p>
                    <?php if ($email === ''): ?>
                        <p class="small-note">Log in and finish a run to see your personal stats here.</p>
                    <?php elseif (empty($recentRuns)): ?>
                        <p class="small-note">No saved runs yet for this player. Start a run and finish the week to create
                            your first leaderboard entry.</p>
                    <?php else: ?>
                        <div class="summary-grid">
                            <article>
                                <span class="summary-label">Games Played</span>
                                <span class="summary-value"><?= h((string) $userSummary['games_played']) ?></span>
                            </article>
                            <article>
                                <span class="summary-label">Best Cash</span>
                                <span class="summary-value">$<?= h((string) $userSummary['best_cash']) ?></span>
                            </article>
                            <article>
                                <span class="summary-label">Average Cash</span>
                                <span class="summary-value">$<?= h((string) $userSummary['average_cash']) ?></span>
                            </article>
                            <article>
                                <span class="summary-label">Best Cart Level</span>
                                <span class="summary-value"><?= h((string) $userSummary['best_cart_level']) ?></span>
                            </article>
                            <article>
                                <span class="summary-label">Best Popularity</span>
                                <span class="summary-value"><?= h((string) $userSummary['best_popularity']) ?></span>
                            </article>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="table-card">
                    <p class="section-title">Top 5 players</p>
                    <?php if (empty($topPlayers)): ?>
                        <p class="small-note">No leaderboard data yet. Import the sample SQL or play a few runs first.</p>
                    <?php else: ?>
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Email</th>
                                    <th>Best Cash</th>
                                    <th>Average Cash</th>
                                    <th>Runs</th>
                                    <th>Best Cart</th>
                                    <th>Best Popularity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topPlayers as $index => $player): ?>
                                    <tr>
                                        <td><?= h((string) ($index + 1)) ?></td>
                                        <td><?= h($player['email']) ?></td>
                                        <td>$<?= h((string) $player['best_cash']) ?></td>
                                        <td>$<?= h((string) $player['average_cash']) ?></td>
                                        <td><?= h((string) $player['total_runs']) ?></td>
                                        <td><?= h((string) $player['best_cart_level']) ?></td>
                                        <td><?= h((string) $player['best_popularity']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </section>

                <?php if (!empty($recentRuns)): ?>
                    <section class="table-card">
                        <p class="section-title">Your recent runs</p>
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Final Cash</th>
                                    <th>Cart Level</th>
                                    <th>Popularity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentRuns as $run): ?>
                                    <tr>
                                        <td><?= h($run['played_date']) ?></td>
                                        <td><?= h($run['played_time']) ?></td>
                                        <td>$<?= h((string) $run['final_cash']) ?></td>
                                        <td><?= h((string) $run['cart_level']) ?></td>
                                        <td><?= h((string) $run['popularity']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>
                <?php endif; ?>

                <div class="page-actions">
                    <?php if ($email !== ''): ?>
                        <a class="primary-button button-link" href="play.php?email=<?= rawurlencode($email) ?>">Play
                            Again</a>
                    <?php endif; ?>
                    <a class="secondary-button button-link" href="index.php">Back to Login</a>
                </div>
            </div>
        </section>
    </main>
</body>

</html>