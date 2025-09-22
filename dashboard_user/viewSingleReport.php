<?php
session_start();
include '../databaseConnection.php';

$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;

// Get and validate report ID
$reportID = $_GET['crueltyReportID'] ?? null;
if (!$reportID || !is_numeric($reportID)) {
    die('❌ Invalid report ID.');
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM cruelty_report WHERE crueltyReportID = ? AND userID = ?");
$stmt->bind_param("ii", $reportID, $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die('❌ Report not found or you do not have permission to view this report.');
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <title>View Cruelty Report</title>
    <link rel="stylesheet" type="text/css" href="../crueltyreport.css">
    <style>
        table { border-collapse: collapse; width: 60%; margin: 30px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: skyblue; }
        tr:nth-child(even) { background: #f9f9f9; }
        img { max-width: 200px; max-height: 200px; }
        button { padding: 8px 16px; cursor: pointer; }
             td {
    max-width: 300px; /* Adjust as needed */
    word-break: break-word;
    vertical-align: top;
}
textarea#detailedDescription {
    width: 100%;
    max-width: 280px; /* Slightly less than td */
    resize: vertical;
    overflow: auto;
    box-sizing: border-box;
}
    </style>
</head>
<body>
    <button onclick="window.location.href='CreateCrueltyReport.php';" style="margin: 20px;">&larr; Back</button>
    <h1 style="text-align:center;">Cruelty Report Details</h1>
    <table>
        <tr><th>ID</th><td><?php echo htmlspecialchars($row['crueltyReportID']); ?></td></tr>
        <tr>
            <th>Picture</th>
            <td>
                <?php 
                    if(!empty($row['picture'])) {
                        echo '<img src="../pictures/cruelty/' . htmlspecialchars($row['picture']) . '" alt="Report Image">';
                    } else {
                        echo 'No image';
                    }
                ?>
            </td>
        </tr>
        <tr><th>Detailed Description</th><td><?php echo htmlspecialchars($row['detailedDescription']); ?></td></tr>
        <tr><th>Location</th><td><?php echo htmlspecialchars($row['location']); ?></td></tr>
        <tr><th>Investigation Status</th><td><?php echo htmlspecialchars($row['investigationStatus'] ?? 'Pending'); ?></td></tr>
        <tr><th>Rescue Circumstance</th><td><?php echo htmlspecialchars($row['rescueCircumstance'] ?? 'N/A'); ?></td></tr>
        <tr><th>Inspector ID</th><td><?php echo htmlspecialchars($row['userID'] ?? 'N/A'); ?></td></tr>
    </table>
    <div style="text-align:center; margin-top:20px;">
        <a href="CreateCrueltyReport.php"><button>Create New Report</button></a>
    </div>
</body>
</html>

<?php 
$conn->close(); 
?>

