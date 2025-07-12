<?php
session_start();
require_once 'database.inc.php';

$data = $_SESSION['register_customer'] ?? [];

$idnumber   = $data['idnumber'] ?? '';
$fullname   = $data['fullname'] ?? '';
$flat_no    = $data['flat_no'] ?? '';
$street_name    = $data['street_name'] ?? '';
$city    = $data['city'] ?? '';
$postal_code    = $data['postal_code'] ?? '';

$dob        = $data['dob'] ?? '';
$email      = $data['email'] ?? '';
$mobile     = $data['mobile'] ?? '';
$telephone  = $data['telephone'] ?? '';
$username   = $data['username'] ?? '';
$password_hash = $data['password'] ?? '';


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("SELECT national_id, email, username FROM users 
                               WHERE national_id = :nid OR email = :email OR username = :username");
        $stmt->execute([
            ':nid' => $idnumber,
            ':email' => $email,
            ':username' => $username
        ]);

        $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($duplicates) {
            foreach ($duplicates as $row) {
                if ($row['national_id'] === $idnumber) {
                    $error = "National ID already exists.";
                    break;
                } elseif ($row['email'] === $email) {
                    $error = "Email already exists.";
                    break;
                } elseif ($row['username'] === $username) {
                    $error = "Username already exists.";
                    break;
                }
            }
        } else {
           $sql = "INSERT INTO users (role, national_id, name, flat_no, street_name, city, postal_code, dob, email, mobile, telephone, username, password)
            VALUES (:role, :national_id, :name, :flat_no, :street_name, :city, :postal_code, :dob, :email, :mobile, :telephone, :username, :password)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':role' => 'customer',
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

            unset($_SESSION['register_customer']);
            header('Location: registration_success.php');
            exit;
        }

    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customer Registration - Step 3</title>
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

    <form method="post" action="customer_reg_3.php">
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

      <button type="button" class="btn" onclick="window.location.href='customer_reg_2.php'">Back</button>
      <button type="submit" class="btn">Confirm & Submit</button>
    </form>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
