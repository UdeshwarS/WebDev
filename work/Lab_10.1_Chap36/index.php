<?php
require_once 'connect.php';

$message = '';
$messageClass = 'neutral';
$pollIdValue = '';
$optionValue = '';
$selectedPoll = null;
$recentPolls = [];

if ($dbh !== null) {
    try {
        $recentStmt = $dbh->query(
            'SELECT ID, title, question, option1, option2, option3, option4
             FROM poll
             ORDER BY ID ASC'
        );
        $recentPolls = $recentStmt->fetchAll();
    } catch (PDOException $e) {
        $message = 'Connected to the database, but could not read the poll table.';
        $messageClass = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pollIdRaw = filter_input(INPUT_POST, 'poll_id', FILTER_UNSAFE_RAW);
    $optionRaw = filter_input(INPUT_POST, 'option', FILTER_UNSAFE_RAW);

    $pollIdValue = is_string($pollIdRaw) ? trim($pollIdRaw) : '';
    $optionValue = is_string($optionRaw) ? trim($optionRaw) : '';

    $pollId = filter_var($pollIdValue, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $optionNumber = filter_var($optionValue, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 4]]);

    if ($dbh === null) {
        $message = $connectError !== '' ? $connectError : "Couldn't connect to the database.";
        $messageClass = 'error';
    } elseif ($pollId === false) {
        $message = 'Invalid poll id. Please enter a whole number greater than 0.';
        $messageClass = 'error';
    } elseif ($optionNumber === false) {
        $message = 'Invalid option. Please choose option 1, 2, 3, or 4.';
        $messageClass = 'error';
    } else {
        try {
            $pollStmt = $dbh->prepare(
                'SELECT ID, title, question, option1, option2, option3, option4,
                        vote1, vote2, vote3, vote4
                 FROM poll
                 WHERE ID = ?'
            );
            $pollStmt->execute([$pollId]);
            $selectedPoll = $pollStmt->fetch();

            if ($selectedPoll === false) {
                $message = 'That poll id does not exist.';
                $messageClass = 'error';
            } else {
                $optionColumn = 'option' . $optionNumber;
                $voteColumn = 'vote' . $optionNumber;
                $optionLabel = $selectedPoll[$optionColumn] ?? null;

                if ($optionLabel === null || trim((string) $optionLabel) === '') {
                    $message = 'That option does not exist for the chosen poll.';
                    $messageClass = 'error';
                } else {
                    $updateSql = "UPDATE poll SET $voteColumn = $voteColumn + 1 WHERE ID = ?";
                    $updateStmt = $dbh->prepare($updateSql);
                    $success = $updateStmt->execute([$pollId]);

                    if ($success && $updateStmt->rowCount() === 1) {
                        $message = 'Your vote was recorded for poll #' . $pollId . ' (' . $selectedPoll['title'] . ') - option ' . $optionNumber . ': ' . $optionLabel . '.';
                        $messageClass = 'success';

                        $refreshStmt = $dbh->prepare(
                            'SELECT ID, title, question, option1, option2, option3, option4,
                                    vote1, vote2, vote3, vote4
                             FROM poll
                             WHERE ID = ?'
                        );
                        $refreshStmt->execute([$pollId]);
                        $selectedPoll = $refreshStmt->fetch();
                    } else {
                        $message = 'The vote could not be recorded.';
                        $messageClass = 'error';
                    }
                }
            }
        } catch (PDOException $e) {
            $message = 'The vote could not be recorded because of a database error.';
            $messageClass = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Vote App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="wrapper">
        <section class="card">
            <h1>Vote in a Poll</h1>
            <p>Enter a poll id and the option number you want to vote for.</p>

            <?php if ($message !== ''): ?>
                <p class="message <?= htmlspecialchars($messageClass) ?>"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="post" action="index.php">
                <label for="poll_id">Poll ID</label>
                <input
                    id="poll_id"
                    name="poll_id"
                    type="number"
                    min="1"
                    step="1"
                    required
                    value="<?= htmlspecialchars($pollIdValue) ?>"
                >

                <label for="option">Option</label>
                <select id="option" name="option" required>
                    <option value="">Choose an option</option>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <option value="<?= $i ?>" <?= $optionValue === (string) $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <button type="submit">Submit Vote</button>
            </form>
        </section>

        <section class="card">
            <h2>Available Polls</h2>
            <?php if ($dbh === null): ?>
                <p class="message error"><?= htmlspecialchars($connectError !== '' ? $connectError : "Couldn't connect to the database.") ?></p>
            <?php elseif (count($recentPolls) === 0): ?>
                <p>No polls were found in the database.</p>
            <?php else: ?>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Question</th>
                                <th>Available Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPolls as $poll): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) $poll['ID']) ?></td>
                                    <td><?= htmlspecialchars((string) $poll['title']) ?></td>
                                    <td><?= htmlspecialchars((string) $poll['question']) ?></td>
                                    <td>
                                        <?php
                                        $options = [];
                                        for ($i = 1; $i <= 4; $i++) {
                                            $key = 'option' . $i;
                                            if (isset($poll[$key]) && trim((string) $poll[$key]) !== '') {
                                                $options[] = $i . '. ' . $poll[$key];
                                            }
                                        }
                                        echo htmlspecialchars(implode(' | ', $options));
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <?php if ($selectedPoll !== null): ?>
            <section class="card">
                <h2>Current Totals for Poll #<?= htmlspecialchars((string) $selectedPoll['ID']) ?></h2>
                <p><strong><?= htmlspecialchars((string) $selectedPoll['title']) ?></strong></p>
                <p><?= htmlspecialchars((string) $selectedPoll['question']) ?></p>
                <ul class="totals">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <?php $optionKey = 'option' . $i; ?>
                        <?php $voteKey = 'vote' . $i; ?>
                        <?php if (isset($selectedPoll[$optionKey]) && trim((string) $selectedPoll[$optionKey]) !== ''): ?>
                            <li>
                                Option <?= $i ?>: <?= htmlspecialchars((string) $selectedPoll[$optionKey]) ?>
                                <span><?= htmlspecialchars((string) $selectedPoll[$voteKey]) ?> votes</span>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                </ul>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
