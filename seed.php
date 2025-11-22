<?php
require 'db.php';
$pdo->exec(file_get_contents('biblioteca.sql'));
echo "SQL executed (if tables did not exist).";
