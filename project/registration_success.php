<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
      <link rel="stylesheet" href="style.css" />

</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'nav.php'; ?>
    <main>
        <section class="card">
            <h2>Registration Successful</h2>
            <p>Thank you for registering! Your account has been created successfully.</p>
            <p>You can now log in using your credentials.</p>
            <button onclick="window.location.href='login.php'" class="btn">Go to Login</button>
        </section>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>