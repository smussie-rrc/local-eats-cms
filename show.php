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

<div class="container mt-4">

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

    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <p>
            <a href="menu_create.php?restaurant_id=<?= $id ?>">Add Menu Item</a> |
            <a href="edit.php?id=<?= $id ?>">Edit</a> |
            <a href="delete.php?id=<?= $id ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </p>
    <?php endif; ?>

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

    <hr>

    <h2>Comments</h2>

    <?php
    $commentQuery = "
        SELECT c.*, u.username 
        FROM comments c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.restaurant_id = :id
        ORDER BY c.created_at DESC
    ";

    $commentStmt = $db->prepare($commentQuery);
    $commentStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $commentStmt->execute();
    $comments = $commentStmt->fetchAll();
    ?>

    <?php if ($comments): ?>
        <ul class="list-group mb-4">
            <?php foreach ($comments as $c): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($c['username']) ?></strong>
                    <small class="text-muted">(<?= $c['created_at'] ?>)</small><br>
                    <?= nl2br(htmlspecialchars($c['comment_text'])) ?>

                    <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                        <a href="comment_delete.php?id=<?= $c['comment_id'] ?>&restaurant=<?= $id ?>"
                           class="text-danger float-end"
                           onclick="return confirm('Delete this comment?')">Delete</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>

    <hr>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <h3>Add a Comment</h3>

        <form method="post" action="comment_add.php">
            <input type="hidden" name="restaurant_id" value="<?= $id ?>">
            <textarea name="comment_text" class="form-control" required></textarea>
            <button class="btn btn-primary mt-2">Post Comment</button>
        </form>

    <?php else: ?>
        <p><a href="login.php">Login</a> to post a comment.</p>
    <?php endif; ?>

    <p class="mt-4"><a href="index.php">← Back to list</a></p>

</div>

</body>
</html>
