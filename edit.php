<head>
    <title>Local Eats</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
</head>

<?php
require('connect.php');

function resizeImage($sourcePath, $destinationPath, $maxWidth = 600)
{
    list($width, $height, $type) = getimagesize($sourcePath);

    if ($width <= $maxWidth) {
        move_uploaded_file($sourcePath, $destinationPath);
        return;
    }

    $scale = $maxWidth / $width;
    $newWidth = $maxWidth;
    $newHeight = floor($height * $scale);

    $srcImg = null;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $srcImg = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $srcImg = imagecreatefrompng($sourcePath);
            break;
        default:
            return;
    }

    $newImg = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImg, $srcImg, 0, 0, 0, 0,
                       $newWidth, $newHeight, $width, $height);

    imagejpeg($newImg, $destinationPath, 90);
    imagedestroy($srcImg);
    imagedestroy($newImg);
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$query = "SELECT * FROM restaurants WHERE restaurant_id = :id";
$stmt = $db->prepare($query);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$restaurant = $stmt->fetch();

$catQuery = "SELECT * FROM categories ORDER BY category_name";
$catStmt = $db->prepare($catQuery);
$catStmt->execute();
$categories = $catStmt->fetchAll();

if ($_POST) {

    $removeImage = isset($_POST['remove_image']);

    $newImagePath = $restaurant['image_url'];

    if (!empty($_FILES['image']['name'])) {

        if (!empty($restaurant['image_url']) && file_exists($restaurant['image_url'])) {
            unlink($restaurant['image_url']);
        }

        $filename = time() . "_" . basename($_FILES['image']['name']);
        $target = "uploads/" . $filename;

        if (getimagesize($_FILES['image']['tmp_name'])) {
            resizeImage($_FILES['image']['tmp_name'], $target);
            $newImagePath = $target;
        }
    }

    if ($removeImage) {
        if (!empty($restaurant['image_url']) && file_exists($restaurant['image_url'])) {
            unlink($restaurant['image_url']);
        }
        $newImagePath = null;
    }

    $update = "UPDATE restaurants
               SET name = :name,
                   description = :description,
                   address = :address,
                   phone_number = :phone_number,
                   email = :email,
                   website = :website,
                   category_id = :category_id,
                   image_url = :image_url
               WHERE restaurant_id = :id";

    $stmt = $db->prepare($update);
    $stmt->bindValue(':name', $_POST['name']);
    $stmt->bindValue(':description', $_POST['description']);
    $stmt->bindValue(':address', $_POST['address']);
    $stmt->bindValue(':phone_number', $_POST['phone_number']);
    $stmt->bindValue(':email', $_POST['email']);
    $stmt->bindValue(':website', $_POST['website']);
    $stmt->bindValue(':category_id', $_POST['category_id'], PDO::PARAM_INT);
    $stmt->bindValue(':image_url', $newImagePath);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: show.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Restaurant</title>
    <link rel="stylesheet" href="css/styles.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<nav>
    <a href="index.php">Home</a>
    <a href="create.php">Add Restaurant</a>
</nav>

<div class="container">

    <h1>Edit Restaurant</h1>

    <form method="post" enctype="multipart/form-data">

        <label>Name</label>
        <input name="name" value="<?= htmlspecialchars($restaurant['name']) ?>" required>

        <label>Description</label>
        <textarea name="description" required><?= htmlspecialchars($restaurant['description']) ?></textarea>

        <label>Address</label>
        <input name="address" value="<?= htmlspecialchars($restaurant['address']) ?>">

        <label>Phone Number</label>
        <input name="phone_number" value="<?= htmlspecialchars($restaurant['phone_number']) ?>">

        <label>Email</label>
        <input name="email" value="<?= htmlspecialchars($restaurant['email']) ?>">

        <label>Website</label>
        <input name="website" value="<?= htmlspecialchars($restaurant['website']) ?>">

        <label>Category</label>
        <select name="category_id" required>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['category_id'] ?>"
                    <?= ($restaurant['category_id'] == $c['category_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>New Image</label>
        <input type="file" name="image">

        <?php if (!empty($restaurant['image_url'])): ?>
            <p>Current Image:</p>
            <img src="<?= $restaurant['image_url'] ?>" style="max-width:150px; border:1px solid gray;">
            <br>
            <label><input type="checkbox" name="remove_image"> Remove current image</label>
        <?php endif; ?>

        <br><br>
        <button type="submit">Save Changes</button>

    </form>

    <p><a href="show.php?id=<?= $id ?>">← Cancel</a></p>

</div>

</body>
</html>

