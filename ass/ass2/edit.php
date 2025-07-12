<?php 
require_once 'dbconfig.in.php';
require_once 'Product.php';

$product = null;
$error = '';
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :id");
    $stmt->execute([':id' => $product_id]);
    $data = $stmt->fetch();

    if ($data) {
        $product = new Product(
            $data['product_id'],
            $data['name'],
            $data['category'],
            $data['description'],
            $data['price'],
            $data['rating'],
            $data['image_name'] ?? '',
            $data['quantity']
        );
    } else {
        die("Product not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $rating = floatval($_POST['rating']);
    $quantity = intval($_POST['quantity']);

    $image_name = $product->getImageName(); 
    if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'images/';
        $uploadedFile = $_FILES['image_file']['tmp_name'];
        $newFileName = $product_id . '.jpg'; 
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($uploadedFile, $destination)) {
            $image_name = $newFileName;
        } else {
            $error = "Image upload failed.";
        }
    }

    if ($name && $category && $description && $price >= 0 && $rating >= 0 && $quantity >= 0) {
        try {
            $sql = "UPDATE products 
                    SET name = :name, category = :category, description = :description,
                        price = :price, rating = :rating, image_name = :image_name, quantity = :quantity
                    WHERE product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':category' => $category,
                ':description' => $description,
                ':price' => $price,
                ':rating' => $rating,
                ':image_name' => $image_name,
                ':quantity' => $quantity,
                ':product_id' => $product_id
            ]);
            $success = "Product updated successfully.";
        } catch (PDOException $e) {
            $error = "Error updating product: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="website icon" type="image/png" href="toqaStorelogo.png" />
</head>
<body>
<?php include 'header.php'; ?>

<main>
<blockquote>
<fieldset>
<legend><strong>Edit Product Information</strong></legend>
<h1>Product #<?php echo htmlspecialchars($product_id); ?></h1>

<?php if ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php elseif ($success): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Name:
        <input type="text" name="name" value="<?php echo htmlspecialchars($product->getName()); ?>" required>
    </label><br><br>

    <label>Category:
        <input type="text" name="category" value="<?php echo htmlspecialchars($product->getCategory()); ?>" required>
    </label><br><br>

    <label>Price:
        <input type="number" name="price" value="<?php echo htmlspecialchars($product->getPrice()); ?>" step="0.01" required>
    </label><br><br>

    <label>Rating:
        <input type="number" name="rating" value="<?php echo htmlspecialchars($product->getRating()); ?>" step="0.1" min="0" max="5" required>
    </label><br><br>

    <label>Photo:
        <input type="file" name="image_file" accept=".jpg">
    </label><br><br>

    <label>Quantity:
        <input type="number" name="quantity" value="<?php echo htmlspecialchars($product->getQuantity()); ?>" required>
    </label><br><br>

    <label>Description: <br>
        <textarea name="description" rows="8" cols="80" required><?php echo htmlspecialchars($product->getDescription()); ?></textarea>
    </label><br><br>

    <button type="submit">Update Product</button>
</form>
</fieldset>
</blockquote>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
