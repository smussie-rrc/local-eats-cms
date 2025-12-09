<?php
require('connect.php');

// Make sure we have a valid ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

// Load restaurant
$query = "SELECT * FROM restaurants WHERE restaurant_id = :id";
$stmt = $db->prepare($query);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$restaurant = $stmt->fetch();

if (!$restaurant) {
    echo "<p>Restaurant not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($restaurant['name']) ?></title>
</head>
<body>

<h1><?= htmlspecialchars($restaurant['name']) ?></h1>

<p><strong>Description:</strong><br>
<?= nl2br(htmlspecialchars($restaurant['description'])) ?></p>

<p><strong>Address:</strong> <?= htmlspecialchars($restaurant['address']) ?></p>
<p><strong>Phone:</strong> <?= htmlspecialchars($restaurant['phone_number']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($restaurant['email']) ?></p>

<?php if (!empty($restaurant['website'])): ?>
<p><strong>Website:</strong>
    <a href="<?= htmlspecialchars($restaurant['website']) ?>" target="_blank">
        <?= htmlspecialchars($restaurant['website']) ?>
    </a>
</p>
<?php endif; ?>

<p><a href="index.php">⬅ Back to list</a></p>

</body>
</html>
