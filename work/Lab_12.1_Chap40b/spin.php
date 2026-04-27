<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['credits'])) {
    echo json_encode([
        'success' => false,
        'error' => 'NO_SESSION',
        'message' => 'No active game session was found.'
    ]);
    exit;
}

$bet = filter_input(INPUT_POST, 'bet', FILTER_VALIDATE_INT);
$credits = (int) $_SESSION['credits'];

if ($bet === false || $bet === null || $bet < 1) {
    echo json_encode([
        'success' => false,
        'error' => 'BET_TOO_LOW',
        'message' => 'Your bet must be at least 1 credit.',
        'credits' => $credits
    ]);
    exit;
}

if ($bet > $credits) {
    echo json_encode([
        'success' => false,
        'error' => 'BET_TOO_HIGH',
        'message' => 'Your bet cannot be more than the credits you have left.',
        'credits' => $credits
    ]);
    exit;
}

$reels = [rand(1, 7), rand(1, 7), rand(1, 7)];
$counts = array_count_values($reels);
rsort($counts);
$bestMatchCount = $counts[0];

$winnings = 0;
$message = 'No match. You lost your bet.';

if ($bestMatchCount === 3) {
    $winnings = $bet * 10;
    $message = 'Jackpot! All three fruits matched.';
} elseif ($bestMatchCount === 2) {
    $winnings = $bet * 2;
    $message = 'Two fruits matched. You won a small prize.';
}

$credits = $credits - $bet + $winnings;
$_SESSION['credits'] = $credits;
$gameOver = false;

if ($credits <= 0) {
    $credits = 0;
    $gameOver = true;
    session_unset();
    session_destroy();
}

echo json_encode([
    'success' => true,
    'reels' => $reels,
    'winnings' => $winnings,
    'credits' => $credits,
    'message' => $message,
    'gameOver' => $gameOver
]);
