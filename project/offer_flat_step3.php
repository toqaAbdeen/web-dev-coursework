<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['flat_offer'])) {
    header("Location: offer_flat_step1.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days = trim($_POST['viewing_days']);
    $time = trim($_POST['viewing_time']);
    $phone = trim($_POST['contact_number']);

    $_SESSION['flat_offer']['preview_slot'] = [
        'days'  => $days,
        'time'  => $time,
        'phone' => $phone
    ];

    header("Location: offer_flat_confirm.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Step 3: Viewing Timetables | Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Step 3: Add Viewing Timetables</h2>
    <form action="offer_flat_step3.php" method="post">

      <section class="form-group">
        <label for="viewing_days">Days Available for Viewing:</label>
        <input type="text" id="viewing_days" name="viewing_days" placeholder="e.g., Monday, Wednesday, Friday" class="form-control" required>
      </section>

      <section class="form-group">
        <label for="viewing_time">Time Available for Viewing:</label>
        <input type="text" id="viewing_time" name="viewing_time" placeholder="e.g., 10:00 AM - 12:00 PM" class="form-control" required>
      </section>

      <section class="form-group">
        <label for="contact_number">Contact Number:</label>
        <input type="tel" id="contact_number" name="contact_number" placeholder="Your phone number" class="form-control" required>
      </section>

      <button type="submit" class="btn">Continue to Confirmation</button>
    </form>
  </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
