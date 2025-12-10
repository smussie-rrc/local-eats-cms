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

</body>
</html>
