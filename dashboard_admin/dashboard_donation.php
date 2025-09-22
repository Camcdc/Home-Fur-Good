<?php
//display all donation records
 session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseconnection.php';
include 'sidebar_admin.php';

// STATISTIC QUERIES
$totalDonationsQuery = "SELECT COUNT(*) AS total_donations FROM donations WHERE isDeleted = 0";
$totalDonationsResult = $conn->query($totalDonationsQuery);

$totalAmountQuery = "SELECT SUM(amount) AS total_amount FROM donations WHERE isDeleted = 0";
$totalAmountResult = $conn->query($totalAmountQuery);

// Top donors (by total amount)
$topDonorsQuery = "SELECT userID, SUM(amount) AS total_given FROM donations WHERE isDeleted = 0 GROUP BY userID ORDER BY total_given DESC LIMIT 3";
$topDonorsResult = $conn->query($topDonorsQuery);

// Donation breakdown by causeType
$causeBreakdownQuery = "SELECT causeType, SUM(amount) AS total FROM donations WHERE isDeleted = 0 GROUP BY causeType";
$causeBreakdownResult = $conn->query($causeBreakdownQuery);

// Recent recurring donations
$recentRecurringQuery = "SELECT donationID, amount, date, userID, causeType FROM donations WHERE isDeleted = 0 AND recurring = 1 ORDER BY date DESC LIMIT 3";
$recentRecurringResult = $conn->query($recentRecurringQuery);

// Pending/processing donations
$pendingDonationsQuery = "SELECT donationID, amount, date, userID, status FROM donations WHERE isDeleted = 0 AND status IN ('pending','confirmed') ORDER BY date DESC LIMIT 3";
$pendingDonationsResult = $conn->query($pendingDonationsQuery);

