<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Step 1: Flat Details | Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Step 1: Enter Flat Details</h2>
    <?php if (isset($_SESSION['upload_error'])): ?>
        <p style="color: red; ">
        <?= $_SESSION['upload_error']; ?>
        </p>
    <?php unset($_SESSION['upload_error']); ?>
    <?php endif; ?>

    <form action="offer_flat_step1_process.php" method="post" enctype="multipart/form-data">

      <section class="form-group">
        <label for="location">City/Location</label>
        <input type="text" name="location" id="location" class="form-control" placeholder="Enter flat location" required>
      </section>

      <section class="form-group">
        <label for="address">Full Address</label>
        <input type="text" name="address" id="address" class="form-control"  placeholder="Enter flat address"required>
      </section>

      <section class="form-group">
        <label for="rent_cost">Monthly Rent Cost</label>
        <input type="number" step="0.01" name="rent_cost" id="rent_cost" class="form-control" placeholder="Enter rent cost" required>
      </section>

      <section class="form-group">
        <label for="available_from">Available From</label>
        <input type="date" name="available_from" id="available_from" class="form-control" required>
      </section>

      <section class="form-group">
        <label for="available_to">Available To</label>
        <input type="date" name="available_to" id="available_to" class="form-control" required>
      </section>

      <section class="form-group">
        <label for="bedrooms">Bedrooms</label>
        <input type="number" name="bedrooms" id="bedrooms" class="form-control" placeholder="Enter flat Bedrooms number" required>
      </section>

      <section class="form-group">
        <label for="bathrooms">Bathrooms</label>
        <input type="number" name="bathrooms" id="bathrooms" class="form-control" placeholder="Enter flat bathrooms number" required>
      </section>

      <section class="form-group">
        <label for="size">Size (m²)</label>
       <input type="number" step="0.1" name="size" id="size" class="form-control" placeholder="Enter flat size in square meters" required>
      </section>

      <section class="form-group">
        <label for="rent_conditions">Rental Conditions</label>
        <textarea name="rent_conditions" id="rent_conditions" class="form-control" required></textarea>
      </section>

      <section class="form-group">
        <label>Features</label><br>
        <input type="checkbox" name="has_heating" value="1"> Heating<br>
        <input type="checkbox" name="has_ac" value="1"> Air Conditioning<br>
        <input type="checkbox" name="has_access_control" value="1"> Access Control<br>
        <input type="checkbox" name="has_parking" value="1"> Parking<br>
        <input type="checkbox" name="has_playground" value="1"> Playground<br>
        <input type="checkbox" name="has_storage" value="1"> Storage<br>
      </section>

      <section class="form-group">
        <label for="has_backyard">Backyard</label>
        <select name="has_backyard" class="form-control" required>
          <option value="none">None</option>
          <option value="insectionidual">Insectionidual</option>
          <option value="shared">Shared</option>
        </select>
      </section>

      <section class="form-group">
        <label for="photos">Upload at least 3 photos</label>
        <input type="file" name="photos[]" id="photos" class="form-control" accept="image/*" multiple required>
      </section>

      <section class="form-group">
        <button type="submit" class="btn">Continue to Step 2</button>
      </section>
    </form>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
