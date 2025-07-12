<?php
session_start();
require 'database.inc.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flat_id'])) {
    $flat_id = $_POST['flat_id'];
    $manager_id = $_SESSION['user']['id'];

    $sql = "UPDATE flats SET status = 'approved', approved_by = :mid, approved_date = NOW() WHERE flat_id = :fid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'mid' => $manager_id,
        'fid' => $flat_id
    ]);

    $_SESSION['success'] = "Flat #$flat_id approved successfully.";
}

header("Location: approve_flats.php");
exit;
