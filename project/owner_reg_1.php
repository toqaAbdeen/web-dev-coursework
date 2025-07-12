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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idnumber       = trim($_POST['idnumber']);
    $email          = trim($_POST['email']);
    $account_number = trim($_POST['account_number']);

    $_SESSION['register_owner'] = array_map('trim', $_POST);
    $data_owner = $_SESSION['register_owner'];

    if (!preg_match('/^\d{9}$/', $idnumber)) {
        $error = "National ID must be exactly 9 digits.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email must be a valid format.";
    } else {
        try {
            $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM users WHERE national_id = ? OR email = ?");
            $stmt1->execute([$idnumber, $email]);
            $exists = $stmt1->fetchColumn();

            if ($exists > 0) {
                $error = "National ID or email already exists in the system.";
            } else {
                $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM owner WHERE account_number = ?");
                $stmt2->execute([$account_number]);
                $accExists = $stmt2->fetchColumn();

                if ($accExists > 0) {
                    $error = "Account number already in use.";
                } else {
                    header("Location: owner_reg_2.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['role'])) {
    $_SESSION['register_role'] = $_GET['role'];
}

$data_owner = $_SESSION['register_owner'] ?? [];
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Onwer Registration - Step 1</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Step 1: Personal Information</h2>
    <?php if (!empty($error)): ?>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php endif; ?>

    <form method="post" action="owner_reg_1.php">
      <section class="form-group">
        <label for="idnumber">National ID Number</label>
        <input type="text" id="idnumber" name="idnumber" class="form-control"
              placeholder="Enter your National ID"
               pattern="[A-Za-z0-9\-]+" required
               value="<?= htmlspecialchars($data_owner['idnumber'] ?? '') ?>" />
      </section>

      <section class="form-group">
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" class="form-control"
                placeholder="Enter your full name"
               pattern="[A-Za-z\s'\-]+" required
               value="<?= htmlspecialchars($data_owner['fullname'] ?? '') ?>" />
      </section>

      <section class="card">
        <h3>Address Information</h3>
        <section class="form-group">
          <label for="flat_no">Flat / House No:</label>
          <input type="text" id="flat_no" name="flat_no" placeholder="e.g., Flat 12B"class="form-control"  required 
           value="<?= htmlspecialchars($data_owner['flat_no'] ?? '') ?>" />
          </section>
        <section class="form-group">

          <label for="street_name">Street Name:</label>
          <input type="text" id="street_name" name="street_name" placeholder="e.g., Main Street" class="form-control"required
                     value="<?= htmlspecialchars($data_owner['street_name'] ?? '') ?>" />

        </section>
        <section class="form-group">
          <label for="city">City:</label>
          <input type="text" id="city" name="city" placeholder="e.g., Ramallah" class="form-control"required
                     value="<?= htmlspecialchars($data_owner['city'] ?? '') ?>" />

        </section>
        <section class="form-group">

          <label for="postal_code">Postal Code:</label>
          <input type="text" id="postal_code" name="postal_code" placeholder="e.g., 00970"class="form-control" required
                     value="<?= htmlspecialchars($data_owner['postal_code'] ?? '') ?>" />
          </section>

      </section>

      <section class="form-group">
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" class="form-control" required
               value="<?= htmlspecialchars($data_owner['dob'] ?? '') ?>" />
      </section>

      <section class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" 
                  placeholder="e.g., example@email.com" required
               value="<?= htmlspecialchars($data_owner['email'] ?? '') ?>" />
      </section>

      <section class="form-group">
        <label for="mobile">Mobile</label>
        <input type="tel" id="mobile" name="mobile" class="form-control"
               pattern="[0-9]{10,15}" required placeholder="e.g., 059-1234567"
               value="<?= htmlspecialchars($data_owner['mobile'] ?? '') ?>" />
      </section>

      <section class="form-group">
        <label for="telephone">Telephone</label>
        <input type="tel" id="telephone" name="telephone" class="form-control"
               pattern="[0-9]{7,15}" required placeholder="e.g., 02-1234567" 
               value="<?= htmlspecialchars($data_owner['telephone'] ?? '') ?>" />
      </section>

       <section class="card">
        <h3>Bank Information</h3>

        <section class="form-group">
          <label for="bank_name">Bank Name:</label>
          <input type="text" id="bank_name" name="bank_name" placeholder="Enter your bank name"class="form-control"  required 
           value="<?= htmlspecialchars($data_owner['bank_name'] ?? '') ?>" />
          </section>

        <section class="form-group">
          <label for="street_name">Bank Branch:</label>
          <input type="text" id="bank_branch" name="bank_branch" placeholder="Enter your bank branch" class="form-control"required
                     value="<?= htmlspecialchars($data_owner['bank_branch'] ?? '') ?>" />
        </section>


        <section class="form-group">
          <label for="account_number">Account Number:</label>
          <input type="text" id="account_number" name="account_number" placeholder="Enter your account number" class="form-control" required
                     value="<?= htmlspecialchars($data_owner['account_number'] ?? '') ?>" />
        </section>
    </section>

      <button type="submit" class="btn">Next</button>
    </form>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
