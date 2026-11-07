<?php
require 'includes/db.php';
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