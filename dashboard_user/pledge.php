<?php

session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}
include '../databaseconnection.php';

// Get userID from query string or session
$userID = isset($_GET['userID']) ? $_GET['userID'] : (isset($_SESSION['userID']) ? $_SESSION['userID'] : null);
$fname = isset($_SESSION['Fname']) ? $_SESSION['Fname'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <button onclick="window.location.href='donations.php?userID=<?php echo urlencode($userID); ?>'">Back to Donation</button>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pledge</title>
    <link rel="stylesheet" type="text/css" href="pledge.css">
    <link rel="stylesheet" type="text/css" href="../navbar functionalities/navbar.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>
    <div class="pledge-content">
        <img src="../pictures/logo/Log.jpg" type="image/jpeg">
        <h1>PLEDGE TO GIVE</h1>
        <p>I recognise that I can use part of my income to do a significant amount of good. Since I can live well enough on a smaller income,
            I pledge that from now until I decide to stop,
            I shall give <span id="pledge-amount">[Your chosen amount]</span> to the SPCA organization in order to improve the lives of animals. I make this pledge freely, openly, and sincerely 
        </p>
        <h4>Name:<?php echo htmlspecialchars($fname); ?> <br>UserID:<?php echo htmlspecialchars($userID); ?></h4>
    </div>
</body>
</html>