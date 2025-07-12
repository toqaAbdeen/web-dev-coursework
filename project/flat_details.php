<?php
require 'database.inc.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid flat ID.");
}
$flat_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM flats WHERE flat_id = :id AND status = 'approved'");
$stmt->execute([':id' => $flat_id]);
$flat = $stmt->fetch();
if (!$flat) {
    die("Flat not found or not approved.");
}

$photosStmt = $pdo->prepare("SELECT * FROM flat_photos WHERE flat_id = :id");
$photosStmt->execute([':id' => $flat_id]);
$photos = $photosStmt->fetchAll();

$marketingStmt = $pdo->prepare("SELECT * FROM flat_marketing WHERE flat_id = :id");
$marketingStmt->execute([':id' => $flat_id]);
$marketing = $marketingStmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($flat['location']) ?> Flat | Details</title>
    <link rel="stylesheet" href="style.css" />
   
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
    <section class="card">
        <h2>Flat in <?= htmlspecialchars($flat['location']) ?></h2>

        <section class="photos">
            <?php if ($photos): ?>
                <?php foreach ($photos as $photo): ?>
                    <img src="<?= htmlspecialchars($photo['image_path']) ?>" alt="Flat photo">
                <?php endforeach; ?>
            <?php else: ?>
                <p>No photos available.</p>
            <?php endif; ?>
        </section>

        <section class="flex">
            <section class="flatcard">
                <p><strong>Address:</strong> <?= htmlspecialchars($flat['address']) ?></p>
                <p><strong>Rent:</strong> <?= htmlspecialchars($flat['rent_cost']) ?> NIS</p>
                <p><strong>Available:</strong> <?= htmlspecialchars($flat['available_from']) ?> to <?= htmlspecialchars($flat['available_to']) ?></p>
                <p><strong>Bedrooms:</strong> <?= htmlspecialchars($flat['bedrooms']) ?></p>
                <p><strong>Bathrooms:</strong> <?= htmlspecialchars($flat['bathrooms']) ?></p>
                <p><strong>Size:</strong> <?= htmlspecialchars($flat['size']) ?> m²</p>
                <p><strong>Conditions:</strong><br><?= nl2br(htmlspecialchars($flat['rent_conditions'])) ?></p>
                <p><strong>Features:</strong></p>
                <ul>
                    <li>Heating: <?= $flat['has_heating'] ? 'Yes' : 'No' ?></li>
                    <li>AC: <?= $flat['has_ac'] ? 'Yes' : 'No' ?></li>
                    <li>Access Control: <?= $flat['has_access_control'] ? 'Yes' : 'No' ?></li>
                    <li>Parking: <?= $flat['has_parking'] ? 'Yes' : 'No' ?></li>
                    <li>Playground: <?= $flat['has_playground'] ? 'Yes' : 'No' ?></li>
                    <li>Storage: <?= $flat['has_storage'] ? 'Yes' : 'No' ?></li>
                    <li>Backyard: <?= $flat['has_backyard'] ? 'Yes' : 'No' ?></li>
                </ul>

                <br>
                <a href="request_preview.php?flat_id=<?= $flat_id ?>" class="btn no-underline">Request Preview</a>
                <a href="rent_flat.php?flat_id=<?= $flat_id ?>" class="btn no-underline">Rent Now</a>
            </section>

            <aside>
                <h3>Marketing Info</h3>
                <?php if ($marketing): ?>
                    <p><strong><?= htmlspecialchars($marketing['title']) ?></strong></p>
                    <p><?= nl2br(htmlspecialchars($marketing['description'])) ?></p>
                    <?php if (!empty($marketing['link'])): ?>
                        <p><a href="<?= htmlspecialchars($marketing['link']) ?>" target="_blank">More Info</a></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No marketing content provided for this flat.</p>
                <?php endif; ?>
            </aside>
        </section>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
