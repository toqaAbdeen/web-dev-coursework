<?php
session_start();
require 'database.inc.php'; 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: login.php");
    exit;
}
$sql = "SELECT f.flat_id, f.address, u.name AS owner_name, f.status, 
               MIN(fp.image_path) AS image_path
        FROM flats f
        JOIN users u ON f.owner_id = u.user_id
        LEFT JOIN flat_photos fp ON f.flat_id = fp.flat_id
        WHERE f.status = 'pending'
        GROUP BY f.flat_id, f.address, u.name, f.status";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$pending_flats = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
      <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'nav.php'; ?>
    <main>
        <section class="card">
            <h2>Pending Flats for Approval</h2>
            <?php if (empty($pending_flats)): ?>
                <p>No pending flats to approve.</p>
            <?php else: ?>
                <table class="flat-table">
                    <thead>
                        <tr>
                            <th>Address</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Photos</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_flats as $flat): ?>
                            <tr>
                                <td><?= htmlspecialchars($flat['address']) ?></td>
                                <td><?= htmlspecialchars($flat['owner_name']) ?></td>
                                <td><?= htmlspecialchars($flat['status']) ?></td>
                                <td>
                                    <?php
                                    $photos_sql = "SELECT image_path FROM flat_photos WHERE flat_id = ?";
                                    $photos_stmt = $pdo->prepare($photos_sql);
                                    $photos_stmt->execute([$flat['flat_id']]);
                                    $photos = $photos_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (count($photos) > 0): ?>
                                        <section class="photo-gallery">
                                            <?php foreach ($photos as $photo): ?>
                                                <a href="<?= htmlspecialchars($photo['image_path']) ?>" target="_blank">
                                                    <img src="<?= htmlspecialchars($photo['image_path']) ?>" 
                                                         alt="Flat Photo" 
                                                         class="flat-photo"
                                                         title="Click to view full size">
                                                </a>
                                            <?php endforeach; ?>
                                        </section>
                                    <?php else: ?>
                                        No Photos
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form action="approve_flat_confirm.php" method="post" style="display:inline-block;">
                                        <input type="hidden" name="flat_id" value="<?= $flat['flat_id'] ?>">
                                        <button type="submit" class="btn approve-btn">Approve</button>
                                    </form>
                                    <br>                                    <br>

                                    <form action="reject_flat_confirm.php" method="post" style="display:inline-block;">
                                        <input type="hidden" name="flat_id" value="<?= $flat['flat_id'] ?>">
                                        <button type="submit" class="btn reject-btn" onclick="return confirm('Are you sure you want to reject this flat?');">Reject</button>
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