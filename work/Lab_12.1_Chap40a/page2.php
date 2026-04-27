<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['min'], $_POST['max'])) {
    $min = filter_input(INPUT_POST, 'min', FILTER_VALIDATE_INT);
    $max = filter_input(INPUT_POST, 'max', FILTER_VALIDATE_INT);

    if ($min === false || $max === false || $min >= $max) {
        $_SESSION['range_error'] = 'Please enter a valid numeric range where min is less than max.';
        header('Location: page1.php');
        exit;
    }

    if (!isset($_SESSION['target']) || !isset($_SESSION['min']) || !isset($_SESSION['max']) || $_SESSION['min'] !== $min || $_SESSION['max'] !== $max) {
        $_SESSION['min'] = $min;
        $_SESSION['max'] = $max;
        $_SESSION['target'] = rand($min, $max);
    }
} elseif (!isset($_SESSION['target'], $_SESSION['min'], $_SESSION['max'])) {
    header('Location: page1.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guessing Game - Guess</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="card">
        <h1>Guessing Game</h1>
        <p>Step 2: Guess a number between <strong><?= htmlspecialchars((string) $_SESSION['min']) ?></strong> and <strong><?= htmlspecialchars((string) $_SESSION['max']) ?></strong>.</p>

        <form method="post" action="page3.php">
            <label for="guess">Your Guess</label>
            <input id="guess" name="guess" type="number" min="<?= htmlspecialchars((string) $_SESSION['min']) ?>" max="<?= htmlspecialchars((string) $_SESSION['max']) ?>" required>
            <button type="submit">Check Guess</button>
        </form>
    </main>
</body>
</html>
