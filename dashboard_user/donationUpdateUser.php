<?php
include '../databaseConnection.php';

$donationID = isset($_GET['donationID']) ? $_GET['donationID'] : null;
if (!$donationID) {
    die('<p>No donation selected.</p>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recurring = isset($_POST['recurring']) ? 1 : 0;
    $sql = "UPDATE donations SET recurring = '$recurring' WHERE donationID = '$donationID'";
    if ($conn->query($sql)) {
        echo "<script>alert('Recurring status updated.');window.location.href='donationreceipt.php?userID=" . $_POST['userID'] . "';</script>";
        exit;
    } else {
        echo "<p>Error updating recurring status: " . $conn->error . "</p>";
    }
}

// Fetch current status
$sql = "SELECT recurring, userID FROM donations WHERE donationID = '$donationID' LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="UTF-8">
    <title>Update Recurring Status</title>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>
    <h2>Update Recurring Status for Donation ID: <?php echo $donationID; ?></h2>
    <form method="post">
        <input type="hidden" name="donationID" value="<?php echo $donationID; ?>">
        <input type="hidden" name="userID" value="<?php echo $row['userID']; ?>">
        <label>
            <input type="checkbox" name="recurring" <?php echo ($row['recurring'] ? 'checked' : ''); ?>>
            Recurring
        </label>
        <br><br>
        <input type="submit" value="Update">
    </form>
</body>
</html>