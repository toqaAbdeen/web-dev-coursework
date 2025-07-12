<?php 
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :uid");
$stmt->execute([':uid' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile | Birzeit Flat Rent</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
    <section class="card">
        <h2>👤 My Profile</h2>


        <div><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></div>

        <div><strong>Username:</strong> 
            <?= !empty($user['username']) ? htmlspecialchars($user['username']) : '—' ?>
        </div>

        <div><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></div>

        <div><strong>Role:</strong> <?= ucfirst($user['role']) ?></div>

        <div><strong>National ID:</strong> 
            <?= !empty($user['national_id']) ? htmlspecialchars($user['national_id']) : '—' ?>
        </div>

        <div><strong>Mobile:</strong> 
            <?= !empty($user['mobile']) ? htmlspecialchars($user['mobile']) : '—' ?>
        </div>

        <?php if (!empty($user['telephone'])): ?>
            <div><strong>Telephone:</strong> <?= htmlspecialchars($user['telephone']) ?></div>
        <?php endif; ?>

        <?php if (!empty($user['dob'])): ?>
            <div><strong>DOB:</strong> <?= htmlspecialchars($user['dob']) ?></div>
        <?php endif; ?>

        <div><strong>Address:</strong>
            <?php
                $addressParts = [];
                if (!empty($user['flat_no'])) $addressParts[] = $user['flat_no'];
                if (!empty($user['street_name'])) $addressParts[] = $user['street_name'];
                if (!empty($user['city'])) $addressParts[] = $user['city'];
                if (!empty($user['postal_code'])) $addressParts[] = $user['postal_code'];

                echo !empty($addressParts) ? htmlspecialchars(implode(', ', $addressParts)) : '—';
            ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>  