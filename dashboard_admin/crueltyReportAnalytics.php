<?php
include '../databaseconnection.php';
 session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }

// Reports by status
$statusQuery = "SELECT investigationStatus, COUNT(*) as count FROM cruelty_report GROUP BY investigationStatus";
$statusResult = mysqli_query($conn, $statusQuery);
$statuses = [];
$counts = [];
while ($row = mysqli_fetch_assoc($statusResult)) {
    $statuses[] = $row['investigationStatus'];
    $counts[] = $row['count'];
}

// Total reports
$totalReportsQuery = "SELECT COUNT(*) as total FROM cruelty_report where isDeleted=0";
$totalReportsResult = mysqli_query($conn, $totalReportsQuery);
$totalReports = ($totalReportsResult && mysqli_num_rows($totalReportsResult) > 0) ? mysqli_fetch_assoc($totalReportsResult)['total'] : 0;

// Reports per month (last 12 months)
$sql1 = "SELECT DATE_FORMAT(createDate, '%Y-%m') as month, COUNT(*) as count FROM cruelty_report WHERE createDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH) AND createDate IS NOT NULL GROUP BY month ORDER BY month ASC";
$monthResult = mysqli_query($conn, $sql1);
$months = [];
$monthCounts = [];
while ($row = mysqli_fetch_assoc($monthResult)) {
    $months[] = date('M Y', strtotime($row['month'] . '-01'));
    $monthCounts[] = $row['count'];
}

// Reports that have been unassigned an inspector
$sql = "SELECT COUNT(*) AS unassignedCount 
FROM cruelty_report 
WHERE (userID IS NULL OR userID = '') AND isDeleted = 0;";
$result = mysqli_query($conn, $sql);
$unassignedCount = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['unassignedCount'] : 0;


// Average resolution time (for completed cases)
$resolutionQuery = "SELECT AVG(completedDate, createDate)) as avg_days FROM cruelty_report WHERE investigationStatus = 'investigation complete' AND completedDate IS NOT NULL AND createDate IS NOT NULL";
$resolutionResult = mysqli_query($conn, $resolutionQuery);
$avgResolutionDays = ($resolutionResult && mysqli_num_rows($resolutionResult) > 0) ? round(mysqli_fetch_assoc($resolutionResult)['avg_days'], 1) : 0;

// Reports by day of week
$dayQuery = "SELECT DAYNAME(createDate) as day_name, COUNT(*) as count FROM cruelty_report WHERE createDate IS NOT NULL AND  createDate >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DAYOFWEEK(createDate), DAYNAME(createDate) ORDER BY DAYOFWEEK(createDate)";
$dayResult = mysqli_query($conn, $dayQuery);
$days = [];
$dayCounts = [];
while ($row = mysqli_fetch_assoc($dayResult)) {
    $days[] = $row['day_name'];
    $dayCounts[] = $row['count'];
}

// Location/area analysis (assuming there's a location field)
$locationQuery = "SELECT location, COUNT(*) as count FROM cruelty_report WHERE location IS NOT NULL AND location != '' GROUP BY location ORDER BY count DESC LIMIT 10";
$locationResult = mysqli_query($conn, $locationQuery);
$locations = [];
$locationCounts = [];
while ($row = mysqli_fetch_assoc($locationResult)) {
    $locations[] = $row['location'];
    $locationCounts[] = $row['count'];
}

// Recent activity (last 30 days)
$recentQuery = "SELECT COUNT(*) as recent_count FROM cruelty_report WHERE createDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$recentResult = mysqli_query($conn, $recentQuery);
$recentReports = ($recentResult && mysqli_num_rows($recentResult) > 0) ? mysqli_fetch_assoc($recentResult)['recent_count'] : 0;

// Pending reports older than 30 days
$overdueQuery = "SELECT COUNT(*) as overdue_count FROM cruelty_report WHERE investigationStatus IN ('ongoing investigation') AND createDate < DATE_SUB(NOW(), INTERVAL 15 DAY)";
$overdueResult = mysqli_query($conn, $overdueQuery);
$overdueReports = ($overdueResult && mysqli_num_rows($overdueResult) > 0) ? mysqli_fetch_assoc($overdueResult)['overdue_count'] : 0;

