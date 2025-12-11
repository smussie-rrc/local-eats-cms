<?php
require('connect.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$query = "SELECT image_url FROM restaurants WHERE restaurant_id = :id";
$stmt = $db->prepare($query);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$restaurant = $stmt->fetch();

if ($restaurant && !empty($restaurant['image_url'])) {
    if (file_exists($restaurant['image_url'])) {
        unlink($restaurant['image_url']); 
    }
}

$menuDelete = "DELETE FROM menus WHERE restaurant_id = :id";
$stmtMenu = $db->prepare($menuDelete);
$stmtMenu->bindValue(':id', $id, PDO::PARAM_INT);
$stmtMenu->execute();

$delete = "DELETE FROM restaurants WHERE restaurant_id = :id";
$stmtDelete = $db->prepare($delete);
$stmtDelete->bindValue(':id', $id, PDO::PARAM_INT);
$stmtDelete->execute();

header("Location: index.php");
exit;
?>
