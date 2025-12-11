<?php
session_start();
require('connect.php');

$errors = [];

if ($_POST) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    $check = $db->prepare("SELECT * FROM users WHERE username = :username");
    $check->bindValue(':username', $username);
    $check->execute();

    if ($check->fetch()) {
        $errors[] = "This username is already taken.";
    }

    if (empty($errors)) {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, password, is_admin)
                  VALUES (:username, :password, 0)";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $hashed);
        $stmt->execute();

        header("Location: login.php?registered=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<nav class="p-3 bg-dark text-white">

    <a href="index.php" class="text-white me-3">Home</a>

    <span class="float-end">
        <a href="login.php" class="text-white me-3">Login</a>
        <a href="register.php" class="text-white me-3">Register</a>
    </span>

</nav>


<div class="container mt-4">
    <h1>Create an Account</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="mt-3">

        <label class="mt-2">Username</label>
        <input type="text" name="username" class="form-control" required>

        <label class="mt-3">Password</label>
        <input type="password" name="password" class="form-control" required>

        <label class="mt-3">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>

        <button type="submit" class="btn btn-primary mt-4">Register</button>

    </form>

    <p class="mt-3">
        Already have an account?
        <a href="login.php">Login here</a>
    </p>

</div>

</body>
</html>
