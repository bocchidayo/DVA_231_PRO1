<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Check if the user is mod
// Mod is only set in mod login success
if (!isset($_SESSION['is_mod'])) {
    http_response_code(403);
    exit('Forbidden');
}

// Validates song_id must contain only digits
// Prevents SQLi using Burpsuite/Caido
$songId = $_POST['song_id'] ?? '';
if (!ctype_digit($songId)) {
    redirect('../index.php?error=invalid');
}

// Only allow known actions, and decide the new status hardcoded
// Prevents people sending random post values
$action = $_POST['action'] ?? '';
if ($action === 'answered') {
    $newStatus = 'answered';
} elseif ($action === 'delete') {
    $newStatus = 'deleted';
} elseif ($action === 'restore') {
    $newStatus = 'open';
} else {
    redirect('../mod.php');
}

// Change the song status 
// delete = soft delete, the row stays in the database anyway
$stmt = $conn->prepare('UPDATE songs SET status = ? WHERE id = ?');
$stmt->execute([$newStatus, $songId]);


redirect('../mod.php');