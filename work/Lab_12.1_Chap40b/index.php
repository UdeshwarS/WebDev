<?php
session_start();
if (!isset($_SESSION['credits'])) {
    $_SESSION['credits'] = 10;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX Slot Machine</title>
    <link rel="stylesheet" href="style.css">
    <script>
        window.initialCredits = <?= json_encode((int) $_SESSION['credits']) ?>;
    </script>
    <script defer src="script.js"></script>
</head>
<body>
    <main class="machine">
        <h1>AJAX Slot Machine</h1>
        <p>Start with 10 credits. Bet at least 1 credit on each spin.</p>

        <section class="status-bar">
            <p><strong>Credits:</strong> <span id="credits"><?= htmlspecialchars((string) $_SESSION['credits']) ?></span></p>
        </section>

        <section class="reels" id="reels">
            <div class="reel"><img src="images/1.png" alt="Fruit 1"></div>
            <div class="reel"><img src="images/2.png" alt="Fruit 2"></div>
            <div class="reel"><img src="images/3.png" alt="Fruit 3"></div>
        </section>

        <form id="slot-form">
            <label for="bet">Bet Amount</label>
            <input id="bet" name="bet" type="number" min="1" step="1" value="1" required>
            <button type="submit" id="spin-button">Spin</button>
        </form>

        <p id="loading" class="loading hidden">Spinning...</p>
        <section id="result" class="result-card">Place a bet and spin.</section>
        <p><a href="reset.php">Reset Game</a></p>
    </main>
</body>
</html>
