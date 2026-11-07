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

     

<?php endif; ?>


</body>
</html>