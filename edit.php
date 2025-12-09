<?php
require('connect.php');
echo "Connected OK!<br>";


$sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING);

$allowed = ['name', 'created_at', 'updated_at'];
if (!in_array($sort, $allowed)) {
    $sort = 'name'; 
}


$query = "SELECT * FROM restaurants ORDER BY $sort";
$statement = $db->prepare($query);
$statement->execute();
$restaurants = $statement->fetchAll();
?>

<h1>Local Eats</h1>

<p>
    Sort by:
    <a href="index.php?sort=name">Name</a> |
    <a href="index.php?sort=created_at">Date Created</a> |
    <a href="index.php?sort=u
