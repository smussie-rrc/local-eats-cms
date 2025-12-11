<?php
session_start();
require('connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

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
            resizeImage($_FILES['image']['tmp_name'], $target, 600);
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

    <h1>Edit Restaurant</h1>

    <form method="post" enctype="multipart/form-data">

        <label class="mt-2">Name</label>
        <input name="name" value="<?= htmlspecialchars($restaurant['name']) ?>" class="form-control" required>

        <label class="mt-2">Description</label>
        <textarea name="description" class="form-control" required><?= htmlspecialchars($restaurant['description']) ?></textarea>

        <label class="mt-2">Address</label>
        <input name="address" value="<?= htmlspecialchars($restaurant['address']) ?>" class="form-control">

        <label class="mt-2">Phone Number</label>
        <input name="phone_number" value="<?= htmlspecialchars($restaurant['phone_number']) ?>" class="form-control">

        <label class="mt-2">Email</label>
        <input name="email" value="<?= htmlspecialchars($restaurant['email']) ?>" class="form-control">

        <label class="mt-2">Website</label>
        <input name="website" value="<?= htmlspecialchars($restaurant['website']) ?>" class="form-control">

        <label class="mt-3">Category</label>
        <select name="category_id" class="form-control" required>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['category_id'] ?>"
                    <?= ($restaurant['category_id'] == $c['category_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label class="mt-3">Current Image</label><br>

        <?php if (!empty($restaurant['image_url'])): ?>
            <img src="<?= htmlspecialchars($restaurant['image_url']) ?>" style="max-width:150px; border:1px solid #ccc;"><br>
        <?php else: ?>
            <p><em>No image uploaded.</em></p>
        <?php endif; ?>

        <label class="mt-3">Upload New Image</label>
        <input type="file" name="image" class="form-control">

        <div class="form-check mt-2">
            <input type="checkbox" name="remove_image" class="form-check-input" id="removeImageCheck">
            <label class="form-check-label" for="removeImageCheck">Remove Existing Image</label>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Save Changes</button>

    </form>

    <p class="mt-3"><a href="show.php?id=<?= $id ?>">← Cancel</a></p>

</div>

</body>
</html>
