<?php
session_start();
$message = $_SESSION['success'] ?? "No recent flat offer.";
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Flat Offered | Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Flat Offered Successfully 🎉</h2>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="index.php" class="btn no-underline">Back to Home</a>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
