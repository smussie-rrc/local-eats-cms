<?php
require('connect.php');


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}


$query = "SELECT * FROM restaurants WHERE restaurant_id = :id";
$stmt = $db->prepare($query);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$restaurant = $stmt->fetch();


$menuQuery = "SELECT * FROM menus WHERE restaurant_id = :id";
$menuStmt = $db->prepare($menuQuery);
$menuStmt->bindValue(':id', $id, PDO::PARAM_INT);
$menuStmt->execute();
$menuItems = $menuStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($restaurant['name']) ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="create.php">Add Restaurant</a>
</nav>

<div class="container">

    <h1><?= htmlspecialchars($restaurant['name']) ?></h1>

    <p><?= nl2br(htmlspecialchars($restaurant['description'])) ?></p>

    <?php if (!empty($restaurant['image_url'])): ?>
        <img src="uploads/<?= htmlspecialchars($restaurant['image_url']) ?>"
             style="max-width: 300px; border:1px solid #ccc;">
    <?php endif; ?>

    <p>
        <strong>Address:</strong> <?= htmlspecialchars($restaurant['address']) ?><br>
        <strong>Phone:</strong> <?= htmlspecialchars($restaurant['phone_number']) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($restaurant['email']) ?><br>
        <strong>Website:</strong> <a href="<?= htmlspecialchars($restaurant['website']) ?>">
            <?= htmlspecialchars($restaurant['website']) ?>
        </a>
    </p>

    <p>
        <a href="menu_create.php?restaurant_id=<?= $id ?>">Add Menu Item</a> |
        <a href="edit.php?id=<?= $id ?>">Edit</a> |
        <a href="delete.php?id=<?= $id ?>" onclick="return confirm('Are you sure?')">Delete</a>
    </p>

    <h2>Menu Items</h2>

    <?php if ($menuItems): ?>
        <ul>
            <?php foreach ($menuItems as $m): ?>
                <li>
                    <strong><?= htmlspecialchars($m['item_name']) ?></strong>
                    — $<?= number_format($m['price'], 2) ?><br>
                    <small><?= htmlspecialchars($m['item_description']) ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No menu items yet.</p>
    <?php endif; ?>

    <p><a href="index.php">← Back to list</a></p>

</div>

</body>
</html>
