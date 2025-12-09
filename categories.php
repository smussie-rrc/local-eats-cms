<?php
require('connect.php');

// Insert new category if form submitted
if ($_POST && isset($_POST['category_name'])) {
    $query = "INSERT INTO categories (category_name) VALUES (:name)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':name', $_POST['category_name']);
    $stmt->execute();
    header("Location: categories.php");
    exit;
}

// Get existing categories
$query = "SELECT * FROM categories ORDER BY category_name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<h1>Categories</h1>

<form method="post">
    <input name="category_name" placeholder="New category" required>
    <button type="submit">Add</button>
</form>

<ul>
<?php foreach ($categories as $c): ?>
    <li><?= htmlspecialchars($c['category_name']) ?></li>
<?php endforeach; ?>
</ul>

<a href="index.php">Back to Restaurants</a>
