<?php
require __DIR__ . '/../public_html/connect-db.php';
try {
    $stmt = $db->query("SHOW CREATE TABLE Entry");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['Create Table'])) {
        echo $row['Create Table'] . PHP_EOL;
    } else {
        // different key name on some MySQL versions
        print_r($row);
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
