<?php require('connect.php'); ?>

<h1>Local Eats</h1>

<p><a href="create.php">Add New Restaurant</a></p>

<?php
$query = "SELECT * FROM restaurants ORDER BY name";
$statement = $db->prepare($query);
$statement->execute();
$restaurants = $statement->fetchAll();
?>

<ul>
<?php foreach($restaurants as $r): ?>
    <li>
        <?= htmlspecialchars($r['name']) ?>

        | <a href="show.php?id=<?= $r['restaurant_id'] ?>">View</a>
        | <a href="edit.php?id=<?= $r['restaurant_id'] ?>">Edit</a>
        | <a href="delete.php?id=<?= $r['restaurant_id'] ?>"
             onclick="return confirm('Delete this restaurant?')">
            Delete
          </a>
    </li>
<?php endforeach; ?>
</ul>
