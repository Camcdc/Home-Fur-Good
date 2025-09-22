<?php session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
  <meta charset="UTF-8">
  <title>Adoption Application Submitted</title>
  <link rel="stylesheet" href="../css/adoptAppform.css">
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
      color: #1976d2;
      margin-bottom: 1.5rem;
    }
    .success-container a {
      display: inline-block;
      margin-top: 2rem;
      padding: 0.8rem 2rem;
      background: #3498db;
      color: #fff;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(52,152,219,0.08);
      transition: background 0.2s;
    }
    .success-container a:hover {
      background: #1565c0;
    }
  </style>
</head>
<body>
  <div class="success-container">
    <h2>Thank you for your application!</h2>
    <p>Your adoption application has been submitted. We will contact you soon.</p>
    <a href="../landing pages/browseAnimals.php">Head Back to Browse Animals</a>
  </div>
</body>
</html>
