<?php
require('connect.php');

// Fetch categories for dropdown
$catQuery = "SELECT * FROM categories ORDER BY category_name";
$catStmt = $db->prepare($catQuery);
$catStmt->execute();
$categories = $catStmt->fetchAll();

// Insert restaurant on POST
if ($_POST) {

    $query = "INSERT INTO restaurants (name, description, address, phone_number, email, website, category_id) 
              VALUES (:name, :description, :address, :phone_number, :email, :website, :category_id)";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':name', $_POST['name']);
    $stmt->bindValue(':description', $_POST['description']);
    $stmt->bindValue(':address', $_POST['address']);
    $stmt->bindValue(':phone_number', $_POST['phone_number']);
    $stmt->bindValue(':email', $_POST['email']);
    $stmt->bindValue(':website', $_POST['website']);
    $stmt->bindValue(':category_id', $_POST['category_id'], PDO::PARAM_INT);

    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Restaurant</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<nav>
    <a href="index.php">Home</a>
    <a href="create.php">Add Restaurant</a>
</nav>

<div class="container">

    <h1>Add Restaurant</h1>

    <form method="post">

        <label>Name</label>
        <input name="name" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label>Address</label>
        <input name="address">

        <label>Phone Number</label>
        <input name="phone_number">

        <label>Email</label>
        <input name="email">

        <label>Website</label>
        <input name="website">

        <label>Category</label>
        <select name="category_id" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['category_id'] ?>">
                    <?= htmlspecialchars($c['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Save</button>

    </form>

</div>

</body>
</html>
