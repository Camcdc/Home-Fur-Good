<?php
session_start();
include '../databaseConnection.php';

// Fetch userID from the database or set it dynamically
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
if (!$userID) {
    die('<p>❌ No user ID found. Please log in to make a donation.</p>');
}else{
    $userID = $_SESSION['userID'];
}

// Process the donation form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['Pledge']) || $_POST['Pledge'] != 'on') {
        echo "<script>alert('❌ You must agree to the pledge before donating.'); window.history.back();</script>";
        exit;
    }
    $userID = $_POST["userID"];
    $donationAmount = $_POST["donationAmount"];
    $donationDate = $_POST["donationDate"];
    $donationType = $_POST["donationType"];
    $causeType = $_POST["causeType"];
    $pledge = 1; // Always 1 if checked, already validated above
    
    // Map donationType to an integer value for the recurring column
    $recurring = ($donationType === "Recurring") ? 1 : 0;  // 1 for Recurring, 0 for One-time
    
    // Set status based on amount
    $status = ($donationAmount >= 10000) ? 'pending' : 'confirmed';
    
    // Insert donation into the database with status
    $sqli = "INSERT INTO donations (amount, date, recurring, userID, causeType, status, pledge) 
             VALUES ('$donationAmount', '$donationDate', '$recurring', '$userID', '$causeType', '$status', '$pledge')";
    
    $result = $conn->query($sqli);

    if ($result === FALSE) {
        die("<script>alert('❌ Unable to create the donation record. Error: " . $conn->error . "');</script>");
    } else {
        if ($status === 'pending') {
            echo "<script>alert('✅ Donation amounts greater than R10,000 require administration confirmation. Your donation is pending approval.');</script>";
        } else {
            echo "<script>alert('✅ The donation record was successfully created.');</script>";
        }
        echo "<script>window.location.href='donationreceipt.php?userID=$userID';</script>";
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
    <link rel="stylesheet" type="text/css" href="../navbar functionalities/navbar.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background:  #B6CBDF;
        }
        .main-content {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 0 10px;
        }
        .donation-form {
            margin-top: 40px;
        }
        .view-donations {
            margin: 32px auto 0 auto;
            text-align: center;
        }
        .bank-details {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .bank-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .bank-row:last-child {
            border-bottom: none;
        }
        .bank-label {
            font-weight: bold;
            color: #333;
        }
        .bank-value {
            color: #555;
        }
        @media (max-width: 600px) {
            .main-content {
                max-width: 98vw;
                padding: 0 2vw;
            }
        }
    </style>
</head>
<body>
    <?php include '../navbar functionalities/navbar.php'?>
    <div class="main-content">
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
                        
                        <td><label for="Pledge"><a href="pledge.php?userID=<?php echo urlencode($userID); ?>">Click To View Full Pledge (Check the box if you agree to the pledge)</a></label></td><td><input type="checkbox" id="Pledge" name="Pledge" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Donate"></td>
                    </tr>
                </table>
            </form>
            <div class="bank-details">
                <h3>Bank Details</h3>
                <div class="bank-row"><span class="bank-label">Account Name:</span> <span class="bank-value">SPCA National Council of SA</span></div>
                <div class="bank-row"><span class="bank-label">Account Number:</span> <span class="bank-value">123456789</span></div>
                <div class="bank-row"><span class="bank-label">Bank:</span> <span class="bank-value">Standard Bank</span></div>
                <div class="bank-row"><span class="bank-label">Branch Code:</span> <span class="bank-value">051 001</span></div>
                <div class="bank-row"><span class="bank-label">SWIFT Code:</span> <span class="bank-value">SBZAZAJJ</span></div>
                <div class="bank-row"><span class="bank-label">Reference:</span> <span class="bank-value">(Optional) Your name, company name or campaign name</span></div>
            </div>
        </div>
        <div class="view-donations">
            <?php if ($userID): ?>
            <form action="donationreceipt.php" method="get" style="display:inline;">
                <input type="hidden" name="userID" value="<?php echo $userID; ?>">
                <input type="submit" value="View All Donations">
            </form>
            <?php else: ?>
            <p>Please log in to view your donations.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>