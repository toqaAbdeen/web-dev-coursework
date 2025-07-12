<?php
require 'database.inc.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Available Flats | Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Search Flats</h2>
    <form method="GET" action="search.php" class="form-inline">
      <label for="location">Location:</label>
      <input type="text" id="location" name="location" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>" class="form-control">

      <label for="min_price">Min Price:</label>
      <input type="number" id="min_price" name="min_price" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" class="form-control">

      <label for="max_price">Max Price:</label>
      <input type="number" id="max_price" name="max_price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" class="form-control">

      <label for="bedrooms">Bedrooms:</label>
      <input type="number" id="bedrooms" name="bedrooms" value="<?= htmlspecialchars($_GET['bedrooms'] ?? '') ?>" class="form-control">

      <label for="rental_start">Rental Start Date:</label>
      <input type="date" id="rental_start" name="rental_start" value="<?= htmlspecialchars($_GET['rental_start'] ?? '') ?>" class="form-control">

      <label for="rental_end">Rental End Date:</label>
      <input type="date" id="rental_end" name="rental_end" value="<?= htmlspecialchars($_GET['rental_end'] ?? '') ?>" class="form-control">

      <br><br>
      <button type="submit" class="btn">Filter</button>
    </form>
  </section>

  <section class="card">
    <h3>Available Flats</h3>

    <?php
    $query = "SELECT * FROM flats WHERE status = 'approved'";
    $params = [];

    if (!empty($_GET['location'])) {
      $query .= " AND location LIKE :location";
      $params[':location'] = '%' . $_GET['location'] . '%';
    }

    if (!empty($_GET['min_price'])) {
      $query .= " AND rent_cost >= :min_price";
      $params[':min_price'] = $_GET['min_price'];
    }

    if (!empty($_GET['max_price'])) {
      $query .= " AND rent_cost <= :max_price";
      $params[':max_price'] = $_GET['max_price'];
    }

    if (!empty($_GET['bedrooms'])) {
      $query .= " AND bedrooms = :bedrooms";
      $params[':bedrooms'] = $_GET['bedrooms'];
    }

    if (!empty($_GET['rental_start'])) {
      $query .= " AND available_from <= :rental_start";
      $params[':rental_start'] = $_GET['rental_start'];
    }

    if (!empty($_GET['rental_end'])) {
      $query .= " AND available_to >= :rental_end";
      $params[':rental_end'] = $_GET['rental_end'];
    }

    if (!empty($_GET['rental_start']) && !empty($_GET['rental_end'])) {
      $query .= " AND flat_id NOT IN (
        SELECT flat_id FROM rentals
        WHERE status = 'confirmed'
        AND (
          (start_date <= :rental_end_conflict AND end_date >= :rental_start_conflict)
        )
      )";
      $params[':rental_start_conflict'] = $_GET['rental_start'];
      $params[':rental_end_conflict'] = $_GET['rental_end'];
    }

    try {
      $stmt = $pdo->prepare($query);
      $stmt->execute($params);

      if ($stmt->rowCount() > 0) {
        echo "<table>";
        echo "<thead><tr><th>Location</th><th>Address</th><th>Rent</th><th>Bedrooms</th><th>Size</th><th>Action</th></tr></thead><tbody>";
        while ($row = $stmt->fetch()) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row['location']) . "</td>";
          echo "<td>" . htmlspecialchars($row['address']) . "</td>";
          echo "<td>" . htmlspecialchars($row['rent_cost']) . " JOD</td>";
          echo "<td>" . htmlspecialchars($row['bedrooms']) . "</td>";
          echo "<td>" . htmlspecialchars($row['size']) . " m²</td>";
          echo "<td><a href='flat_details.php?id=" . $row['flat_id'] . "' class='btn no-underline'>View</a></td>";
          echo "</tr>";
        }
        echo "</tbody></table>";
      } else {
        echo "<p>No flats found with the selected criteria.</p>";
      }

    } catch (PDOException $e) {
      echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
    ?>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
