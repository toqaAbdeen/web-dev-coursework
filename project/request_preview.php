<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['flat_id']) || !is_numeric($_GET['flat_id'])) {
    die("Invalid flat ID.");
}

$flat_id = (int)$_GET['flat_id'];
$customer_id = $_SESSION['user']['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $view_date = $_POST['view_date'] ?? '';
    $view_time = $_POST['view_time'] ?? '';
    $phone = $_POST['phone'] ?? '';

    if ($view_date && $view_time && $phone) {
        $sql = "INSERT INTO preview_slots (flat_id, day, time, phone, booked_by, status)
                VALUES (:flat_id, :day, :time, :phone, :booked_by, 'booked')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':flat_id' => $flat_id,
            ':day' => $view_date,
            ':time' => $view_time,
            ':phone' => $phone,
            ':booked_by' => $customer_id
        ]);
        $success = "Your preview has been requested!";
    } else {
        $error = "Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Request Preview</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
    <section class="card">
        <h2>Request Preview</h2>

        <?php if (!empty($success)): ?>
            <p class="success" style="color: green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="error" style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <section class="form-group">
                <label for="view_date">Preferred Date:</label>
                <input type="date" name="view_date" id="view_date"  class="form-control"required>
            </section>

            <section class="form-group">
                <label for="view_time">Preferred Time:</label>
                <input type="time" name="view_time" id="view_time"  class="form-control" required>
            </section>

            <section class="form-group">
                <label for="phone">Contact Phone:</label>
                <input type="tel" name="phone" id="phone" class="form-control" required>
            </section>

            <button type="submit" class="btn">Submit Request</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
