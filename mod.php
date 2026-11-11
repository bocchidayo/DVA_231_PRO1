<?php
require 'includes/db.php';
$loginError = '';


// Log out button to forget the moderator flag; POST only
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    unset($_SESSION['is_mod']);
    redirect('index.php');
}


// Log in compare the posted passkey with the one in db.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['passkey'])) {
    if ($_POST['passkey'] === MOD_PASSKEY) {
        session_regenerate_id(true);
        $_SESSION['is_mod'] = true;
        redirect('mod.php');
    }
    sleep(1); // Hardening guessing password form
    $loginError = 'Wrong passkey';
}

// Table with Answered and deleted songs, newest first
$history = [];
if (isset($_SESSION['is_mod'])) {
    $history = $conn->query(
        "SELECT id, title, artist, status, created_at
         FROM songs
         WHERE status != 'open'
         ORDER BY id DESC"
    )->fetchAll();
}

// Shows only the 5 newest history entries
$historyTotal = count($history);
$showAllHistory = isset($_GET['history']);

if ($showAllHistory) {
    $visibleHistory = $history;
} else {
    $visibleHistory = array_slice($history, 0, 5);
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/styles.css">
    <title>Moderator View</title>
</head>
<body>
    <main>

        <?php if (!isset($_SESSION['is_mod'])): ?>

            <h1>Moderator access</h1>

            <?php if ($loginError !== ''): ?>
                <p><?php echo $loginError; ?></p>
            <?php endif; ?>

            <form method="post" action="mod.php">
                <label for="passkey">Passkey</label>
                <input type="password" id="passkey" name="passkey" required>
                <button type="submit">Enter</button>
            </form>

        <?php else: ?>
            <!-- Defer js inside php else -->
            <script src="js/queue.js" defer></script>

            <h1>Manage queue</h1>
            
            <!-- Logout button as post instead of get -->
                <form method="post" action="mod.php">
                    <button type="submit" name="logout" value="1">Log out</button>
                </form>

            <h2>Queue</h2>
            <!-- The song table goes here -->
            <div id="queue" data-mod="yes"></div>

            <h2 id="history">History</h2>

            <?php if (count($history) === 0): ?>
                <p>No answered or deleted songs yet.</p>
            
            <?php else: ?>
                <!-- The history table  -->
                <div class="table-scroll"> <!-- Div to make table horizontal lol scrollable -->
                    <table>
                        <tr>
                            <th class="col-title">Title</th>
                            <th class="col-artist">Artist</th>
                            <th class="col-status">Status</th>
                            <th class="col-time">Requested</th>
                            <th class="col-action">Action</th>
                        </tr>

                        <?php foreach ($visibleHistory as $song): ?>
                            <tr>
                                <td class="cell-title"><?php echo htmlspecialchars($song['title']); ?></td>
                                <td class="cell-title"><?php echo htmlspecialchars($song['artist']); ?></td>
                                <td><?php echo $song['status']; ?></td>
                                <td><?php echo $song['created_at']; ?></td>
                                <td>
                                    <form method="post" action="actions/mod_action.php">
                                        <input type="hidden" name="song_id" value="<?php echo $song['id']; ?>">
                                        <button type="submit" name="action" value="restore">Restore</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <?php if ($historyTotal > 5): ?>
                    <?php if ($showAllHistory): ?>
                        <a href="mod.php#history">Show less</a>
                    <?php else: ?>
                        <a href="mod.php?history=all#history">Show all (<?php echo $historyTotal; ?>)</a>
                    <?php endif; ?>
                <?php endif; ?>

            <?php endif; ?>

            <h2>End of event</h2>
            <p>Clears entire queue for next event</p>

            <form method="post" action="actions/clear_queue.php"
                onsubmit="return confirm('Clear ALL queued songs?');">
                <button type="submit">Clear queue</button>
            </form>

        <?php endif; ?>
    </main>

</body>
</html>