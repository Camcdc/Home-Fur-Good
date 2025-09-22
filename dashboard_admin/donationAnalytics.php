<?php
 session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseconnection.php';

// Total donations
$totalDonationsQuery = "SELECT COUNT(*) as total FROM donations WHERE isDeleted = 0";
$totalDonationsResult = $conn->query($totalDonationsQuery);
$totalDonations = ($totalDonationsResult && $totalDonationsResult->num_rows > 0) ? $totalDonationsResult->fetch_assoc()['total'] : 0;

// Total amount
$totalAmountQuery = "SELECT SUM(amount) as total_amount FROM donations WHERE isDeleted = 0";
$totalAmountResult = $conn->query($totalAmountQuery);
$totalAmount = ($totalAmountResult && $totalAmountResult->num_rows > 0) ? $totalAmountResult->fetch_assoc()['total_amount'] : 0;

// Donations per month (last 12 months)
$monthQuery = "SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total FROM donations WHERE date >= DATE_SUB(NOW(), INTERVAL 12 MONTH) AND isDeleted = 0 GROUP BY month ORDER BY month ASC";
$monthResult = $conn->query($monthQuery);
$months = [];
$monthTotals = [];
while ($row = $monthResult->fetch_assoc()) {
    $months[] = date('M Y', strtotime($row['month'] . '-01'));
    $monthTotals[] = $row['total'];
}

// Donations by causeType
$causeQuery = "SELECT causeType, SUM(amount) as total FROM donations WHERE isDeleted = 0 GROUP BY causeType";
$causeResult = $conn->query($causeQuery);
$causes = [];
$causeTotals = [];
while ($row = $causeResult->fetch_assoc()) {
    $causes[] = $row['causeType'];
    $causeTotals[] = $row['total'];
}

// Top donors
$topDonorsQuery = "SELECT donations.userID, user.Fname, user.Sname, SUM(amount) as total_given FROM donations JOIN user ON donations.userID = user.userID WHERE donations.isDeleted = 0 GROUP BY userID ORDER BY total_given DESC";
$topDonorsResult = $conn->query($topDonorsQuery);
$topDonorIDs = [];
$topDonorTotals = [];
$topDonorNames = [];
while ($row = $topDonorsResult->fetch_assoc()) {
    $topDonorIDs[] = $row['userID'];
    $topDonorNames[] = $row['Fname'] . ' ' . $row['Sname'];
    $topDonorTotals[] = $row['total_given'];
}

// Recent donations (last 30 days)
$recentQuery = "SELECT COUNT(*) as recent_count FROM donations WHERE date >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND isDeleted = 0";
$recentResult = $conn->query($recentQuery);
$recentDonations = ($recentResult && $recentResult->num_rows > 0) ? $recentResult->fetch_assoc()['recent_count'] : 0;

// Recurring donations
$recurringQuery = "SELECT COUNT(*) as recurring_count FROM donations WHERE recurring = 1 AND isDeleted = 0";
$recurringResult = $conn->query($recurringQuery);
$recurringDonations = ($recurringResult && $recurringResult->num_rows > 0) ? $recurringResult->fetch_assoc()['recurring_count'] : 0;

// Average donation amount
$avgQuery = "SELECT AVG(amount) as avg_amount FROM donations WHERE isDeleted = 0";
$avgResult = $conn->query($avgQuery);
$avgAmount = ($avgResult && $avgResult->num_rows > 0) ? round($avgResult->fetch_assoc()['avg_amount'], 2) : 0;

