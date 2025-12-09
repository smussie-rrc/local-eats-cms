<?php
require('connect.php');

if ($_POST) {

    $query = "INSERT INTO restaurants
              (name, description, address, phone_number, email, website, category_id)
              VALUES
              (:name, :description, :address, :phone_number, :email, :website, :category_id)";

    $statement = $db->prepare($query);

    $statement->bindValue(':name', $_POST['name']);
    $statement->bindValue(':description', $_POST['description']);
    $statement->bindValue(':address', $_POST['address']);
    $statement->bindValue(':phone_number', $_POST['phone_number']);
    $statement->bindValue(':email', $_POST['email']);
    $statement->bindValue(':website', $_POST['website']);
    $statement->bindValue(':category_id', $_POST['category_id']);

    $statement->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Restaurant</title>
</head>
<body>

<h1>Add Restaurant</h1>

<form method="post">

    <label>Name</label><br>
    <input name="name" required><br><br>

    <label>Description</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Address</label><br>
    <input name="address"><br><br>

    <label>Phone</label><br>
    <input name="phone_number"><br><br>

    <label>Email</label><br>
    <input name="email"><br><br>

    <label>Website</label><br>
    <input name="website"><br><br>

    <label>Category</label><br>
    <select name="category_id">
        <?php
        $catQuery = "SELECT * FROM categories ORDER BY category_name";
        $catStmt = $db->prepare($catQuery);
        $catStmt->execute();
        $categories = $catStmt->fetchAll();

        foreach($categories as $c): ?>
            <option value="<?= $c['category_id'] ?>">
                <?= htmlspecialchars($c['category_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Save</button>

</form>

</body>
</html>
