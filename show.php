<?php
session_start();
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

if (!$restaurant) {
    echo "Restaurant not found.";
    exit;
}

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

<div class="container">

    <h1><?= htmlspecialchars($restaurant['name']) ?></h1>

    <p><?= nl2br(htmlspecialchars($restaurant['description'])) ?></p>

    <?php if (!empty($restaurant['image_url'])): ?>
        <img src="<?= htmlspecialchars($restaurant['image_url']) ?>" 
             style="max-width: 300px; border:1px solid #ccc; margin-bottom:15px;">
    <?php endif; ?>

    <p>
        <strong>Address:</strong> <?= htmlspecialchars($restaurant['address']) ?><br>
        <strong>Phone:</strong> <?= htmlspecialchars($restaurant['phone_number']) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($restaurant['email']) ?><br>
        <strong>Website:</strong> 
            <a href="<?= htmlspecialchars($restaurant['website']) ?>">
                <?= htmlspecialchars($restaurant['website']) ?>
            </a>
    </p>

    <p>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="menu_create.php?restaurant_id=<?= $id ?>">Add Menu Item</a> |
            <a href="edit.php?id=<?= $id ?>">Edit</a> |
            <a href="delete.php?id=<?= $id ?>" onclick="return confirm('Are you sure?')">Delete</a>
        <?php endif; ?>
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
