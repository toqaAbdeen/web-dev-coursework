<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flat_id'])) {
    $flat_id = $_POST['flat_id'];

    $sql = "UPDATE flats SET status = 'rejected' WHERE flat_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$flat_id]);

    header("Location: index.php");
    exit;
}
?>
