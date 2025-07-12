<form action="send_message.php" method="POST">
    <label for="receiver_id">To:</label>
    <select name="receiver_id" id="receiver_id" required>
        <?php
        $users = $pdo->query("SELECT user_id, name FROM users")->fetchAll();
        foreach ($users as $user) {
            echo "<option value='{$user['user_id']}'>{$user['name']}</option>";
        }
        ?>
    </select>

    <label for="subject">Subject:</label>
    <input type="text" id="subject" name="subject" required>

    <label for="content">Message:</label>
    <textarea id="content" name="content" required></textarea>

    <button type="submit">Send</button>
</form>
