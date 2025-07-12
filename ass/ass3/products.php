<?php
require_once 'dbconfig.in.php';
require_once 'Product.php';

$name = $category = $price = "";
$products = [];
$limit = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM products";
$countSql = "SELECT COUNT(*) FROM products";
$conditions = [];
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_GET) {
    $name = $_REQUEST['name'] ?? '';
    $category = $_REQUEST['category'] ?? '';
    $price = $_REQUEST['price'] ?? '';

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
        $where = " WHERE " . implode(" AND ", $conditions);
        $sql .= $where;
        $countSql .= $where;
    }
}

try {
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalProducts = $countStmt->fetchColumn();
    $totalPages = ceil($totalProducts / $limit);

    $sql .= " LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

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
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
  <!-- SEARCH FILTER PANEL -->
  <aside>
    <h2>Search Products</h2>
    <form action="products.php" method="GET">
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
      <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
        <a href="products.php" style="margin-left: 10px;">Show All</a>
      <?php endif; ?>
    </form>
  </aside>

  <!-- PRODUCT LIST -->
  <section class="product-grid">
    <?php
    if (count($products) > 0) {
      foreach ($products as $product) {
        echo str_replace('class=\'product-name\'', 'class=\'product-name\' tabindex=\'0\'', $product->displayAsCard());
      }
    } else {
      echo "<p>No products found.</p>";
    }
    ?>
  </section>

  <!-- Pagination Bar -->
  <?php if ($totalPages > 1): ?>
  <nav class="pagination-bar">
    <form method="GET" style="display:inline;">
      <?php foreach ($_GET as $key => $val): if ($key !== 'page') echo "<input type='hidden' name='$key' value='" . htmlspecialchars($val) . "'>"; endforeach; ?>
      <input type="hidden" name="page" value="<?= $page - 1 ?>">
      <button <?= ($page <= 1 ? 'disabled' : '') ?>>Previous</button>
    </form>

    <span class="pagination-info">Page <?= $page ?> of <?= $totalPages ?></span>

    <form method="GET" style="display:inline;">
      <?php foreach ($_GET as $key => $val): if ($key !== 'page') echo "<input type='hidden' name='$key' value='" . htmlspecialchars($val) . "'>"; endforeach; ?>
      <input type="hidden" name="page" value="<?= $page + 1 ?>">
      <button <?= ($page >= $totalPages ? 'disabled' : '') ?>>Next</button>
    </form>
  </nav>
  <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

</body>
</html>