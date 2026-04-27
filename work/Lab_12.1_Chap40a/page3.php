<?php
session_start();

if (!isset($_SESSION['target'], $_SESSION['min'], $_SESSION['max'])) {
    header('Location: page1.php');
    exit;
}

$guess = filter_input(INPUT_POST, 'guess', FILTER_VALIDATE_INT);
$target = $_SESSION['target'];
$min = $_SESSION['min'];
$max = $_SESSION['max'];

if ($guess === false || $guess === null) {
    $message = 'Please enter a valid number.';
    $resultClass = 'error';
    $correct = false;
} elseif ($guess < $min || $guess > $max) {
    $message = 'Your guess must stay inside the chosen range.';
    $resultClass = 'error';
    $correct = false;
} elseif ($guess === $target) {
    $message = 'Correct! You guessed the random number.';
    $resultClass = 'success';
    $correct = true;
    session_unset();
    session_destroy();
} elseif ($guess < $target) {
    $message = 'Not quite. Your guess was too low.';
    $resultClass = 'info';
    $correct = false;
} else {
    $message = 'Not quite. Your guess was too high.';
    $resultClass = 'info';
    $correct = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guessing Game - Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="card">
        <h1>Guessing Game</h1>
        <p class="message <?= $resultClass ?>"><?= htmlspecialchars($message) ?></p>
        <p>Your guess: <strong><?= htmlspecialchars((string) ($guess ?? '')) ?></strong></p>

        <?php if ($correct): ?>
            <a class="button-link" href="page1.php">Play Again</a>
        <?php else: ?>
            <form method="post" action="page2.php">
                <input type="hidden" name="min" value="<?= htmlspecialchars((string) $min) ?>">
                <input type="hidden" name="max" value="<?= htmlspecialchars((string) $max) ?>">
                <button type="submit">Try Again</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
