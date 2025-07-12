<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

$customer = $_SESSION['user'];

if (!isset($_GET['flat_id']) || !is_numeric($_GET['flat_id'])) {
    die("Invalid flat ID.");
}
$flat_id = (int)$_GET['flat_id'];

$stmt = $pdo->prepare("SELECT f.*, u.name AS owner_name, u.user_id AS owner_id, 
       CONCAT(u.flat_no, ', ', u.street_name, ', ', u.city, ' ', u.postal_code) AS owner_address,
       u.mobile AS owner_phone
FROM flats f 
JOIN users u ON f.owner_id = u.user_id
WHERE f.flat_id = :id AND f.status = 'approved'");
$stmt->execute([':id' => $flat_id]);
$flat = $stmt->fetch();

if (!$flat) {
    die("Flat not found or unavailable.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rent Flat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; include 'nav.php'; ?>
<main>
    <?php if (isset($_GET['error'])): ?>
        <section style="color: red;">
            <?= htmlspecialchars($_GET['error']) ?>
        </section>
    <?php endif; ?>

    <section class="card">
        <h2>Flat in <?= htmlspecialchars($flat['location']) ?></h2>

    <h2>Rent Flat</h2>
    <form method="post" action="confirm_rent.php">
        <input type="hidden" name="flat_id" value="<?= $flat_id ?>">

        <p><strong>Reference #:</strong> <?= $flat['flat_id'] ?></p>
        <p><strong>Reference Number:</strong> <?= htmlspecialchars($flat['reference_number']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($flat['location']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($flat['address']) ?></p>
        <p><strong>Bedrooms:</strong> <?= htmlspecialchars($flat['bedrooms']) ?></p>
        <p><strong>Bathrooms:</strong> <?= htmlspecialchars($flat['bathrooms']) ?></p>
        <p><strong>Size:</strong> <?= htmlspecialchars($flat['size']) ?> m²</p>
        <p><strong>Rent Cost:</strong> <?= htmlspecialchars($flat['rent_cost']) ?> NIS</p>
        <p><strong>Available:</strong> <?= htmlspecialchars($flat['available_from']) ?> to <?= htmlspecialchars($flat['available_to']) ?></p>
        <p><strong>Owner:</strong> <?= htmlspecialchars($flat['owner_name']) ?> (ID: <?= $flat['owner_id'] ?>)</p>
        <p><strong>Owner Address:</strong> <?= htmlspecialchars($flat['owner_address']) ?></p>

        <label>Start Date: </label>
        <input type="date" name="start_date" class="form-control" required><br>
        <label>End Date: </label>
        <input type="date" name="end_date" class="form-control"required><br>

        <button type="submit" class="btn">Continue to Payment</button>
    </form>
</section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
