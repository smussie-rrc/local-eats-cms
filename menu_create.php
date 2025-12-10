<head>
    <title>Local Eats</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
</head>

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

<!DOCTYPE html>
<html>
<head>
    <title>Add Menu Item</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<nav>
    <a href="index.php">Home</a>
    <a href="create.php">Add Restaurant</a>
</nav>

<div class="container">

    <h1>Add Menu Item</h1>

    <form method="post">

        <label>Item Name</label>
        <input name="item_name" required>

        <label>Description</label>
        <textarea name="item_description"></textarea>

        <label>Price</label>
        <input name="price" type="number" step="0.01" required>

        <button type="submit">Save</button>
    </form>

    <p><a href="show.php?id=<?= $restaurant_id ?>">← Back to Restaurant</a></p>

</div>

</body>
</html>
