<head>
    <title>Local Eats</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
</head>

<?php
require('connect.php');


$sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$allowed_sorts = ['name', 'created_at', 'updated_at'];
if (!in_array($sort, $allowed_sorts)) {
    $sort = 'name';
}


$filter = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$sql = "SELECT * FROM restaurants WHERE 1=1";

if ($search) {
    $sql .= " AND name LIKE :search";
}

if ($filter) {
    $sql .= " AND category_id = :filter";
}

$sql .= " ORDER BY $sort";

$stmt = $db->prepare($sql);

if ($filter) {
    $stmt->bindValue(':filter', $filter, PDO::PARAM_INT);
}

if ($search) {
    $stmt->bindValue(':search', "%$search%");
}

$stmt->execute();
$restaurants = $stmt->fetchAll();

$catQuery = "SELECT * FROM categories ORDER BY category_name";
$catStmt = $db->prepare($catQuery);
$catStmt->execute();
$categories = $catStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Local Eats</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<nav>
    <a href="index.php">Home</a>
    <a href="create.php">Add Restaurant</a>
</nav>

<div class="container">

    <h1>Local Eats</h1>

    <h3>Sort By:</h3>
    <a href="index.php?sort=name">Name</a> |
    <a href="index.php?sort=created_at">Created</a> |
    <a href="index.php?sort=updated_at">Updated</a>

    <h3>Filter by Category:</h3>
    <form method="get">
        <select name="category">
            <option value="">All</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['category_id'] ?>"
                    <?= ($filter == $c['category_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="hidden" name="sort" value="<?= $sort ?>">
        <button type="submit">Apply</button>
    </form>

    <h3>Search</h3>

<form method="get">
    <input type="text" name="search" placeholder="Search restaurants..."
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <button type="submit">Search</button>

    <input type="hidden" name="category" value="<?= htmlspecialchars($_GET['category'] ?? '') ?>">
</form>


    <p><a href="create.php">Add New Restaurant</a></p>

    <ul>
    <?php foreach($restaurants as $r): ?>
        <li>
            <?= htmlspecialchars($r['name']) ?>
            | <a href="restaurant.php?id=<?= $r['restaurant_id'] ?>">View</a>
            | <a href="edit.php?id=<?= $r['restaurant_id'] ?>">Edit</a>
            | <a href="delete.php?id=<?= $r['restaurant_id'] ?>"
                onclick="return confirm('Delete this restaurant?')">
                Delete
              </a>
        </li>
    <?php endforeach; ?>
    </ul>

</div>

</body>
</html>
