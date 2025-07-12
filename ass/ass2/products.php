<?php
require_once 'dbconfig.in.php';
require_once 'Product.php';


$name = $category = $price = "";
$products = [];

$sql = "SELECT * FROM products";
$conditions = [];
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? '';

    if (!empty($name)) {
        $conditions[] = "name LIKE :name";
        $params[':name'] = '%' . $name . '%';
    }

    if (!empty($category)) {
        $conditions[] = "category = :category";
        $params[':category'] = $category;
    }

    if (!empty($price)) {
        $conditions[] = "price <= :price";
        $params[':price'] = $price;
    }

    if ($conditions) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $product = new Product(
            $row['product_id'],
            $row['name'],
            $row['category'],
            $row['description'],
            $row['price'],
            $row['rating'],
            $row['image_name'],
            $row['quantity']
        );
        $products[] = $product;
    }

} catch (PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toqa Store Products</title>
    <link rel="website icon" type="image/png" href="toqaStorelogo.png" />
</head>
<body>

<?php include 'header.php'; ?>

<br><br>

    <main>
    <blockquote>
        <section id="product-list">
        <fieldset>
              <legend>
                <strong>Advanced Product Search</strong>
              </legend>

              <section id="search-products">
            <h2>Search Products</h2>
            <form action="products.php" method="POST">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Product Name" value="<?= htmlspecialchars($name) ?>">

            <label for="price">Price (Max)</label>
            <input type="number" name="price" placeholder="Maximum price" value="<?= htmlspecialchars($price) ?>">
            
            <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">All Categories</option>
                    <?php
                    try {
                        $catStmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
                        while ($row = $catStmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($category == $row['category']) ? 'selected' : '';
                            echo "<option value=\"" . htmlspecialchars($row['category']) . "\" $selected>" . 
                                 htmlspecialchars($row['category']) . "</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<option disabled>Error loading categories</option>";
                    }
                    ?>
                </select>

                <button type="submit">Filter</button>
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <a href="products.php" style="margin-left: 10px;">Show All</a>
                <?php endif; ?>
            </form>
        </section>
            <h2>Product List</h2>
            <table cellpadding="10" cellspacing="5" border="1">
            <caption>
           Product Table Result
          </caption>

            <tr>
              <th>Product Image</th>
              <th>Product ID</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Actions</th>
            </tr>

                <?php
                if (count($products) > 0) {
                    foreach ($products as $product) {
                        echo $product->displayInTable();
                    }
                } else {
                    echo "<tr><td colspan='9'>No products found.</td></tr>";
                }
                ?>
            </table>
              </fieldset>

        </section>
        </blockquote>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>