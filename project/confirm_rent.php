<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['flat_id'], $_POST['start_date'], $_POST['end_date'])) {
    die("Invalid request. Please select a flat and rental period first.");
}

$flat_id = (int)$_POST['flat_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$stmt = $pdo->prepare("SELECT * FROM flats WHERE flat_id = :id AND status = 'approved'");
$stmt->execute([':id' => $flat_id]);
$flat = $stmt->fetch();

if (!$flat) {
    die("Invalid or unavailable flat.");
}

$start_ts = strtotime($start_date);
$end_ts = strtotime($end_date);
$available_from = strtotime($flat['available_from']);
$available_to = strtotime($flat['available_to']);

if ($end_ts < $start_ts) {
    $error = urlencode("End date must not be before start date.");
    header("Location: rent_flat.php?flat_id=$flat_id&error=$error");
    exit;
}

if ($start_ts < $available_from || $end_ts > $available_to) {
    $error = urlencode("Rental period must be within the flat availability: " . $flat['available_from'] . " to " . $flat['available_to']);
    header("Location: rent_flat.php?flat_id=$flat_id&error=$error");
    exit;
}

$days = (($end_ts - $start_ts) / (60 * 60 * 24)) + 1;
$total_cost = $days * $flat['rent_cost'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Rent</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
<section class="card">
    <h2>Confirm Your Rent</h2>
    <p><strong>Flat:</strong> <?= htmlspecialchars($flat['location']) ?>, <?= htmlspecialchars($flat['address']) ?></p>
    <p><strong>Reference #:</strong> <?= htmlspecialchars($flat['reference_number']) ?></p>
    <p><strong>Period:</strong> <?= htmlspecialchars($start_date) ?> to <?= htmlspecialchars($end_date) ?> (<?= $days ?> day<?= $days > 1 ? 's' : '' ?>)</p>
    <p><strong>Total Cost:</strong> <?= number_format($total_cost, 2) ?> NIS</p>

    <form method="post" action="process_rent.php">
        <input type="hidden" name="flat_id" value="<?= htmlspecialchars($flat_id) ?>">
        <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
        <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
        <input type="hidden" name="total_cost" value="<?= htmlspecialchars($total_cost) ?>">

        <section class="form-group">
            <label>Credit Card Number:</label>
            <input type="text" name="card_number" pattern="\d{9}" maxlength="9" class="form-control" required>
        </section>

        <section class="form-group">
            <label>Expiry Date:</label>
            <input type="month" name="card_expiry" class="form-control" required>
        </section>

        <section class="form-group">
            <label>Name on Card:</label>
            <input type="text" name="card_name" class="form-control" required>
        </section>

        <button type="submit" class="btn">Confirm Rent</button>
    </form>
</section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
