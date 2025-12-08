<?php
require __DIR__ . '/../public_html/connect-db.php';
require __DIR__ . '/../public_html/entry-db.php';
// Use an existing user id (pick 1)
$user_id = 1;
$res = addEntry('TEST STATUS ENTRY', 5, $user_id, 'in progress', 'Testing status in progress');
if ($res) echo "Inserted with id: $res\n";
else echo "Insert failed. Check error log.\n";
