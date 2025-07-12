<?php
require_once 'dbconfig.in.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? '');
    $category = trim($_POST["category"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $price = floatval($_POST["price"] ?? 0);
    $rating = intval($_POST["rating"] ?? 0);
    $quantity = intval($_POST["quantity"] ?? 0);

    try {
        $sql = "INSERT INTO products (name, category, description, price, rating, quantity) 
                VALUES (:name, :category, :description, :price, :rating, :quantity)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':category' => $category,
            ':description' => $description,
            ':price' => $price,
            ':rating' => $rating,
            ':quantity' => $quantity
        ]);
        
        $product_id = $pdo->lastInsertId();
        $image_name = $product_id . '.jpg';
        $target_file = "images/" . $image_name;

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            
            if ($imageFileType != "jpg") {
                throw new Exception("Only JPG files are allowed.");
            }

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                throw new Exception("Error uploading image.");
            }
        } else {
            if (!copy("images/default.jpg", $target_file)) {
                throw new Exception("Error setting default image.");
            }
        }

        $update_sql = "UPDATE products SET image_name = :image_name WHERE product_id = :product_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([
            ':image_name' => $image_name,
            ':product_id' => $product_id
        ]);

        $message = "Product added successfully! ID: " . $product_id;

    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Product</title>
    <link rel="website icon" type="image/png" href="toqaStorelogo.png" />
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <blockquote>
            <?php if ($message): ?>
                <p><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <fieldset>
                <legend><strong>Add New Product</strong></legend>
                <form action="add.php" method="post" enctype="multipart/form-data">
                    <label for="name">Product Name:</label>
                    <input type="text" name="name" id="name" required><br><br>

                    <label for="category">Category:</label>
                    <input type="text" name="category" id="category" required><br><br>

                    <label for="description">Description:</label>
                    <textarea name="description" id="description" required></textarea><br><br>

                    <label for="price">Price:</label>
                    <input type="number" step="0.01" name="price" id="price" required><br><br>

                    <label for="rating">Rating (0-5):</label>
                    <input type="number" name="rating" id="rating" min="0" max="5" required><br><br>

                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="0" required><br><br>

                    <label for="image">Product Image (JPG only):</label>
                    <input type="file" name="image" id="image" accept=".jpg"><br><br>

                    <input type="submit" value="Add Product">
                </form>
            </fieldset>
        </blockquote>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>