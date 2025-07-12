<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'] ?? $_SESSION['user']['id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid message ID.");
}

$msg_id = (int)$_GET['id'];

$sql = "SELECT m.*, 
               u_from.name AS sender_name, 
               u_to.name AS recipient_name
        FROM messages m
        JOIN users u_from ON m.sender_id = u_from.user_id
        JOIN users u_to ON m.receiver_id = u_to.user_id
        WHERE m.msg_id = :msg_id 
        AND (m.sender_id = :uid1 OR m.receiver_id = :uid2)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':msg_id' => $msg_id,
    ':uid1' => $user_id,
    ':uid2' => $user_id
]);
$message = $stmt->fetch();

if (!$message) {
    die("Message not found or access denied.");
}

if ($message['receiver_id'] == $user_id && !$message['is_read']) {
    $update = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE msg_id = :id");
    $update->execute([':id' => $msg_id]);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Message</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; include 'nav.php'; ?>

<main>
    <section class="card">
        <h2>📨 Message from <?= htmlspecialchars($message['sender_name']) ?></h2>
        <p><strong>To:</strong> <?= htmlspecialchars($message['recipient_name']) ?></p>
        <p><strong>Subject:</strong> <?= htmlspecialchars($message['subject']) ?></p>
        <p><strong>Sent:</strong> <?= htmlspecialchars($message['sent_at']) ?></p>
        <hr>
        <p><?= nl2br(htmlspecialchars($message['content'])) ?></p>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
