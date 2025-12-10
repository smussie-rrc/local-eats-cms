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
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($restaurant['name']) ?></title>
</head>
<body>

<h1><?= htmlspecialchars($restaurant['name']) ?></h1>


<?php if (!empty($restaurant['image_url'])): ?>
    <img src="uploads/<?= htmlspecialchars($restaurant['image_url']) ?>" 
         alt="<?= htmlspecialchars($restaurant['name']) ?>" 
         style="max-width:300px;">
<?php endif; ?>


<p><strong>Description:</strong><br>
<?= nl2br(htmlspecialchars($restaurant['description'])) ?></p>

<p><strong>Address:</strong> <?= htmlspecialchars($restaurant['address']) ?></p>
<p><strong>Phone:</strong> <?= htmlspecialchars($restaurant['phone_number']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($restaurant['email']) ?></p>

<p><strong>Website:</strong>
    <a href="<?= htmlspecialchars($restaurant['website']) ?>">
        <?= htmlspecialchars($restaurant['website']) ?>
    </a>
</p>

<p><a href="index.php">← Back to list</a></p>

<?php

$menuQuery = "SELECT * FROM menus WHERE restaurant_id = :id";
$menuStmt = $db->prepare($menuQuery);
$menuStmt->bindValue(':id', $id, PDO::PARAM_INT);
$menuStmt->execute();
$menuItems = $menuStmt->fetchAll();
?>

<p>
    <a href="menu_create.php?restaurant_id=<?= $id ?>">Add Menu Item</a>
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


</body>
</html>
