<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// No user cooldown for voting


// Validates song_id must contain only digits
// Prevents SQLi using Burpsuite/Caido
$songId = $_POST['song_id'] ?? '';
if (!ctype_digit($songId)) {
    redirect('index.php?error=invalid');
}

// The song must exist and still be in the queue (open)
$stmt = $conn->prepare("SELECT id FROM songs WHERE id = ? AND status = 'open'");
$stmt->execute([$songId]);
if ($stmt->fetch() === false) {
    redirect('index.php?error=invalid');
}

// The song must not have the same session id on it
$stmt = $conn->prepare('SELECT id FROM votes WHERE song_id = ? AND voter_token = ?');
$stmt->execute([$songId, session_id()]);
if ($stmt->fetch() !== false) {
    redirect('index.php?error=already_voted');
}

// Insert vote into db and db has fallback to prevent duplicates
try {
    $stmt = $conn->prepare('INSERT INTO votes (song_id, voter_token) VALUES (?, ?)');
    $stmt->execute([$songId, session_id()]);
} catch (PDOException $error) {
    error_log('Vote failed: ' . $error->getMessage());
    redirect('index.php?error=server');
}

redirect('index.php');