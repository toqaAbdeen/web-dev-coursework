<?php
require_once 'dbconfig.in.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = :id");
    $stmt->execute([':id' => $product_id]);

    header("Location: products.php?message=Product+deleted+successfully");
    exit();
} catch (PDOException $e) {
    echo "<p>Error deleting product: " . htmlspecialchars($e->getMessage()) . "</p>";
}
