<?php
 session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
// donattionupdaterecord.php
include '../databaseConnection.php';

// Fetch donation record from database if donationID is provided via GET or POST
$donationID = isset($_GET['donationID']) ? $_GET['donationID'] : (isset($_POST['donationID']) ? $_POST['donationID'] : null);
$amount = $date = $recurring = $userID = $causeType = $status = '';
if ($donationID) {
    $sql = "SELECT * FROM donations WHERE donationID='$donationID' LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $amount = $row['amount'];
        $date = $row['date'];
        $recurring = $row['recurring'];
        $userID = $row['userID'];
        $causeType = $row['causeType'];
        $status = $row['status'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $donationID) {
    // Get the form data
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $recurring = isset($_POST['recurring']) ? 1 : 0;
    $userID = $_POST['userID'];
    $causeType = $_POST['causeType'];
    $status = $_POST['status'];

    // Update the donation record in the database
    $sql = "UPDATE donations SET amount='$amount', date='$date', recurring='$recurring', userID='$userID', causeType='$causeType', status='$status' WHERE donationID='$donationID'";
    if ($conn->query($sql) === TRUE) {
        echo"<script>alert('✅Donation record updated successfully.');</script>";
    } else {
        echo "<script>alert('❌Error updating record: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Donation Record</title>
    <link rel="stylesheet" type="text/css" href="donationupdaterecord.css">
    <link rel="stylesheet" type="text/css" href="sidebar_admin.css">
  
</head>
<body>
<?php
include 'sidebar_admin.php';
?>
<div class="entire-container">
    

    <div class="content-container">
        <div class="back-container">
        <button onclick="window.location.href='displayAllDonations.php';" class="back-button">Back to All Donations</button>
    </div>
    <div class="update-container">
        <h1>Update Donation Record</h1>
        <form method="POST" action="">
            <input type="hidden" name="donationID" value="<?php echo $donationID; ?>" readonly>
            <label for="amount">Amount:</label>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="font-weight:600;color:#27ae60;">R</span>
                <input type="number" name="amount" value="<?php echo $amount; ?>" required readonly>
            </div>
            <label for="date">Date:</label>
            <input type="date" name="date" value="<?php echo $date; ?>" required>
            <label for="recurring">Recurring:</label>
            <input type="checkbox" name="recurring" <?php echo $recurring ? 'checked' : '' ?> readonly disabled>
            <label for="userID">User ID:</label>
            <input type="text" name="userID" value="<?php echo $userID; ?>" required readonly>
            <label for="causeType">Cause Type:</label>
            <input type="text" name="causeType" value="<?php echo $causeType; ?>" required readonly>
            <label for="status">Status:</label>
            <select name="status" required>
                <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="confirmed" <?php echo ($status === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                <option value="cancelled" <?php echo ($status === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <input type="submit" onclick="return confirm('Are you sure you want to update this record?');" value="Update">
        </form>
    </div>
    </div>
</div>
</body>
</html>