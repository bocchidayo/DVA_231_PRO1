<?php
require 'includes/db.php';

// Pick a message for the ?error= code in the URL (unknown codes show nothing)
$error = $_GET['error'] ?? '';
$errorMessage = '';

if ($error === 'wait') {
    $errorMessage = 'Please wait 30 seconds before requesting another song.';
} elseif ($error === 'title_empty') {
    $errorMessage = 'Please enter a song title.';
} elseif ($error === 'title_long') {
    $errorMessage = 'The song title can be at most 80 characters.';
} elseif ($error === 'artist_long') {
    $errorMessage = 'The artist name can be at most 60 characters.';
} elseif ($error === 'invalid') {
    $errorMessage = 'That song is no longer in the queue.';
} elseif ($error === 'already_voted') {
    $errorMessage = 'You already voted for this song.';
} elseif ($error === 'server') {
    $errorMessage = 'Something went wrong. Please try again.';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/styles.css">
    <script src="js/queue.js" defer></script>
    <title>Song Requests</title>
</head>
<body>
    <main>

        <!-- Error message php if at the top -->
        <?php if ($errorMessage !== ''): ?>
            <p class="error" role="alert"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <h1>Song Requests</h1>
    
        <!-- Show form to submit song -->
        <form id="request-form" method="post" action="submit.php">
            <label for="title">Song title</label>
            <input type="text" id="title" name="title" maxlength="80" required>

            <label for="artist">Artist (Optional)</label>
            <input type="text" id="artist" name="artist" maxlength="60">

            <button type="submit">Request</button>
        </form>

        <h2>Queue</h2>
        <!-- Show queue of songs with AJAX -->
        <div id="queue"></div>

    </main>

</body>
</html>