<?php
session_start();
require('connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {

    $select = "SELECT image_url FROM restaurants WHERE restaurant_id = :id";
    $stmt = $db->prepare($select);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $restaurant = $stmt->fetch();

    if ($restaurant) {

        if (!empty($restaurant['image_url']) && file_exists($restaurant['image_url'])) {
            unlink($restaurant['image_url']);
        }

        $delete = "DELETE FROM restaurants WHERE restaurant_id = :id";
        $stmt = $db->prepare($delete);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

header("Location: index.php");
exit;
