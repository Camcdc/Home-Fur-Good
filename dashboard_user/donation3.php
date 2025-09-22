<?php
include '../databaseConnection.php/';

// Fetch userID from the database or set it dynamically
$userResult = $conn->query("SELECT userID FROM user LIMIT 1");  // Adjust the table name if necessary
if(!$userResult) {
    die("❌ Unable to fetch userID. Error: " . $conn->error);
}
$userRow = $userResult->fetch_assoc();
$userID = $userRow['userID'];  // Assuming you want the first userID

// Process the donation form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donationAmount = $_POST["donationAmount"];
    $donationDate = $_POST["donationDate"];
    $donationType = $_POST["donationType"];
    $causeType = $_POST["causeType"];
    
    // Map donationType to an integer value for the recurring column
    $recurring = ($donationType === "Recurring") ? 1 : 0;  // 1 for Recurring, 0 for One-time

    // Insert donation into the database
    $sqli = "INSERT INTO donations (amount, date, recurring, userID, causeType) 
             VALUES ('$donationAmount', '$donationDate', '$recurring', '$userID', '$causeType')";
    
    $result = $conn->query($sqli);

    if ($result === FALSE) {
        die("<script>alert('❌ Unable to create the donation record. Error: " . $conn->error . "');</script>");
    } else {
        echo "<script>alert('✅ The donation record was successfully created.');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations</title>
    <link rel="stylesheet" type="text/css" href="donations.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>
    <h1>Make a Donation</h1>
    <div class="donation-form"> 
        <form action="donations.php" method="POST">
            <table>
                <tr>
                    <td><label for="userID">User ID:</label></td>
                    <td><input type="text" id="userID" name="userID" value="<?php echo $userID; ?>" readonly required></td>
                </tr>
                <tr>
                    <td><label for="donationAmount">Donation Amount: In Rands(R)</label></td>
                    <td><input type="number" id="donationAmount" name="donationAmount" step="0.01" required></td>
                </tr>
                <tr>
                    <td><label for="donationDate">Donation Date:</label></td>
                    <td><input type="date" id="donationDate" name="donationDate" required></td>
                </tr>
                <tr>
                    <td><label for="donationType">Donation Type:</label></td>
                    <td>
                        <select id="donationType" name="donationType" required>
                            <option value="">Choose Donation Type</option>
                            <option value="One-time">One-time</option>
                            <option value="Recurring">Recurring</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="causeType">Cause:</label></td>
                    <td>
                        <select id="causeType" name="causeType" required>
                            <option value="">Choose Cause</option>
                            <option value="Animal Welfare">Animal Welfare</option>
                            <option value="Animal Protection">Animal Protection</option>
                            <option value="Animal Rights">Animal Rights</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Donate"></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>