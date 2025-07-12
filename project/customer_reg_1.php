<?php
session_start();
if (isset($_GET['role'])) {
    $_SESSION['register_role'] = $_GET['role'];
}

if (!isset($_SESSION['register_role'])) {
    header('Location: register.php');
    exit;
}
require_once 'database.inc.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idnumber = trim($_POST['idnumber'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!preg_match('/^\d{9}$/', $idnumber)) {
        $error = "National ID must be exactly 9 digits.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email must be a valid format.";
    } else {
        try {
            $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM users WHERE national_id = ? OR email = ?");
            $stmt1->execute([$idnumber, $email]);
            $exists = $stmt1->fetchColumn();

            if ($exists > 0) {
                $error = "National ID or email already exists in the system.";
            } else {
                $_SESSION['register_customer'] = array_map('trim', $_POST);
                header("Location: customer_reg_2.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['role'])) {
    $_SESSION['register_role'] = $_GET['role'];
}

$data = $_SESSION['register_customer'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Registration - Step 1</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Step 1: Personal Information</h2>

    <?php if (!empty($error)): ?>
      <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="customer_reg_1.php">
      <section class="form-group">
        <label for="idnumber">National ID Number</label>
        <input type="text" id="idnumber" name="idnumber" class="form-control"
               placeholder="Enter your National ID"
               pattern="\d{9}" required
               value="<?= htmlspecialchars($data['idnumber'] ?? '') ?>" />
      </section>

      <section class="form-group">
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" class="form-control"
               placeholder="Enter your full name"
               pattern="[A-Za-z\s'\-]+" required
               value="<?= htmlspecialchars($data['fullname'] ?? '') ?>" />
      </section>

      <section class="card">
        <h3>Address Information</h3>
        <section class="form-group">
          <label for="flat_no">Flat / House No:</label>
          <input type="text" id="flat_no" name="flat_no" placeholder="e.g., Flat 12B" class="form-control" required 
                 value="<?= htmlspecialchars($data['flat_no'] ?? '') ?>" />
        </section>
        <section class="form-group">
          <label for="street_name">Street Name:</label>
          <input type="text" id="street_name" name="street_name" placeholder="e.g., Main Street" class="form-control" required
                 value="<?= htmlspecialchars($data['street_name'] ?? '') ?>" />
        </section>
        <section class="form-group">
          <label for="city">City:</label>
          <input type="text" id="city" name="city" placeholder="e.g., Ramallah" class="form-control" required
                 value="<?= htmlspecialchars($data['city'] ?? '') ?>" />
        </section>
        <section class="form-group">
          <label for="postal_code">Postal Code:</label>
          <input type="text" id="postal_code" name="postal_code" placeholder="e.g., 00970" class="form-control" required
                 value="<?= htmlspecialchars($data['postal_code'] ?? '') ?>" />
        </section>
      </section>

      <section class="form-group">
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" class="form-control" required
               value="<?= htmlspecialchars($data['dob'] ?? '') ?>" />
      </section>

      <section class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" 
               placeholder="e.g., example@email.com" required
               value="<?= htmlspecialchars($data['email'] ?? '') ?>" />
      </section>

      <section class="form-group">
        <label for="mobile">Mobile</label>
        <input type="tel" id="mobile" name="mobile" class="form-control"
               pattern="[0-9]{10,15}" required placeholder="e.g., 0591234567"
               value="<?= htmlspecialchars($data['mobile'] ?? '') ?>" />
      </section>

      <section class="form-group">
        <label for="telephone">Telephone</label>
        <input type="tel" id="telephone" name="telephone" class="form-control"
               pattern="[0-9]{7,15}" required placeholder="e.g., 021234567"
               value="<?= htmlspecialchars($data['telephone'] ?? '') ?>" />
      </section>

      <button type="submit" class="btn">Next</button>
    </form>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
