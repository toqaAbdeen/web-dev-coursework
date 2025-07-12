<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: login.php");
    exit;
}

$owner_id = $_SESSION['user']['user_id'] ?? $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT * FROM flats WHERE owner_id = :owner_id");
$stmt->execute([':owner_id' => $owner_id]);
$flats = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Flats | Birzeit Flat Rent</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
    <section class="card">
        <h2>🏠 My Listed Flats</h2>

        <?php if (empty($flats)): ?>
            <p>You haven't listed any flats yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Address</th>
                        <th>Bedrooms</th>
                        <th>Rent</th>
                        <th>Available</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($flats as $flat): ?>
                    <tr>
                        <td><?= htmlspecialchars($flat['location']) ?></td>
                        <td><?= htmlspecialchars($flat['address']) ?></td>
                        <td><?= $flat['bedrooms'] ?></td>
                        <td><?= number_format($flat['rent_cost'], 2) ?> NIS</td>
                        <td><?= htmlspecialchars($flat['available_from']) ?> → <?= htmlspecialchars($flat['available_to']) ?></td>
                        <td class="status-<?= $flat['status'] ?>"><?= ucfirst($flat['status']) ?></td>
                        <td><a href="flat_details.php?id=<?= $flat['flat_id'] ?>" class="btn no-underline">View</a></td>
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
