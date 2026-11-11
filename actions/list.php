<?php
require '../includes/db.php';

// Header set to json only
header('Content-Type: application/json');

// All open songs with their vote count
// highest votes first (descending), oldest first on ties
// select songs id title artists, count the votes.id but named as vote count
$sql = "SELECT songs.id, songs.title, songs.artist, COUNT(votes.id) AS vote_count
        FROM songs
-- for each song, find the votes whose song_id matches the song id, and attach them.
        LEFT JOIN votes ON votes.song_id = songs.id
-- only select songs status opened
        WHERE songs.status = 'open'
-- group all votes by song id
        GROUP BY songs.id
-- sort songs by vote descending = if tie, show the oldest first
        ORDER BY vote_count DESC, songs.created_at ASC";

// Fetch all songs and return as JSON
$songs = $conn->query($sql)->fetchAll();

// Check which songs this session token already voted for
$stmt = $conn->prepare('SELECT song_id FROM votes WHERE voter_token = ?');
$stmt->execute([session_id()]);


$votedIds = [];
foreach ($stmt->fetchAll() as $row) {
    $votedIds[] = $row['song_id'];
}

// Add if session voted for each song, add voted, if not = false
foreach ($songs as $index => $song) {
    if (in_array($song['id'], $votedIds)) {
        $songs[$index]['voted'] = true;
    } else {
        $songs[$index]['voted'] = false;
    }
}


echo json_encode($songs);