<?php

define('DB_DSN','mysql:host=127.0.0.1;port=3307;dbname=local_eats;charset=utf8');
define('DB_USER','root');
define('DB_PASS','');

try {
    $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected OK!";
} catch (PDOException $e) {
    echo $e->getMessage();
}
