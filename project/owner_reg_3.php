<?php
session_start();

if (!isset($_SESSION['register_role']) || $_SESSION['register_role'] !== 'owner') {
    header('Location: register.php');
    exit;
}

require_once 'database.inc.php';

$data_owner = $_SESSION['register_owner'] ?? [];

$idnumber       = trim($data_owner['idnumber'] ?? '');
$fullname       = trim($data_owner['fullname'] ?? '');
$flat_no        = trim($data_owner['flat_no'] ?? '');
$street_name    = trim($data_owner['street_name'] ?? '');
$city           = trim($data_owner['city'] ?? '');
$postal_code    = trim($data_owner['postal_code'] ?? '');
$dob            = $data_owner['dob'] ?? '';
$email          = trim($data_owner['email'] ?? '');
$mobile         = trim($data_owner['mobile'] ?? '');
$telephone      = trim($data_owner['telephone'] ?? '');
$username       = trim($data_owner['username'] ?? '');
$password_raw   = $data_owner['password'] ?? '';
$bank_name      = trim($data_owner['bank_name'] ?? '');
$bank_branch    = trim($data_owner['bank_branch'] ?? '');
$account_number = trim($data_owner['account_number'] ?? '');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($idnumber) || empty($fullname) || empty($flat_no) || empty($street_name) || empty($city) ||
        empty($postal_code) || empty($dob) || empty($email) || empty($mobile) || empty($telephone) ||
        empty($username) || empty($password_raw) || empty($bank_name) || empty($bank_branch) || empty($account_number)
    ) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            $pdo->beginTransaction();

            $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (role, national_id, name, flat_no, street_name, city, postal_code, dob, email, mobile, telephone, username, password)
                    VALUES (:role, :national_id, :name, :flat_no, :street_name, :city, :postal_code, :dob, :email, :mobile, :telephone, :username, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':role' => 'owner',
                ':national_id' => $idnumber,
                ':name' => $fullname,
                ':flat_no' => $flat_no,
                ':street_name' => $street_name,
                ':city' => $city,
                ':postal_code' => $postal_code,
                ':dob' => $dob,
                ':email' => $email,
                ':mobile' => $mobile,
                ':telephone' => $telephone,
                ':username' => $username,
                ':password' => $password_hash,
            ]);

            $owner_id = $pdo->lastInsertId();

            $sql_owner = "INSERT INTO owner (owner_id, bank_name, bank_branch, account_number)
                          VALUES (:owner_id, :bank_name, :bank_branch, :account_number)";
            $stmt_owner = $pdo->prepare($sql_owner);
            $stmt_owner->execute([
                ':owner_id' => $owner_id,
                ':bank_name' => $bank_name,
                ':bank_branch' => $bank_branch,
                ':account_number' => $account_number
            ]);

            $pdo->commit();

            unset($_SESSION['register_owner']);
            header('Location: registration_success.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Owner Registration - Step 3</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Step 3: Review and Confirm</h2>
    <?php if ($error): ?>
      <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="owner_reg_3.php">
      <p><strong>ID Number:</strong> <?= htmlspecialchars($idnumber) ?></p>
      <p><strong>Full Name:</strong> <?= htmlspecialchars($fullname) ?></p>
      <p><strong>Flat/House No:</strong> <?= htmlspecialchars($flat_no) ?></p>
      <p><strong>Street Name:</strong> <?= htmlspecialchars($street_name) ?></p>
      <p><strong>City:</strong> <?= htmlspecialchars($city) ?></p>
      <p><strong>Postal Code:</strong> <?= htmlspecialchars($postal_code) ?></p>
      <p><strong>Date of Birth:</strong> <?= htmlspecialchars($dob) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
      <p><strong>Mobile:</strong> <?= htmlspecialchars($mobile) ?></p>
      <p><strong>Telephone:</strong> <?= htmlspecialchars($telephone) ?></p>
      <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
      <p><strong>Bank Name:</strong> <?= htmlspecialchars($bank_name) ?></p>
      <p><strong>Bank Branch:</strong> <?= htmlspecialchars($bank_branch) ?></p>
      <p><strong>Account Number:</strong> <?= htmlspecialchars($account_number) ?></p>

      <button type="button" class="btn" onclick="window.location.href='owner_reg_2.php'">Back</button>
      <button type="submit" class="btn">Confirm & Submit</button>
    </form>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
