<?php

session_start();

// Settings, XAMPP default
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'song_queue');
define('DB_USER', 'root');
define('DB_PASS', '');
define('MOD_PASSKEY', 'password');

// DB Connection
try {
    $conn = new PDO(
        'mysql:host=' . DB_HOST .';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    error_log('DB con. failed: ' .$error->getMessage());
    http_response_code(500);
    exit('Database not reachable');
}

// Function to redirect to a different page
function redirect($url) {
    header('Location: ' . $url);
    exit;
}