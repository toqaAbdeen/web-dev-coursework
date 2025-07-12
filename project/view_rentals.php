<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['user_id'] ?? $user['user_id'];
$role = $user['role'];

$isCustomer = $role === 'customer';
$isOwner = $role === 'owner';

$sql = "SELECT r.*, f.address, f.location, f.rent_cost, u.name AS customer_name
        FROM rentals r
        JOIN flats f ON r.flat_id = f.flat_id
        JOIN users u ON r.customer_id = u.user_id
        WHERE ";
$sql .= $isCustomer ? "r.customer_id = :uid" : "f.owner_id = :uid";
$sql .= " ORDER BY r.start_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $user_id]);
$rentals = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rented Flats</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; include 'nav.php'; ?>
<main>
  <section class="card">
    <h2>🏠 <?= $isCustomer ? "Your Rentals" : "Rented Out Flats" ?></h2>
    <table>
      <thead>
        <tr>
          <th>Flat</th><th>Address</th><th>Start</th><th>End</th><th>Status</th><th>Cost</th>
          <?php if ($isOwner): ?><th>Customer</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rentals as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['location']) ?></td>
            <td><?= htmlspecialchars($r['address']) ?></td>
            <td><?= $r['start_date'] ?></td>
            <td><?= $r['end_date'] ?></td>
            <td><?= $r['status'] ?></td>
<td><?= is_numeric($r['total_cost']) ? number_format((float)$r['total_cost'], 2) . ' NIS' : '—' ?></td>
            <?php if ($isOwner): ?><td><?= htmlspecialchars($r['customer_name']) ?></td><?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
