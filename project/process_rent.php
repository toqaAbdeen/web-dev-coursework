<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$customer = $_SESSION['user'];
$customer_id = $customer['user_id'];

$flat_id = $_POST['flat_id'] ?? null;
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$total_cost = $_POST['total_cost'] ?? null;
$card_number = $_POST['card_number'] ?? null;
$card_name = $_POST['card_name'] ?? null;
$card_expiry = $_POST['card_expiry'] ?? null;

if (!$flat_id || !$start_date || !$end_date || !$total_cost || !$card_number || !$card_name || !$card_expiry) {
    die("Missing rental or payment details.");
}

if (!preg_match('/^\d{9}$/', $card_number)) {
    die("Invalid card number. Must be exactly 9 digits.");
}

$stmt = $pdo->prepare("
    SELECT f.*, u.name AS owner_name, u.mobile AS owner_phone
    FROM flats f
    JOIN users u ON f.owner_id = u.user_id
    WHERE f.flat_id = :id
");
$stmt->execute([':id' => $flat_id]);
$flat = $stmt->fetch();

if (!$flat) {
    die("Invalid flat selected.");
}

$conflict = $pdo->prepare("
    SELECT * FROM rentals 
    WHERE flat_id = :flat_id 
    AND status = 'confirmed'
    AND (
        (start_date <= :end_date AND end_date >= :start_date)
    )
");
$conflict->execute([
    ':flat_id' => $flat_id,
    ':start_date' => $start_date,
    ':end_date' => $end_date
]);

if ($conflict->rowCount() > 0) {
    die("This flat is already rented during the selected period.");
}

$insert = $pdo->prepare("
    INSERT INTO rentals (customer_id, flat_id, start_date, end_date, total_cost, status)
    VALUES (:customer_id, :flat_id, :start_date, :end_date, :total_cost, 'confirmed')
");
$insert->execute([
    ':customer_id' => $customer_id,
    ':flat_id' => $flat_id,
    ':start_date' => $start_date,
    ':end_date' => $end_date,
    ':total_cost' => $total_cost
]);

$rental_id = $pdo->lastInsertId();

$payment = $pdo->prepare("
    INSERT INTO payments (rental_id, card_number, card_holder_name, card_expiry)
    VALUES (:rental_id, :card_number, :card_holder_name, :card_expiry)
");
$payment->execute([
    ':rental_id' => $rental_id,
    ':card_number' => $card_number,
    ':card_holder_name' => $card_name,
    ':card_expiry' => $card_expiry
]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rent Confirmation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; include 'nav.php'; ?>
<main>
    <section class="card">
        <h2>Flat Rented Successfully!</h2>
        <p>Thank you, <?= htmlspecialchars($customer['name']) ?>. Your rental has been confirmed.</p>
        <p><strong>You can collect the key from:</strong></p>
        <p><?= htmlspecialchars($flat['owner_name']) ?> - Phone: <?= htmlspecialchars($flat['owner_phone']) ?></p>
        <p>Your payment has also been processed successfully.</p>
    </section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
