<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['user']['user_id'] ?? $_SESSION['user']['id'] ?? null;
if (!$customer_id) {
    die("Invalid session. Please login again.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_slot_id'])) {
    $slotId = $_POST['remove_slot_id'];
    $update = $pdo->prepare("UPDATE preview_slots 
                             SET is_in_basket = 0 
                             WHERE slot_id = :slot_id 
                             AND booked_by = :cid 
                             AND status = 'booked'");
    $update->execute([
        ':slot_id' => $slotId,
        ':cid' => $customer_id
    ]);
}

$sql = "SELECT ps.*, f.location, f.address, f.rent_cost 
        FROM preview_slots ps 
        JOIN flats f ON ps.flat_id = f.flat_id 
        WHERE ps.booked_by = :cid 
          AND ps.status = 'booked' 
          AND ps.is_in_basket = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':cid' => $customer_id]);
$previewFlats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Basket - Birzeit Flat Rent</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .inline-form {
            display: inline;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'nav.php'; ?>

    <main>
        <section class="card">
            <h2>🧺 My Basket (Preview Requests)</h2>

            <?php if (count($previewFlats) === 0): ?>
                <p>You haven't requested any flat previews yet (or you've removed them).</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Address</th>
                            <th>Rent</th>
                            <th>Scheduled Preview</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($previewFlats as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['location']) ?></td>
                                <td><?= htmlspecialchars($item['address']) ?></td>
                                <td><?= htmlspecialchars($item['rent_cost']) ?> NIS</td>
                                <td><?= htmlspecialchars($item['day']) ?> at <?= htmlspecialchars($item['time']) ?></td>
                                <td>
                                    <a href="rent_flat.php?flat_id=<?= $item['flat_id'] ?>" class="btn no-underline">Rent</a>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="remove_slot_id" value="<?= $item['slot_id'] ?>">
                                        <button type="submit" class="btn">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
