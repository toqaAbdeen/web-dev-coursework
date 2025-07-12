<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? 'guest';

function isActive($page) {
  return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
?>
<nav>
  <a href="index.php" class="<?= isActive('index.php') ?>">Home</a>
  <a href="search.php" class="<?= isActive('search.php') ?>">Search Flats</a>

  <?php if ($role === 'guest'): ?>
    <a href="register.php" class="<?= isActive('register.php') ?>">Register</a>
    <a href="login.php" class="<?= isActive('login.php') ?>">Login</a>
  <?php else: ?>
    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
  <?php endif; ?>

  <?php if ($role === 'customer'): ?>
    <a href="basket.php" class="<?= isActive('basket.php') ?>">Shopping Basket</a>
    <a href="view_rentals.php" class="<?= isActive('view_rentals.php') ?>">My Rentals</a>
    <a href="profile.php" class="<?= isActive('profile.php') ?>">My Profile</a>
    <a href="messages.php" class="<?= isActive('messages.php') ?>">Messages</a>
  <?php elseif ($role === 'owner'): ?>
    <a href="offer_flat.php" class="<?= isActive('offer_flat.php') ?>">Offer Flat</a>
    <a href="owner_flats.php" class="<?= isActive('owner_flats.php') ?>">My Flats</a>
    <a href="messages.php" class="<?= isActive('messages.php') ?>">Messages</a>
  <?php elseif ($role === 'manager'): ?>
    <a href="approve_flats.php" class="<?= isActive('approve_flats.php') ?>">Approve Flats</a>
    <a href="inquiries.php" class="<?= isActive('inquiries.php') ?>">Flat Inquiry</a>
    <a href="messages.php" class="<?= isActive('messages.php') ?>">Messages</a>
  <?php endif; ?>

  <a href="about_us.php" class="<?= isActive('about_us.php') ?>">About Us</a>
</nav>
