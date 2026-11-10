<?php
require 'includes/db.php';
echo $conn->query('SELECT COUNT(*) FROM songs')->fetchColumn();