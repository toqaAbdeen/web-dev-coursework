<?php
session_start();
require 'database.inc.php'; 

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['flat_offer'])) {
    header("Location: offer_flat_step1.php");
    exit;
}

$flat = $_SESSION['flat_offer'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $reference_number = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("INSERT INTO flats (
            owner_id, reference_number, location, address, rent_cost, available_from, available_to,
            bedrooms, bathrooms, size, rent_conditions, has_heating, has_ac, has_access_control,
            has_parking, has_backyard, has_playground, has_storage, status
        ) VALUES (
            :owner_id, :reference_number, :location, :address, :rent_cost, :available_from, :available_to,
            :bedrooms, :bathrooms, :size, :rent_conditions, :has_heating, :has_ac, :has_access_control,
            :has_parking, :has_backyard, :has_playground, :has_storage, 'pending'
        )");

        $stmt->execute([
            'owner_id' => $_SESSION['user']['user_id'],
            'reference_number' => $reference_number,
            'location' => $flat['location'],
            'address' => $flat['address'],
            'rent_cost' => $flat['rent_cost'],
            'available_from' => $flat['available_from'],
            'available_to' => $flat['available_to'],
            'bedrooms' => $flat['bedrooms'],
            'bathrooms' => $flat['bathrooms'],
            'size' => $flat['size'],
            'rent_conditions' => $flat['rent_conditions'],
            'has_heating' => $flat['has_heating'],
            'has_ac' => $flat['has_ac'],
            'has_access_control' => $flat['has_access_control'],
            'has_parking' => $flat['has_parking'],
            'has_backyard' => $flat['has_backyard'],
            'has_playground' => $flat['has_playground'],
            'has_storage' => $flat['has_storage']
        ]);

        $flat_id = $pdo->lastInsertId();

        $stmtPhoto = $pdo->prepare("INSERT INTO flat_photos (flat_id, image_path) VALUES (:flat_id, :image_path)");
        foreach ($flat['photos'] as $path) {
            $stmtPhoto->execute([
                'flat_id' => $flat_id,
                'image_path' => $path
            ]);
        }

        if (isset($flat['marketing'])) {
            $stmtMarketing = $pdo->prepare("INSERT INTO flat_marketing (flat_id, title, description, link)
                VALUES (:flat_id, :title, :description, :link)");
            $stmtMarketing->execute([
                'flat_id' => $flat_id,
                'title' => $flat['marketing']['title'],
                'description' => $flat['marketing']['description'],
                'link' => $flat['marketing']['url']
            ]);
        }

        if (isset($flat['preview_slot'])) {
            $stmtSlot = $pdo->prepare("INSERT INTO preview_slots (flat_id, day, time, phone)
                VALUES (:flat_id, :day, :time, :phone)");
            $stmtSlot->execute([
                'flat_id' => $flat_id,
                'day' => $flat['preview_slot']['days'],  
                'time' => $flat['preview_slot']['time'], 
                'phone' => $flat['preview_slot']['phone']
            ]);
        }

        $pdo->commit();

        unset($_SESSION['flat_offer']);
        $_SESSION['success'] = "Flat submitted for approval. Reference #: $reference_number";
        header("Location: offer_flat_success.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Database Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirm Flat Offer</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Confirm Flat Offer</h2>
    <ul>
      <li><strong>Location:</strong> <?= htmlspecialchars($flat['location']) ?></li>
      <li><strong>Address:</strong> <?= htmlspecialchars($flat['address']) ?></li>
      <li><strong>Rent:</strong> <?= htmlspecialchars($flat['rent_cost']) ?> NIS</li>
      <li><strong>Bedrooms:</strong> <?= $flat['bedrooms'] ?> | Bathrooms: <?= $flat['bathrooms'] ?> | Size: <?= $flat['size'] ?> sqm</li>
      <li><strong>Available:</strong> <?= $flat['available_from'] ?> to <?= $flat['available_to'] ?></li>
      <li><strong>Backyard:</strong> <?= $flat['has_backyard'] ?></li>
    </ul>

    <form method="post">
      <button type="submit" class="btn btn-primary">Submit for Approval</button>
    </form>
  </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
