<?php
/**
 * Lab 9.1 Chapter 31 Exercise 4
 * Simple slot machine using fruit images.
 */

$fruitNames = [
    1 => 'Cherries',
    2 => 'Apple',
    3 => 'Grapes',
    4 => 'Lemon',
    5 => 'Orange',
    6 => 'Pear',
    7 => 'Watermelon'
];

$wheels = [rand(1, 7), rand(1, 7), rand(1, 7)];
$counts = array_count_values($wheels);
rsort($counts);
$bestMatchCount = $counts[0];

$message = 'No match this time. Try again!';
$resultClass = 'lose';
$prizeText = 'You won 0 credits.';

if ($bestMatchCount === 3) {
    $message = 'JACKPOT! All three fruits match!';
    $resultClass = 'jackpot';
    $prizeText = 'You won 100 credits.';
} elseif ($bestMatchCount === 2) {
    $message = 'Nice! Two fruits match!';
    $resultClass = 'win';
    $prizeText = 'You won 10 credits.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruit Slot Machine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="machine">
        <h1>Fruit Slot Machine</h1>
        <p class="subtitle">Spin the wheels and see if the fruits line up.</p>

        <section class="display-window">
            <?php foreach ($wheels as $wheel): ?>
                <figure class="wheel">
                    <img src="images/<?= $wheel ?>.png" alt="<?= htmlspecialchars($fruitNames[$wheel]) ?>">
                    <figcaption><?= htmlspecialchars($fruitNames[$wheel]) ?></figcaption>
                </figure>
            <?php endforeach; ?>
        </section>

        <section class="result-card <?= $resultClass ?>">
            <h2><?= htmlspecialchars($message) ?></h2>
            <p><?= htmlspecialchars($prizeText) ?></p>
        </section>

        <a class="spin-button" href="index.php">Spin Again</a>
    </main>
</body>
</html>
