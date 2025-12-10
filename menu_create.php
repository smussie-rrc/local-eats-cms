<?php
require('connect.php');

$restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);

if (!$restaurant_id) {
    header("Location: index.php");
    exit;
}

if ($_POST) {
    $query = "INSERT INTO menus (restaurant_id, item_name, item_description, price)
              VALUES (:restaurant_id, :item_name, :item_description, :price)";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':restaurant_id', $restaurant_id, PDO::PARAM_INT);
    $stmt->bindValue(':item_name', $_POST['item_name']);
    $stmt->bindValue(':item_description', $_POST['item_description']);
    $stmt->bindValue(':price', $_POST['price']);

    $stmt->execute();

    header("Location: show.php?id=$restaurant_id");
    exit;
}
?>

<h1>Add Menu Item</h1>

<form method="post">
    <label>Name</label><br>
    <input name="item_name" required><br><br>

    <label>Description</label><br>
    <textarea name="item_description"></textarea><br><br>

    <label>Price</label><br>
    <input name="price" type="number" step="0.01" required><br><br>

    <button type="submit">Save Item</button>
</form>
