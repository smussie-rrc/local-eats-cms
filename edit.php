<?php
require('connect.php');

// Validate id
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch the restaurant
$query = "SELECT * FROM restaurants WHERE restaurant_id = :id";
$stmt = $db->prepare($query);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$restaurant = $stmt->fetch();

// If the form is submitted
if ($_POST) {

    $update = "UPDATE restaurants
               SET name = :name,
                   description = :description,
                   address = :address,
                   phone_number = :phone_number,
                   email = :email,
                   website = :website
               WHERE restaurant_id = :id";

    $stmt = $db->prepare($update);
    $stmt->bindValue(':name', $_POST['name']);
    $stmt->bindValue(':description', $_POST['description']);
    $stmt->bindValue(':address', $_POST['address']);
    $stmt->bindValue(':phone_number', $_POST['phone_number']);
    $stmt->bindValue(':email', $_POST['email']);
    $stmt->bindValue(':website', $_POST['website']);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: show.php?id=$id");
    exit;
}
?>

<form method="post">
    <label>Name</label>
    <input name="name" value="<?= htmlspecialchars($restaurant['name']) ?>">

    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($restaurant['description']) ?></textarea>

    <label>Address</label>
    <input name="address" value="<?= htmlspecialchars($restaurant['address']) ?>">

    <label>Phone</label>
    <input name="phone_number" value="<?= htmlspecialchars($restaurant['phone_number']) ?>">

    <label>Email</label>
    <input name="email" value="<?= htmlspecialchars($restaurant['email']) ?>">

    <label>Website</label>
    <input name="website" value="<?= htmlspecialchars($restaurant['website']) ?>">

    <button type="submit">Save</button>
</form>
