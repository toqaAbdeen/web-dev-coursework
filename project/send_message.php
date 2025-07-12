<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['user_id'] ?? $user['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['receiver_id'] ?? '';
    $subject = trim($_POST['subject'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (!$to || !$subject || !$content) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, subject, content, is_read, sent_at)
                               VALUES (:from, :to, :subject, :content, 0, NOW())");
        $stmt->execute([
            ':from' => $user_id,
            ':to' => $to,
            ':subject' => $subject,
            ':content' => $content
        ]);
        $success = "Message sent successfully.";
    }
}

$users = $pdo->query("SELECT user_id, name, role FROM users WHERE user_id != $user_id")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Send Message</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; include 'nav.php'; ?>

<main>
    <section class="card">
        <h2>Send Message</h2>
        <?php if ($success): ?>
            <p style="color: green"><?= $success ?></p>
        <?php elseif ($error): ?>
            <p style="color: red"><?= $error ?></p>
        <?php endif; ?>

        <form method="post" action="send_message.php">
            <section class="form-group">
                <label for="receiver_id">Recipient:</label>
                <select name="receiver_id" class="form-control" required>
                    <option value="">Select a user</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['user_id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= $u['role'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </section>

            <section class="form-group">
                <label>Subject:</label>
                <input type="text" name="subject" class="form-control" required>
            </section>

            <section class="form-group">
                <label>Message:</label>
                <textarea name="content" rows="6" class="form-control" required></textarea>
            </section>

            <button type="submit" class="btn">Send Message</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
