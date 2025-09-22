<?php session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
  <meta charset="UTF-8">
  <title>No Adoption Application Found</title>
  <link rel="stylesheet" href="../navbar functionalities/navbar.css">
  <style>
    .success-container {
      max-width: 600px;
      margin: 4rem auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 16px rgba(79,195,247,0.12);
      padding: 2.5rem 2rem;
      text-align: center;
    }
    .success-container h2 {
      color: #3a6fa0;
      margin-bottom: 1.5rem;
    }
    .success-container a {
      display: inline-block;
      margin-top: 2rem;
      padding: 0.8rem 2rem;
      background: #3a6fa0;
      color: #fff;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(52,152,219,0.08);
      transition: background 0.2s;
    }
    .success-container a:hover {
      background:rgb(51, 99, 145);
    }
  </style>
</head>
<body>
  <?php
  include '../navbar functionalities/navbar.php';
  ?>
  <div class="success-container">
    <h2>No Adoption Application Found</h2>
    <p>You have not submitted any adoption applications yet.</p>
    <a href="../landing pages/browseAnimals.php">Browse Animals</a>
  </div>
</body>
</html>
