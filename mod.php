<?php
require 'includes/db.php';
$loginError = '';

// Log in compare the posted passkey with the one in db.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passkey = $_POST['passkey'] ?? '';

    if ($passkey === MOD_PASSKEY) {
        session_regenerate_id(true);
        $_SESSION['is_mod'] = true;
        redirect('mod.php');
    }
    $loginError = 'Wrong passkey';
}

// Log out, forgets the moderator flag
if (isset($_GET['logout'])) {
    unset($_SESSION['is_mod']);
    redirect('index.php');
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

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moderator View</title>
</head>
<body>

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

    <h1>Manage queue</h1>
    
    <a href="mod.php?logout=1">Log out</a>
    
    <p> Manage song queue here</p>

    <!-- The song table goes here -->

    <div id="queue" data-mod="yes"></div>
    <script src="js/queue.js" defer></script>


    <h2>History</h2>

    <?php if (count($history) === 0): ?>
        <p>No answered or deleted songs yet.</p>
    
    <?php else: ?>

        <table>
            <tr>
                <th>Title</th>
                <th>Artist</th>
                <th>Status</th>
                <th>Requested</th>
                <th></th>
            </tr>

            <?php foreach ($history as $song): ?>
                <tr>
                    <td><?php echo htmlspecialchars($song['title']); ?></td>
                    <td><?php echo htmlspecialchars($song['artist']); ?></td>
                    <td><?php echo $song['status']; ?></td>
                    <td><?php echo $song['created_at']; ?></td>
                    <td>
                        <form method="post" action="mod_action.php">
                            <input type="hidden" name="song_id" value="<?php echo $song['id']; ?>">
                            <button type="submit" name="action" value="restore">Restore</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    <?php endif; ?>

<?php endif; ?>


</body>
</html>