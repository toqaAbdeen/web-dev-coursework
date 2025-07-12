<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['flat_offer'])) {
    header("Location: offer_flat_step1.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['skip'])) {
        $_SESSION['flat_offer']['marketing'] = [
            'title' => '',
            'description' => '',
            'url' => ''
        ];
        header("Location: offer_flat_step3.php");
        exit;
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $url = trim($_POST['url'] ?? '');

    $_SESSION['flat_offer']['marketing'] = [
        'title' => $title,
        'description' => $description,
        'url' => $url
    ];

    header("Location: offer_flat_step3.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Step 2: Marketing Info | Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2>Step 2: Add Marketing Information</h2>
    <form action="offer_flat_step2.php" method="post">

      <section class="form-group">
        <label for="title">Marketing Title</label>
        <input type="text" name="title" id="title" class="form-control" placeholder="Enter marketing title" >
      </section>

      <section class="form-group">
        <label for="description">Short Description</label>
        <textarea name="description" id="description" class="form-control" placeholder="Enter short description" ></textarea>
      </section>

      <section class="form-group">
        <label for="url">URL Link (if available)</label>
        <input type="url" name="url" id="url" class="form-control" placeholder="Enter URL link (optional)">
      </section>

      <section class="form-group">
        <button type="submit" name="submit" value="submit" class="btn no-underline">Submit Marketing Info</button>
        <button type="submit" name="skip" value="skip" class="btn no-underline">Skip Marketing & Continue</button>
      </section>

    </form>
  </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
