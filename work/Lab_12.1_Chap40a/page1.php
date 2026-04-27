<?php
session_start();
$errorMessage = $_SESSION['range_error'] ?? '';
unset($_SESSION['range_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guessing Game - Range</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="card">
        <h1>Guessing Game</h1>
        <p>Step 1: Enter the minimum and maximum values for the random number.</p>

        <?php if ($errorMessage !== ''): ?>
            <p class="message error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <form method="post" action="page2.php">
            <label for="min">Minimum</label>
            <input id="min" name="min" type="number" required>

            <label for="max">Maximum</label>
            <input id="max" name="max" type="number" required>

            <button type="submit">Continue</button>
        </form>
    </main>
</body>
</html>