// Completion rate
$completedQuery = "SELECT COUNT(*) as completed_count FROM cruelty_report WHERE investigationStatus = 'investigation complete'";
$completedResult = mysqli_query($conn, $completedQuery);
$completedReports = ($completedResult && mysqli_num_rows($completedResult) > 0) ? mysqli_fetch_assoc($completedResult)['completed_count'] : 0;
$completionRate = $totalReports > 0 ? round(($completedReports / $totalReports) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cruelty Report Analytics</title>
    <link rel="stylesheet" href="crueltyReportAnalytics.css">
    <link rel="stylesheet" href="sidebar_admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'sidebar_admin.php'; ?>
    <div class="container">
        <h1><i class="fa-solid fa-chart-line"></i> Cruelty Report Analytics Dashboard</h1>
        
        <!-- Key Performance Indicators -->
        <div class="stats-cards">
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
                <h2>Total Reports</h2>
                <p class="stat-number"><?php echo $totalReports; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-user-slash"></i></div>
                <h2>Unassigned Reports</h2>
                <p class="stat-number"><?php echo $unassignedCount; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                <h2>Recent (30 days)</h2>
                <p class="stat-number"><?php echo $recentReports; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
                <h2>Completion Rate</h2>
                <p class="stat-number"><?php echo $completionRate; ?>%</p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <h2>Avg Resolution</h2>
                <p class="stat-number"><?php echo $avgResolutionDays; ?> days</p>
            </div>
        </div>

        <!-- Primary Charts Row -->
        <div class="charts-section">
            <div class="chart-card">
                <h3><i class="fa-solid fa-chart-pie"></i> Reports by Status</h3>
                <canvas id="statusPieChart"></canvas>
            </div>
            <div class="chart-card">
                <h3><i class="fa-solid fa-chart-bar"></i> Monthly Trend</h3>
                <canvas id="monthBarChart"></canvas>
            </div>
        </div>

        <!-- Secondary Charts Row -->
        <div class="charts-section">
            <div class="chart-card">
                <h3><i class="fa-solid fa-calendar-week"></i> Reports by Day of Week</h3>
                <canvas id="dayChart"></canvas>
            </div>
            <div class="chart-card">
                <h3><i class="fa-solid fa-user-check"></i> Inspector Assignment</h3>
                <canvas id="inspectorDoughnutChart"></canvas>
            </div>
        </div>

        <!-- Location Analysis (if location data exists) -->
        <?php if (!empty($locations)): ?>
        <div class="charts-section">
            <div class="chart-card full-width">
                <h3><i class="fa-solid fa-map-marker-alt"></i> Top Reporting Locations</h3>
                <canvas id="locationChart"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <!-- Performance Insights -->
        <div class="insights-section">
            <div class="insight-card">
                <h3><i class="fa-solid fa-lightbulb"></i> Key Insights</h3>
                <ul class="insights-list">
                    <?php if ($unassignedCount > 0): ?>
                    <li class="insight-warning">
                        <i class="fa-solid fa-exclamation-circle"></i>
                        <?php echo $unassignedCount; ?> reports need inspector assignment
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($overdueReports > 0): ?>
                    <li class="insight-danger">
                        <i class="fa-solid fa-clock"></i>
                        <?php echo $overdueReports; ?> cases are overdue (>30 days)
                    </li>
                    <?php endif; ?>
                    
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
                    
                    <?php if ($recentReports > 0): ?>
                    <li class="insight-info">
                        <i class="fa-solid fa-trending-up"></i>
                        <?php echo $recentReports; ?> new reports in the last 30 days
                    </li>
                    <?php endif; ?>
                    
                    <?php if (!empty($crueltyTypes) && count($crueltyTypes) > 0): ?>
                    <li class="insight-info">
                        <i class="fa-solid fa-chart-column"></i>
                        Most common cruelty type: <?php echo $crueltyTypes[0]; ?>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Data from PHP
        const statuses = <?php echo json_encode($statuses); ?>;
        const counts = <?php echo json_encode($counts); ?>;
        const months = <?php echo json_encode($months); ?>;
        const monthCounts = <?php echo json_encode($monthCounts); ?>;
        const unassignedCount = <?php echo json_encode($unassignedCount); ?>;
        const assignedCount = <?php echo json_encode($totalReports - $unassignedCount); ?>;
        const days = <?php echo json_encode($days); ?>;
        const dayCounts = <?php echo json_encode($dayCounts); ?>;
        const locations = <?php echo json_encode($locations); ?>;
        const locationCounts = <?php echo json_encode($locationCounts); ?>;
    </script>
    <script src="crueltyReportAnalytics.js"></script>
</body>
</html>