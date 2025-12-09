<?php
require('connect.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $query = "DELETE FROM restaurants WHERE restaurant_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

// Return to list after deletion
header("Location: index.php");
exit;
