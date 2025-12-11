<?php
require('connect.php');


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo "<h1>Invalid restaurant ID.</h1>";
    exit;
}


$query = "SELECT * FROM restaurants WHERE restaurant_id = :id";
$stmt = $db->prepare($query);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$restaurant = $stmt->fetch();


if (!$restaurant) {
    echo "<h1>Restaurant not found.</h1>";
    exit;
}


$menuQuery = "SELECT * FROM menus WHERE restaurant_id = :id ORDER BY item_name";
$mStmt = $db->prepare($menuQuery);
$mStmt->bindValue(':id', $id, PDO::PARAM_INT);
$mStmt->execute();
$menuItems = $mStmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($restaurant['name']) ?></title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="container mt-4">

    <a href="index.php" class="btn btn-secondary mb-3">← Back to List</a>

    <h1><?= htmlspecialchars($restaurant['name']) ?></h1>


    <?php if (!empty($restaurant['image_url'])): ?>
        <img src="<?= htmlspecialchars($restaurant['image_url']) ?>" 
             style="max-width:300px; margin-bottom:20px;" 
             class="img-thumbnail">
    <?php endif; ?>

    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($restaurant['description'])) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($restaurant['address']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($restaurant['phone_number']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($restaurant['email']) ?></p>
    <p><strong>Website:</strong> <a href="<?= htmlspecialchars($restaurant['website']) ?>">
        <?= htmlspecialchars($restaurant['website']) ?>
    </a></p>

    <hr>

    <h2>Menu Items</h2>

    <p><a class="btn btn-primary" href="menu_create.php?restaurant_id=<?= $id ?>">Add Menu Item</a></p>

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
