<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// User needs to wait 30 seconds before submit or else spam
if (isset($_SESSION['last_submit']) && time() - $_SESSION['last_submit'] < 30) {
    redirect('index.php?error=wait');
}


// Remove empty spaces from start and end
$title  = trim($_POST['title'] ?? '');
$artist = trim($_POST['artist'] ?? '');


// Checks server-side for empty titles and max length

// Title is required
if ($title === '') {
    redirect('index.php?error=title_empty');
}

// Title max 80 characters as defined by db in schema
if (mb_strlen($title) > 80) {
    redirect('index.php?error=title_long');
}

// Artist max 60 characters as defined by db in schema
if (mb_strlen($artist) > 60) {
    redirect('index.php?error=artist_long');
}


// Save the song to db if no issues
$stmt = $conn->prepare('INSERT INTO songs (title, artist) VALUES (?, ?)');
$stmt->execute([$title, $artist]);

// Start the cooldown only after a successful save
$_SESSION['last_submit'] = time();

redirect('index.php');




