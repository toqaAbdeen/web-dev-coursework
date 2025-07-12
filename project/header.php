<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? 'guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <section class="logo-section">
    <img src="logo.png" alt="Birzeit Flat Rent Logo" class="logo">
    <h1>Birzeit Flat Rent</h1>
  </section>

  <section class="user-greeting">
    <?php if ($user): ?>
      Welcome, <strong><?= htmlspecialchars($user['name']) ?></strong>!
    <?php else: ?>
      Welcome, Guest
    <?php endif; ?>
  </section>
</header>
