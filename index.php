<?php
require 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Song Requests</title>
</head>
<body>
  <h1>Song requests</h1>

  <form method="post" action="submit.php">
    <label for="title">Song title</label>
    <input type="text" id="title" name="title" maxlength="80" required>

    <label for="artist">Artist</label>
    <input type="text" id="artist" name="artist" maxlength="60">

    <button type="submit">Request</button>
  </form>

  
</body>
</html>