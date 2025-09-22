<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}
// donationreceipt.php


// Fetch donation details from the database
include '../databaseConnection.php';
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
if (!$userID) {
    die('<p>❌ No user ID found. Please log in to view your reports.</p>');
}

// Use prepared statement for security
$stmt = $conn->prepare("SELECT * FROM cruelty_report WHERE userID = ? ORDER BY crueltyReportID DESC");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<th><h1>🐾 Cruelty Report Receipt 🐾</h1></th>";
        echo "<table class='donation-receipt'>";
        echo "<tr><th>Report ID</th><td>" . $row['crueltyReportID'] . "</td></tr>";
        echo "<tr><th>First Name</th><td>" . $row['fname'] . "</td></tr>";
        echo "<tr><th>Last Name</th><td>" . $row['lname'] . "</td></tr>";
        echo "<tr><th>Contact Number</th><td>" . $row['contactNumber'] . "</td></tr>";
        echo "<tr><th>Date</th><td>" . $row['createDate'] . "</td></tr>";
        echo "<tr><th>Status</th><td>" . $row['investigationStatus'] . "</td></tr>";
        echo "<tr><td colspan='2'>Thank you for your report!</td></tr>";
        echo "</table>";
    }
} else {
    echo "<p>No cruelty report records found.</p>";
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="crueltyreportreceipt.css">
    <link rel="stylesheet" type="text/css" href="../navbar functionalities/navbar.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    
    
</head>
<body>
<?php
include '../navbar functionalities/navbar.php';
?>
   
</body>
</html>