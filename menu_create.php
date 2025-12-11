<?php
session_start();
require('connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$restaurant_id = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);
if (!$restaurant_id) {
    header("Location: index.php");
    exit;
}

$query = "SELECT name FROM restaurants WHERE restaurant_id = :id";
$stmt = $db->prepare($query);
$stmt->bindValue(':id', $restaurant_id, PDO::PARAM_INT);
$stmt->execute();
$restaurant = $stmt->fetch();

if (!$restaurant) {
    echo "Restaurant not found.";
    exit;
}

if ($_POST) {

    $insert = "INSERT INTO menus (restaurant_id, item_name, item_description, price)
               VALUES (:restaurant_id, :item_name, :item_description, :price)";

    $stmt = $db->prepare($insert);
    $stmt->bindValue(':restaurant_id', $restaurant_id, PDO::PARAM_INT);
    $stmt->bindValue(':item_name', $_POST['item_name']);
    $stmt->bindValue(':item_description', $_POST['item_description']);
    $stmt->bindValue(':price', $_POST['price']);

    $stmt->execute();

    header("Location: show.php?id=" . $restaurant_id);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Menu Item - <?= htmlspecialchars($restaurant['name']) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<nav class="p-3 bg-dark text-white">

    <a href="index.php" class="text-white me-3">Home</a>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="create.php" class="text-white me-3">Add Restaurant</a>
    <?php endif; ?>

    <span class="float-end">

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="text-white me-3">Login</a>
            <a href="register.php" class="text-white me-3">Register</a>
        <?php else: ?>
            <span class="me-2">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="text-white">Logout</a>
        <?php endif; ?>

    </span>

</nav>

<div class="container mt-4">

    <h1>Add Menu Item</h1>
    <h3 class="text-muted"><?= htmlspecialchars($restaurant['name']) ?></h3>

    <form method="post" class="mt-3">

        <label class="mt-2">Item Name</label>
        <input type="text" name="item_name" class="form-control" required>

        <label class="mt-2">Description</label>
        <textarea name="item_description" class="form-control" required></textarea>

        <label class="mt-2">Price ($)</label>
        <input type="number" name="price" step="0.01" class="form-control" required>

        <button type="submit" class="btn btn-primary mt-4">Save Item</button>

    </form>

    <p class="mt-3">
        <a href="show.php?id=<?= $restaurant_id ?>">← Cancel</a>
    </p>

</div>

</body>
</html>
