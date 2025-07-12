<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'] ?? $_SESSION['user']['id'];
$user_role = $_SESSION['user']['role'];

$sql = "SELECT m.*, 
               u_from.name AS sender_name, 
               u_to.name AS receiver_name
        FROM messages m
        JOIN users u_from ON m.sender_id = u_from.user_id
        JOIN users u_to ON m.receiver_id = u_to.user_id
        WHERE m.receiver_id = :uid1 OR m.sender_id = :uid2
        ORDER BY m.sent_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':uid1' => $user_id,
    ':uid2' => $user_id
]);

$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Messages | Birzeit Flat Rent</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
    <section class="card">
        <section >
            <h2>📬 Your Messages</h2>
            <a href="send_message.php" class="btn no-underline">➕ Compose Message</a>
        </section>

        <?php if (empty($messages)): ?>
            <p>No messages found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr class="<?= $msg['is_read'] ? '' : 'unread' ?>">
                        <td><?= htmlspecialchars($msg['sender_name']) ?></td>
                        <td><?= htmlspecialchars($msg['receiver_name']) ?></td>
                        <td>
                            <a href="view_message.php?id=<?= $msg['msg_id'] ?>" class="no-underline">
                                <?= htmlspecialchars($msg['subject']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($msg['sent_at']) ?></td>
                        <td><?= $msg['is_read'] ? 'Read' : 'Unread' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
