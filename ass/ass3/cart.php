<?php
// cart.php
session_start();
require_once 'dbconfig.in.php';

// Check for product ID in POST parameters
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: products.php');
    exit;
}

$productId = (int) $_POST['id'];

// Retrieve product details using a prepared statement
$stmt = $pdo->prepare("SELECT product_id, name, price FROM products WHERE product_id = :id");
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<html>
<head><title>Product Not Found</title></head>
<body>
<h2>Product Not Found</h2>
<p>The requested product does not exist.</p>
<p><a href='products.php'>Back to Products</a></p>
</body>
</html>";
    exit;
}

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productIdStr = (string) $product['product_id'];

// Add or update item in cart
if (isset($_SESSION['cart'][$productIdStr])) {
    $_SESSION['cart'][$productIdStr]['quantity'] += 1;
} else {
    $_SESSION['cart'][$productIdStr] = [
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => 1
    ];
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Shopping Cart</title>
    <link rel="stylesheet" href="../ass3/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
<blockquote>

<center>
    <h2>Your Shopping Cart</h2>

<table width="70%" cellpadding="10" cellspacing="5" border="1">
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
    </tr>
    <?php foreach ($_SESSION['cart'] as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>$<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3"><strong>Total</strong></td>
        <td><strong>$<?= number_format($total, 2) ?></strong></td>
    </tr>
</table>
<p><a href="products.php">Continue Shopping</a></p>

</center>
    </blockquote>

</main>
<?php include 'footer.php'; ?>

</body>
</html>
