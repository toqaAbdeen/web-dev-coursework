<?php
session_start();
require 'database.inc.php';
$loginError = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $sql = "SELECT * FROM users WHERE username COLLATE utf8mb4_general_ci = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if (!$user) {
            $loginError = "Username not found.";
        } else {
           
              if ($password === $user['password']) {
                $_SESSION['user'] = [
                    'user_id' => $user['user_id'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'email' => $user['email']
                ];

                header("Location: index.php");
                exit;
            } else {
                $loginError = "Password is incorrect.";
            }
        }
    } else {
        $loginError = "Please enter both username and password.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include 'header.php'; ?>
  <?php include 'nav.php'; ?>

  <main>
    <section class="card">
      <h2 class="title">Sign In</h2>
      <p class="subtitle">Enter your credentials to access your account</p>

      <?php if ($loginError): ?>
        <p class="error" style="color: red; text-align: center;">
          <?php echo htmlspecialchars($loginError); ?>
        </p>
      <?php endif; ?>

      <form action="login.php" method="post">
        <section class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
        </section>

        <section class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
        </section>

        <section class="form-options">
          <label class="remember-me">
            <input type="checkbox" name="remember"> Remember me
          </label>
        </section>

        <section class="login-footer">
          Don't have an account? <a href="register.php">Register here</a>
        </section>

        <br>
        <section style="text-align: center;">
          <button type="submit" class="btn">Sign In</button>
        </section>
      </form>
    </section>
  </main>

  <?php include 'footer.php'; ?>
</body>
</html>