// Recent donations
$recentDonationsQuery = "SELECT donationID, amount, date, recurring, userID, causeType, status FROM donations WHERE isDeleted = 0 ORDER BY date DESC LIMIT 5";
$recentDonationsResult = $conn->query($recentDonationsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Donation Dashboard</title>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="dashboard_donation.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include 'sidebar_admin.php'; ?>
<div class="container">
    <div class="dashboard-heading">
        <h3>Donation Dashboard</h3>
    </div>
    <div class="dashboard-container">
        <div class="stat-cards">
            <div class="card">
                <h3>Total Donations</h3>
                <p>
                    <?php
                    if ($row = $totalDonationsResult->fetch_assoc()) {
                        echo $row['total_donations'];
                    }
                    ?>
                </p>
            </div>
            <div class="card">
                <h3>Total Amount</h3>
                <p>
                    <?php
                    if ($row = $totalAmountResult->fetch_assoc()) {
                        echo 'R' . number_format($row['total_amount'], 2);
                    }
                    ?>
                </p>
            </div>
        </div>
        <div class="dashboard-section">
            <!-- TOP DONORS -->
            <div class="section">
                <div class="section-header">
                    <h4>Top Donors</h4>
                </div>
                <div class="section-list">
                    <?php

$sql = "
    SELECT u.userID, u.Fname, u.Sname, SUM(d.amount) AS total_given
    FROM donations d
    JOIN user u ON d.userID = u.userID
    WHERE d.isDeleted = 0
    GROUP BY u.userID
    ORDER BY total_given DESC
    LIMIT 5
";

$topDonorsResult1 = $conn->query($sql);

if ($topDonorsResult1 && $topDonorsResult1->num_rows > 0) {
    while ($row = $topDonorsResult1->fetch_assoc()) {
        echo '<div class="list-item">';
        echo '<span class="item-title">User: ' . htmlspecialchars($row['Fname']) . ' ' . htmlspecialchars($row['Sname']) . '</span>';
        echo '<span class="item-subtitle">Total Given: R' . number_format((float)$row['total_given'], 2) . '</span>';
        
        // Acknowledgement for top donors
        if ($row['total_given'] >= 10000) {
            echo '<span class="item-date" style="color: #d35400; font-weight: bold;">🌟 Thank you for your generosity!</span>';
        }
        
        echo '</div>';
    }
} else {
    echo '<div class="list-item"><span class="item-subtitle">No donors found.</span></div>';
}
?>                                 
                </div>
            </div>
            <!-- DONATION BREAKDOWN BY CAUSE -->
            <div class="section">
                <div class="section-header">
                    <h4>Donation Breakdown by Cause</h4>
                </div>
                <div class="section-list">
                    <?php
                    if ($causeBreakdownResult && $causeBreakdownResult->num_rows > 0) {
                        while ($row = $causeBreakdownResult->fetch_assoc()) {
                            echo '<div class="list-item">';
                            echo '<span class="item-title">' . htmlspecialchars($row['causeType']) . '</span>';
                            echo '<span class="item-subtitle">Total: R' . htmlspecialchars(number_format($row['total'], 2)) . '</span>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="list-item"><span class="item-subtitle">No cause data found.</span></div>';
                    }
                    ?>
                </div>
            </div>
            <!-- RECENT RECURRING DONATIONS -->
            <div class="section">
                <div class="section-header">
                    <h4>Recent Recurring Donations</h4>
                </div>
                <div class="section-list">
                    <?php
                    if ($recentRecurringResult && $recentRecurringResult->num_rows > 0) {
                        while ($row = $recentRecurringResult->fetch_assoc()) {
                            echo '<div class="list-item">';
                            echo '<span class="item-title">Donation ID: ' . htmlspecialchars($row['donationID']) . '</span>';
                            echo '<span class="item-subtitle">Amount: R' . htmlspecialchars(number_format($row['amount'], 2)) . '</span>';
                            echo '<span class="item-date">Date: ' . htmlspecialchars($row['date']) . '</span>';
                            echo '<span class="item-date">User ID: ' . htmlspecialchars($row['userID']) . '</span>';
                            echo '<span class="item-date">Cause: ' . htmlspecialchars($row['causeType']) . '</span>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="list-item"><span class="item-subtitle">No recurring donations found.</span></div>';
                    }
                    ?>
                </div>
            </div>
            <!-- PENDING/PROCESSING DONATIONS -->
            <div class="section">
                <div class="section-header">
                    <h4>Pending/Processing Donations</h4>
                </div>
                <div class="section-list">
                    <?php
                    if ($pendingDonationsResult && $pendingDonationsResult->num_rows > 0) {
                        while ($row = $pendingDonationsResult->fetch_assoc()) {
                            echo '<div class="list-item">';
                            echo '<span class="item-title">Donation ID: ' . htmlspecialchars($row['donationID']) . '</span>';
                            echo '<span class="item-subtitle">Amount: R' . htmlspecialchars(number_format($row['amount'], 2)) . '</span>';
                            echo '<span class="item-date">Date: ' . htmlspecialchars($row['date']) . '</span>';
                            echo '<span class="item-date">User ID: ' . htmlspecialchars($row['userID']) . '</span>';
                            echo '<span class="item-date">Status: ' . htmlspecialchars($row['status']) . '</span>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="list-item"><span class="item-subtitle">No pending donations found.</span></div>';
                    }
                    ?>
                </div>
            </div>
            <!-- RECENT DONATIONS -->
            <div class="section">
                <div class="section-header">
                    <h4>Recent Donations</h4>
                    <div class="buttons">
                        <a href="displayAllDonations.php" class="add-btn">
                            <span class="material-symbols-outlined">menu</span>
                            <span class="btn-text">View Donations</span>
                        </a>
                    </div>
                </div>
                <div class="section-list">
                    <?php
                    if ($recentDonationsResult && $recentDonationsResult->num_rows > 0) {
                        while ($row = $recentDonationsResult->fetch_assoc()) {
                            echo '<div class="list-item">';
                            echo '<span class="item-title">Donation ID: ' . htmlspecialchars($row['donationID']) . '</span>';
                            echo '<span class="item-subtitle">Amount: R' . htmlspecialchars(number_format($row['amount'], 2)) . '</span>';
                            echo '<span class="item-date">Date: ' . htmlspecialchars($row['date']) . '</span>';
                            echo '<span class="item-date">Recurring: ' . ($row['recurring'] ? 'Yes' : 'No') . '</span>';
                            echo '<span class="item-date">User ID: ' . htmlspecialchars($row['userID']) . '</span>';
                            echo '<span class="item-date">Cause: ' . htmlspecialchars($row['causeType']) . '</span>';
                            echo '<span class="item-date">Status: ' . htmlspecialchars($row['status']) . '</span>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="list-item"><span class="item-subtitle">No donations found.</span></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>