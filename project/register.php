<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register – Birzeit Flat Rent</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main>
  <section class="card">
    <h2 class="title">Create Your Account</h2>
    <p class="subtitle">Join as:</p>
    
    <section class="Reg-options">
        <a href="customer_reg_1.php?role=customer" class="reg-btn customer-btn">
       <span class="btn-icon">👤</span>
        <span>Customer</span>
        <small>Find your perfect home</small>
      </a>
      
        <a href="owner_reg_1.php?role=owner" class="reg-btn owner-btn">        
        <span class="btn-icon">🏠</span>
        <span>Owner</span>
        <small>List your property</small>
      </a>
    </section>
    
    <p class="reg-card-footer">Already registered? <a href="login.php">Sign in</a></p>
  </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>