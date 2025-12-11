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
    imagecopyresampled(
        $newImg,
        $srcImg,
        0, 0, 0, 0,
        $newWidth,
        $newHeight,
        $width,
        $height
    );

    imagejpeg($newImg, $destinationPath, 90);
    imagedestroy($srcImg);
    imagedestroy($newImg);
}

$catQuery = "SELECT * FROM categories ORDER BY category_name";
$catStmt = $db->prepare($catQuery);
$catStmt->execute();
$categories = $catStmt->fetchAll();

if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

$imagePath = null;

if (!empty($_FILES['image']['name'])) {
    $filename = time() . "_" . basename($_FILES['image']['name']);
    $target = "uploads/" . $filename;

    if (getimagesize($_FILES['image']['tmp_name'])) {
        resizeImage($_FILES['image']['tmp_name'], $target, 600);
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

<nav class="p-3 bg-dark text-white">
    <a href="index.php" class="text-white me-3">Home</a>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="create.php" class="text-white me-3">Add Restaurant</a>
    <?php endif; ?>

    <span class="float-end">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="text-white">Login</a>
        <?php else: ?>
            <span class="me-2">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="text-white">Logout</a>
        <?php endif; ?>
    </span>
</nav>


<div class="container mt-4">

    <h1>Add Restaurant</h1>

    <form method="post" enctype="multipart/form-data" class="mt-3">

        <label class="mt-2">Name</label>
        <input name="name" class="form-control" required>

        <label class="mt-2">Description</label>
        <textarea name="description" class="form-control" required></textarea>

        <label class="mt-2">Address</label>
        <input name="address" class="form-control">

        <label class="mt-2">Phone Number</label>
        <input name="phone_number" class="form-control">

        <label class="mt-2">Email</label>
        <input name="email" class="form-control">

        <label class="mt-2">Website</label>
        <input name="website" class="form-control">

        <label class="mt-3">Image</label>
        <input type="file" name="image" class="form-control">

        <label class="mt-3">Category</label>
        <select name="category_id" class="form-control" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['category_id'] ?>">
                    <?= htmlspecialchars($c['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-primary mt-4">Save</button>

    </form>

</div>

</body>
</html>
