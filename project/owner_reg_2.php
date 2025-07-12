<?php
session_start();
if (!isset($_SESSION['register_role']) || $_SESSION['register_role'] !== 'owner') {
    header('Location: register.php');
    exit;
}
require_once 'database.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $error = "Username must be a valid email.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^\d.{6,13}[a-z]$/', $password)) {
        $error = "Password must start with a digit and end with a lowercase letter.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $exists = $stmt->fetchColumn();

            if ($exists > 0) {
                $error = "This username is already taken.";
            } else {
                $_SESSION['register_owner']['username'] = $username;
                $_SESSION['register_owner']['password'] = password_hash($password, PASSWORD_DEFAULT);
                header("Location: owner_reg_3.php");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
$data_owner = $_SESSION['register_owner'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customer Registration - Step 2</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Step 2: Account Setup</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post" action="owner_reg_2.php">
      <section class="form-group">
        <label for="username">Username (Email)</label>
        <input type="email" id="username" name="username" class="form-control" required
               value="<?= htmlspecialchars($data_owner['username'] ?? '') ?>"
               placeholder="Enter a valid email" />
      </section>

      <section class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control"
               pattern="^[0-9].{6,13}[a-z]$" minlength="8" maxlength="15"
               required placeholder="Start with digit, end with lowercase" />
      </section>

      <section class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control"
               pattern="^[0-9].{6,13}[a-z]$" minlength="8" maxlength="15"
               required placeholder="Re-enter your password" />
      </section>

      <button type="button" class="btn" onclick="window.location.href='owner_reg_1.php'">Back</button>
      <button type="submit" class="btn">Next</button>
    </form>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
