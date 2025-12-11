<?php
session_start();
require('connect.php');

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_POST) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {

        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin']; 

        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<div class="container mt-5" style="max-width: 450px;">

    <h1>Login</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">

        <label>Username</label>
        <input type="text" name="username" class="form-control" required>

        <label>Password</label>
        <input type="password" name="password" class="form-control" required>

        <button class="btn btn-primary mt-3" type="submit">Login</button>
    </form>

    <p class="mt-3">
        <a href="register.php">Need an account? Register here.</a>
    </p>

</div>

</body>
</html>
