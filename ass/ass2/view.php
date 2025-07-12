<?php
require_once 'dbconfig.in.php';
require_once 'Product.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = $_GET['id'];

try {
    $sql = "SELECT * FROM products WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $productData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$productData) {
        die("Product not found.");
    }

    $product = new Product(
        $productData['product_id'],
        $productData['name'],
        $productData['category'],
        $productData['description'],
        $productData['price'],
        $productData['rating'],
        $productData['image_name'],
        $productData['quantity']
    );

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Product Information</title>
</head>
<body>
<?php include 'header.php'; ?>


    <?php
        echo $product->displayProductPage();
    ?>

   
<?php include 'footer.php'; ?>

</body>
</html>
