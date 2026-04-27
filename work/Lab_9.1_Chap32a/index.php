<?php
/**
 * Lab 9.1 Chapter 32 Exercise 2 a, b, c, d
 * One-page tip calculator using POST.
 */

function field_value(string $name): string
{
    $value = filter_input(INPUT_POST, $name, FILTER_UNSAFE_RAW);
    return is_string($value) ? trim($value) : '';
}

function money(float $amount): string
{
    return '$' . number_format($amount, 2);
}

$paramsOk = false;
$errorMessage = '';
$billData = [];

$serverName = field_value('server_name');
$email1 = field_value('email1');
$email2 = field_value('email2');
$billAmountRaw = field_value('bill_amount');
$tipPercentRaw = field_value('tip_percent');
$cardNumberRaw = field_value('card_number');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $billAmount = filter_var($billAmountRaw, FILTER_VALIDATE_FLOAT);
    $tipPercent = filter_var($tipPercentRaw, FILTER_VALIDATE_INT);
    $cardDigits = preg_replace('/\D+/', '', $cardNumberRaw);

    if (
        $serverName === '' ||
        $email1 === '' ||
        $email2 === '' ||
        $billAmountRaw === '' ||
        $tipPercentRaw === '' ||
        $cardNumberRaw === ''
    ) {
        $errorMessage = 'Error: every field is required.';
    } elseif (!filter_var($email1, FILTER_VALIDATE_EMAIL) || !filter_var($email2, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Error: both email addresses must be valid.';
    } elseif ($email1 !== $email2) {
        $errorMessage = 'Error: the email addresses do not match.';
    } elseif ($billAmount === false || $billAmount < 0) {
        $errorMessage = 'Error: the bill amount must be a valid non-negative number.';
    } elseif ($tipPercent === false || $tipPercent < 0) {
        $errorMessage = 'Error: the tip percentage must be a valid non-negative integer.';
    } elseif (!preg_match('/^\d{16}$/', $cardDigits)) {
        $errorMessage = 'Error: the credit card number must contain exactly 16 digits.';
    } else {
        $tipAmount = $billAmount * ($tipPercent / 100);
        $totalAmount = $billAmount + $tipAmount;
        $maskedCard = '************' . substr($cardDigits, -4);

        $billData = [
            'server_name' => $serverName,
            'email' => $email1,
            'bill_amount' => $billAmount,
            'tip_percent' => $tipPercent,
            'tip_amount' => $tipAmount,
            'total_amount' => $totalAmount,
            'card_number' => $maskedCard
        ];
        $paramsOk = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tip Calculator</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
</head>
<body>
    <main class="page">
        <section class="panel">
            <h1>Tip Calculator</h1>
            <p class="intro">Enter the bill details below. This form uses POST so the credit card number does not appear in the URL.</p>

            <form id="tip-form" method="post" action="index.php" novalidate>
                <label for="server_name">Server Name</label>
                <input id="server_name" name="server_name" type="text" required value="<?= htmlspecialchars($serverName) ?>">

                <label for="email1">Customer Email Address</label>
                <input id="email1" name="email1" type="email" required value="<?= htmlspecialchars($email1) ?>">

                <label for="email2">Confirm Email Address</label>
                <input id="email2" name="email2" type="email" required value="<?= htmlspecialchars($email2) ?>">
                <p id="email-feedback" class="small-message"></p>

                <label for="bill_amount">Bill Amount</label>
                <input id="bill_amount" name="bill_amount" type="number" min="0" step="0.01" required value="<?= htmlspecialchars($billAmountRaw) ?>">

                <label for="tip_percent">Tip Percentage</label>
                <input id="tip_percent" name="tip_percent" type="number" min="0" step="1" required value="<?= htmlspecialchars($tipPercentRaw) ?>">

                <label for="card_number">Credit Card Number</label>
                <input id="card_number" name="card_number" type="text" inputmode="numeric" pattern="\d{16}" maxlength="16" required value="<?= htmlspecialchars(preg_replace('/\D+/', '', $cardNumberRaw)) ?>">

                <button type="submit">Calculate Bill</button>
            </form>
        </section>

        <section class="panel result-panel">
            <?php if ($errorMessage !== ''): ?>
                <div class="message error"><?= htmlspecialchars($errorMessage) ?></div>
            <?php elseif ($paramsOk): ?>
                <h2>Formatted Bill</h2>
                <dl class="bill-output">
                    <div><dt>Server</dt><dd><?= htmlspecialchars($billData['server_name']) ?></dd></div>
                    <div><dt>Email</dt><dd><?= htmlspecialchars($billData['email']) ?></dd></div>
                    <div><dt>Credit Card</dt><dd><?= htmlspecialchars($billData['card_number']) ?></dd></div>
                    <div><dt>Original Amount</dt><dd><?= htmlspecialchars(money($billData['bill_amount'])) ?></dd></div>
                    <div><dt>Tip Percentage</dt><dd><?= htmlspecialchars((string) $billData['tip_percent']) ?>%</dd></div>
                    <div><dt>Tip Amount</dt><dd><?= htmlspecialchars(money($billData['tip_amount'])) ?></dd></div>
                    <div class="total-row"><dt>Total</dt><dd><?= htmlspecialchars(money($billData['total_amount'])) ?></dd></div>
                </dl>
                <form method="get" action="index.php">
                    <button type="submit" class="secondary">Clear</button>
                </form>
            <?php else: ?>
                <h2>Ready to Calculate</h2>
                <p>Your nicely formatted bill will appear here after a valid submission.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
