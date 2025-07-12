<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Offer a Flat – Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Offer a Flat for Rent</h2>
    <p>
      Thank you for choosing to list your flat on <strong>Birzeit Flat Rent</strong>. To ensure your flat reaches the right customers, we’ll guide you through a 4-step process:
    </p>

    <ol style="margin-top: 1rem; margin-bottom: 1.5rem;">
      <li><strong>Step 1:</strong> Enter your flat's details and upload at least 3 clear photos.</li>
      <li><strong>Step 2:</strong> (Optional) Add marketing information about nearby landmarks.</li>
      <li><strong>Step 3:</strong> Add available preview time slots so customers can request visits.</li>
      <li><strong>Step 4:</strong> Confirm and submit your flat for manager approval. Once approved, it will be listed publicly.</li>
    </ol>

    <p>Please make sure all the information is accurate and up to date. Only approved listings will be visible to customers.</p>

    <section style="margin-top: 2rem;">
      <a href="offer_flat_step1.php" class="btn no-underline">Start Offering</a>
    </section>
  </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
