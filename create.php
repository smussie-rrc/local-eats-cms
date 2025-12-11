<?php
require('connect.php');

function resizeImage($file, $maxWidth = 600)
{
    $source = imagecreatefromstring(file_get_contents($file));
    $width = imagesx($source);
    $height = imagesy($source);

    if ($width <= $maxWidth) {
        return $source; 
    }

    $newWidth = $maxWidth;
    $newHeight = floor($height * ($maxWidth / $width));

    $resized = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled(
        $resized, $source,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $width, $height
    );

    return $resized;
}

$catQuery = "SELECT * FROM categories ORDER BY category_name";
$catStmt = $db->prepare($catQuery);
$catStmt->execute();
$categories = $catStmt->fetchAll();

$imagePath = null;

if (!empty($_FILES['image']['name'])) {

    $filename = basename($_FILES['image']['name']);
    $target = 'uploads/' . $filename;

    if (getimagesize($_FILES['image']['tmp_name'])) {

        $resized = resizeImage($_FILES['image']['tmp_name'], 600);

        imagejpeg($resized, $target, 85);

        $imagePath = $target;
    }
}

if ($_POST) {

    $query = "INSERT INTO restaurants 
              (name, description, address, phone_number, email, website, category_id, image_url) 
              VALUES 
              (:name, :description, :address, :phone_number, :email, :website, :category_id, :image_url)";

    $stmt = $db->prepare($query);

    $stmt->bindValue(':name', $_POST['name']);
    $stmt->bindValue(':description', $_POST['description']);
    $stmt->bindValue(':address', $_POST['address']);
    $stmt->bindValue(':phone_number', $_POST['phone_number']);
    $stmt->bindValue(':email', $_POST['email']);
    $stmt->bindValue(':website', $_POST['website']);
    $stmt->bindValue(':category_id', $_POST['category_id'], PDO::PARAM_INT);
    $stmt->bindValue(':image_url', $imagePath);

    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Restaurant</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<nav>
    <a href="index.php">Home</a>
    <a href="create.php">Add Restaurant</a>
</nav>

<div class="container">

    <h1>Add Restaurant</h1>

    <form method="post" enctype="multipart/form-data">

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

        <label>Image</label>
        <input type="file" name="image">

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
