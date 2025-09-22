<?php
// donationreceipt.php

// Include FPDF library
require('fpdf.php');  // Ensure the correct path to fpdf.php

// Fetch donation details from the database
include '../databaseConnection.php';

// Get userID from GET parameter
$userID = filter_input(INPUT_GET, 'userID', FILTER_VALIDATE_INT);
if (!$userID) {
    die('<p>❌ No valid user ID provided for receipt.</p>');
}
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'User') {
    // Redirect to homepage if not logged in or not a user
    header("Location: ../landing pages/homepage.php");
    exit;
}

// PDF download logic FIRST, before any output
if (isset($_GET['download_pdf']) && isset($_GET['donationID'])) {
    $donationID = filter_input(INPUT_GET, 'donationID', FILTER_VALIDATE_INT);
    if ($donationID) {
        $stmt = $conn->prepare("SELECT * FROM donations WHERE donationID = ? AND userID = ?");
        $stmt->bind_param("ii", $donationID, $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(40, 10, 'Donation Receipt');
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, "Donation ID: " . $row['donationID'], 0, 1);
            $pdf->Cell(0, 10, "Amount: R" . $row['amount'], 0, 1);
            $pdf->Cell(0, 10, "Date: " . $row['date'], 0, 1);
            $pdf->Cell(0, 10, "Type: " . ($row['recurring'] ? "Recurring" : "One-time"), 0, 1);
            $pdf->Cell(0, 10, "Cause: " . $row['causeType'], 0, 1);
            $pdf->Cell(0, 10, "Status: " . $row['status'], 0, 1);
            $pdf->Ln(10);
            $pdf->Cell(0, 10, 'Thank you for your generous donation!', 0, 1);
            $pdf->Output('D', 'DonationReceipt.pdf'); // Forces download
            exit;
        }
    }
}

// Use prepared statements to avoid SQL injection
$stmt = $conn->prepare("SELECT * FROM donations WHERE userID = ? ORDER BY donationID DESC");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Donation Receipt</h1>";
echo "<button onclick='window.location.href=\"donations.php\"'>Back</button>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Display donation details
        echo "<div class='donation'>";
        echo "<p><strong>Donation ID:</strong> " . $row['donationID'] . "</p>";
        echo "<p><strong>Amount:</strong> R" . $row['amount'] . "</p>";
        echo "<p><strong>Date:</strong> " . $row['date'] . "</p>";
        echo "<p><strong>Type:</strong> " . ($row['recurring'] ? "Recurring" : "One-time") . "</p>";
        echo "<p><strong>Cause:</strong> " . $row['causeType'] . "</p>";
        echo "<p><strong>Status:</strong> " . $row['status'] . "</p>";
        echo "<p>Thank you for your generous donation!</p>";

        // Update recurring button
        $recurringText = $row['recurring'] ? "Recurring" : "One-time";
        $toggleText = $row['recurring'] ? "Mark as One-time" : "Mark as Recurring";
        echo "<form action='donationUpdateUser.php' method='get' style='margin-top:10px;'>";
        echo "<input type='hidden' name='donationID' value='" . $row['donationID'] . "'>";
        echo "<input type='submit' value='$toggleText'>";
        echo "</form>";

        // Download receipt as PDF button
        echo "<form action='' method='get' style='margin-top:10px;'>";
        echo "<input type='hidden' name='userID' value='" . $userID . "'>";
        echo "<input type='hidden' name='donationID' value='" . $row['donationID'] . "'>";
        echo "<input type='hidden' name='download_pdf' value='1'>";
        echo "<input type='submit' value='Download Receipt as PDF'>";
        echo "</form>";

        echo "</div>";
    }
} else {
    echo "<p>No donation records found.</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Receipt</title>
    <link rel="stylesheet" type="text/css" href="donationreceipt.css">
    <link rel="stylesheet" type="text/css" href="../navbar functionalities/navbar.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>
<?php include '../navbar functionalities/navbar.php'?>
</body>
</html>
