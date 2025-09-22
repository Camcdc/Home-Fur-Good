<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseConnection.php';

$Report_ID = $_GET['crueltyReportID'] ?? null;

if (!$Report_ID) {
    die('❌ No report ID provided.');
}

$sql_select = "SELECT * FROM cruelty_report WHERE crueltyReportID = '$Report_ID'";
$result = $conn->query($sql_select);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die('❌ Report not found.');
}
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="UTF-8">
    <title>View Cruelty Report</title>
    <link rel="stylesheet" href="viewFullCrueltyReport.css">
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
</head>
<body>
    <div style="max-width:900px;margin:0 auto;">
        <button onclick="window.history.back();" class="btn btn-back">&larr; Back</button>
    </div>

    <h1>Cruelty Report Details</h1>

    <div class="report-card">
        <table>
            <tr><th>ID</th><td><?php echo htmlspecialchars($row['crueltyReportID']); ?></td></tr>
            <tr>
                <th>Picture</th>
                <td>
                    <?php 
                        if (!empty($row['picture'])) {
                            echo '<img src="../pictures/cruelty/' . htmlspecialchars($row['picture']) . '" alt="Report Image">';
                        } else {
                            echo 'No image';
                        }
                    ?>
                </td>
            </tr>
            <tr><th>First Name</th><td><?php echo htmlspecialchars($row['fname']); ?></td></tr>
            <tr><th>Last Name</th><td><?php echo htmlspecialchars($row['lname']); ?></td></tr>
            <tr><th>Contact Number</th><td><?php echo htmlspecialchars($row['contactNumber']); ?></td></tr>
            <tr>
                <th>Detailed Description</th>
                <td>
                    <div class="description-box">
                        <?php echo nl2br(htmlspecialchars($row['detailedDescription'])); ?>
                    </div>
                </td>
            </tr>
            <tr><th>Location</th><td><?php echo htmlspecialchars($row['location']); ?></td></tr>
            <tr><th>Investigation Status</th><td><?php echo htmlspecialchars($row['investigationStatus'] ?? 'ongoing investigation'); ?></td></tr>
            <tr><th>Rescue Circumstance</th><td><?php echo htmlspecialchars($row['rescueCircumstance'] ?? 'N/A'); ?></td></tr>
            <tr><th>Inspector ID</th><td><?php echo htmlspecialchars($row['userID'] ?? 'N/A'); ?></td></tr>
        </table>
    </div>

    
</body>
</html>

<?php $conn->close(); ?>