// Completion rate (confirmed donations)
$confirmedQuery = "SELECT COUNT(*) as confirmed_count FROM donations WHERE status = 'confirmed' AND isDeleted = 0";
$confirmedResult = $conn->query($confirmedQuery);
$confirmedDonations = ($confirmedResult && $confirmedResult->num_rows > 0) ? $confirmedResult->fetch_assoc()['confirmed_count'] : 0;
$completionRate = $totalDonations > 0 ? round(($confirmedDonations / $totalDonations) * 100, 1) : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation Analytics</title>
    <link rel="stylesheet" href="donationAnalytics.css">
    <link rel="stylesheet" href="sidebar_admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'sidebar_admin.php'; ?>
    <div class="container">
        <h1><i class="fa-solid fa-chart-line"></i> Donation Analytics Dashboard</h1>
        
        <!-- Key Performance Indicators -->
        <div class="stats-cards">
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-hand-holding-heart"></i></div>
                <h2>Total Donations</h2>
                <p class="stat-number"><?php echo $totalDonations; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
                <h2>Total Amount</h2>
                <p class="stat-number">R<?php echo number_format($totalAmount, 2); ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                <h2>Recent (30 days)</h2>
                <p class="stat-number"><?php echo $recentDonations; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-repeat"></i></div>
                <h2>Recurring Donations</h2>
                <p class="stat-number"><?php echo $recurringDonations; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
                <h2>Avg Donation</h2>
                <p class="stat-number">R<?php echo number_format($avgAmount, 2); ?></p>
            </div>
        </div>

        <!-- Primary Charts Row -->
        <div class="charts-section">
            <div class="chart-card">
                <h3><i class="fa-solid fa-chart-pie"></i> Donations by Cause</h3>
                <canvas id="causePieChart"></canvas>
            </div>
            <div class="chart-card">
                <h3><i class="fa-solid fa-chart-bar"></i> Monthly Trend</h3>
                <canvas id="monthBarChart"></canvas>
            </div>
        </div>

        <!-- Secondary Charts Row -->
        <div class="charts-section">
            <div class="chart-card">
                <h3><i class="fa-solid fa-user-money"></i> Top Donors</h3>
                <canvas id="topDonorChart"></canvas>
            </div>
        </div>

        <!-- Performance Insights -->
        <div class="insights-section">
            <div class="insight-card">
                <h3><i class="fa-solid fa-lightbulb"></i> Key Insights</h3>
                <ul class="insights-list">
                    <?php if ($completionRate >= 80): ?>
                    <li class="insight-success">
                        <i class="fa-solid fa-trophy"></i>
                        High completion rate of <?php echo $completionRate; ?>%
                    </li>
                    <?php elseif ($completionRate < 60): ?>
                    <li class="insight-warning">
                        <i class="fa-solid fa-chart-line-down"></i>
                        Low completion rate of <?php echo $completionRate; ?>% - needs attention
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($recentDonations > 0): ?>
                    <li class="insight-info">
                        <i class="fa-solid fa-trending-up"></i>
                        <?php echo $recentDonations; ?> new donations in the last 30 days
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($recurringDonations > 0): ?>
                    <li class="insight-info">
                        <i class="fa-solid fa-repeat"></i>
                        <?php echo $recurringDonations; ?> recurring donations
                    </li>
                    <?php endif; ?>
                    
                    <?php if (!empty($topDonorIDs) && $topDonorTotals[0] >= 10000): ?>
                    <li class="insight-success">
                        <i class="fa-solid fa-star"></i>
                        Donor <?php echo htmlspecialchars($topDonorIDs[0]); ?> has contributed over R10,000!
                    </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </div>

    <script>
        // Data from PHP
        const causes = <?php echo json_encode($causes); ?>;
        const causeTotals = <?php echo json_encode($causeTotals); ?>;
        const months = <?php echo json_encode($months); ?>;
        const monthTotals = <?php echo json_encode($monthTotals); ?>;
        const topDonorIDs = <?php echo json_encode($topDonorIDs); ?>;
        const topDonorTotals = <?php echo json_encode($topDonorTotals); ?>;
        const topDonorNames = <?php echo json_encode($topDonorNames); ?>;
    </script>
    <script src="donationAnalytics.js"></script>
</body>
</html>