<?php
require 'includes/db.php';

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

// Move every song still in the queue to deleted status in
$conn->query("UPDATE songs SET status = 'deleted' WHERE status = 'open'");

redirect('mod.php');