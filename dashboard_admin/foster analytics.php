<?php
 session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseconnection.php';

// --- DATA FETCHING FOR FOSTER ANALYTICS ---

// Total foster applications
$totalAppQuery = "SELECT COUNT(*) as total FROM application WHERE applicationType = 'Foster' AND isDeleted = 0";
$totalAppResult = $conn->query($totalAppQuery);
$totalApplications = ($totalAppResult && $totalAppResult->num_rows > 0) ? $totalAppResult->fetch_assoc()['total'] : 0;

// Applications by status
$statusQuery = "SELECT applicationStatus, COUNT(*) as count FROM application WHERE applicationType = 'Foster' AND isDeleted = 0 GROUP BY applicationStatus";
$statusResult = $conn->query($statusQuery);
$statuses = [];
$statusCounts = [];
if ($statusResult) {
    while ($row = $statusResult->fetch_assoc()) {
        $statuses[] = $row['applicationStatus'];
        $statusCounts[] = $row['count'];
    }
}

// Applications per month (last 12 months)
$monthQuery = "SELECT DATE_FORMAT(applicationDate, '%Y-%m') as month, COUNT(*) as count FROM application WHERE applicationDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH) AND applicationType = 'Foster' AND isDeleted = 0 GROUP BY month ORDER BY month ASC";
$monthResult = $conn->query($monthQuery);
$months = [];
$monthCounts = [];
if ($monthResult) {
    while ($row = $monthResult->fetch_assoc()) {
        $months[] = date('M Y', strtotime($row['month'] . '-01'));
        $monthCounts[] = $row['count'];
    }
}

// Animal foster/adoption statistics
$animalStatsQuery = "SELECT 
    SUM(CASE WHEN status = 'Fostered' THEN 1 ELSE 0 END) as fostered_animals,
    SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available_animals
    FROM animal WHERE isDeleted = 0";
$animalStatsResult = $conn->query($animalStatsQuery);
$animalStats = $animalStatsResult ? $animalStatsResult->fetch_assoc() : ['fostered_animals' => 0, 'available_animals' => 0];
$fosteredAnimals = $animalStats['fostered_animals'] ?? 0;
$availableAnimals = $animalStats['available_animals'] ?? 0;

// Top foster families
$topFostersQuery = "SELECT u.fname, u.sname, COUNT(ad.adoptionID) as foster_count
                    FROM adoption ad
                    JOIN user u ON ad.userID = u.userID
                    WHERE ad.status = 'Fostered'
                    GROUP BY u.userID
                    ORDER BY foster_count DESC
                    LIMIT 10";
$topFostersResult = $conn->query($topFostersQuery);
$topFosterNames = [];
$topFosterCounts = [];
if ($topFostersResult) {
    while ($row = $topFostersResult->fetch_assoc()) {
        $topFosterNames[] = $row['fname'] . ' ' . $row['sname'];
        $topFosterCounts[] = $row['foster_count'];
    }
}

// --- KPIs & INSIGHTS DATA ---
$recentQuery = "SELECT COUNT(*) as recent_count FROM application WHERE applicationDate >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND applicationType = 'Foster' AND isDeleted = 0";
$recentResult = $conn->query($recentQuery);
$recentApplications = ($recentResult && $recentResult->num_rows > 0) ? $recentResult->fetch_assoc()['recent_count'] : 0;

$approvedQuery = "SELECT COUNT(*) as approved_count FROM application WHERE applicationStatus = 'Approved' AND applicationType = 'Foster' AND isDeleted = 0";
$approvedResult = $conn->query($approvedQuery);
$approvedFosters = ($approvedResult && $approvedResult->num_rows > 0) ? $approvedResult->fetch_assoc()['approved_count'] : 0;

$approvalRate = $totalApplications > 0 ? round(($approvedFosters / $totalApplications) * 100, 1) : 0;

$overdueQuery = "SELECT COUNT(*) as overdue_count FROM application WHERE applicationStatus = 'Pending' AND applicationType = 'Foster' AND applicationDate < DATE_SUB(NOW(), INTERVAL 14 DAY) AND isDeleted = 0";
$overdueResult = $conn->query($overdueQuery);
$overdueApplications = ($overdueResult && $overdueResult->num_rows > 0) ? $overdueResult->fetch_assoc()['overdue_count'] : 0;

$avgAnimalsQuery = "SELECT AVG(foster_count) as avg_fosters FROM (SELECT COUNT(animalID) as foster_count FROM adoption WHERE status = 'Fostered' GROUP BY userID) as foster_counts";
$avgAnimalsResult = $conn->query($avgAnimalsQuery);
$avgAnimalsPerFoster = ($avgAnimalsResult && $avgAnimalsResult->num_rows > 0) ? round($avgAnimalsResult->fetch_assoc()['avg_fosters'], 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Foster Program Analytics</title>
    <link rel="stylesheet" href="volunteer analytics.css"> 
    <link rel="stylesheet" href="sidebar_admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'sidebar_admin.php'; ?>
    <div class="container">
        <h1><i class="fa-solid fa-house-chimney-user"></i> Foster Program Analytics</h1>
        
        <div class="stats-cards">
            <div class="stats-card"><div class="stat-icon"><i class="fa-solid fa-file-signature"></i></div><h2>Total Applications</h2><p class="stat-number"><?php echo $totalApplications; ?></p></div>
            <div class="stats-card"><div class="stat-icon"><i class="fa-solid fa-user-check"></i></div><h2>Approved Fosters</h2><p class="stat-number"><?php echo $approvedFosters; ?></p></div>
            <div class="stats-card"><div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div><h2>Recent (30 days)</h2><p class="stat-number"><?php echo $recentApplications; ?></p></div>
            <div class="stats-card"><div class="stat-icon"><i class="fa-solid fa-percentage"></i></div><h2>Approval Rate</h2><p class="stat-number"><?php echo $approvalRate; ?>%</p></div>
            <div class="stats-card"><div class="stat-icon"><i class="fa-solid fa-paw"></i></div><h2>Animals in Foster</h2><p class="stat-number"><?php echo $fosteredAnimals; ?></p></div>
            <div class="stats-card"><div class="stat-icon"><i class="fa-solid fa-heart"></i></div><h2>Animals Needing Foster</h2><p class="stat-number"><?php echo $availableAnimals; ?></p></div>
        </div>

        <div class="charts-section">
            <div class="chart-card">
                <h3><i class="fa-solid fa-chart-pie"></i> Applications by Status</h3>
                <canvas id="statusPieChart"></canvas>
            </div>
            <div class="chart-card">
                <h3><i class="fa-solid fa-chart-bar"></i> Monthly Applications</h3>
                <canvas id="monthBarChart"></canvas>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-card full-width">
                <h3><i class="fa-solid fa-star"></i> Top Foster Families by Animals Cared For</h3>
                <canvas id="topFostersChart"></canvas>
            </div>
        </div>
        
        <div class="insights-section">
            <div class="insight-card">
                <h3><i class="fa-solid fa-lightbulb"></i> Key Insights</h3>
                <ul class="insights-list">
                    <?php if ($overdueApplications > 0): ?>
                    <li class="insight-warning"><i class="fa-solid fa-exclamation-circle"></i><?php echo $overdueApplications; ?> pending applications are older than 14 days and require attention.</li>
                    <?php endif; ?>
                    
                    <?php if ($approvalRate < 50 && $totalApplications > 10): ?>
                    <li class="insight-warning"><i class="fa-solid fa-chart-line-down"></i>Low approval rate of <?php echo $approvalRate; ?>% may indicate a need to review application criteria.</li>
                    <?php elseif ($approvalRate >= 75): ?>
                    <li class="insight-success"><i class="fa-solid fa-trophy"></i>Excellent approval rate of <?php echo $approvalRate; ?>%!</li>
                    <?php endif; ?>
                    
                    <?php if ($recentApplications > 10): ?>
                    <li class="insight-info"><i class="fa-solid fa-trending-up"></i>Strong interest with <?php echo $recentApplications; ?> new applications in the last 30 days.</li>
                    <?php endif; ?>
                    
                    <?php if ($availableAnimals > $fosteredAnimals && $availableAnimals > 5): ?>
                    <li class="insight-info"><i class="fa-solid fa-bullhorn"></i>High need for foster homes with <?php echo $availableAnimals; ?> animals available.</li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="insight-card">
                <h3><i class="fa-solid fa-chart-column"></i> Additional Statistics</h3>
                <div class="stats-grid-small">
                    <div class="stat-item"><span class="stat-label">Pending Applications:</span><span class="stat-value"><?php echo $stats['pending_applications'] ?? 0; ?></span></div>
                    <div class="stat-item"><span class="stat-label">Rejected Applications:</span><span class="stat-value"><?php echo $stats['rejected_applications'] ?? 0; ?></span></div>
                    <div class="stat-item"><span class="stat-label">Avg. Animals per Foster:</span><span class="stat-value"><?php echo $avgAnimalsPerFoster; ?></span></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const statuses = <?php echo json_encode($statuses); ?>;
        const statusCounts = <?php echo json_encode($statusCounts); ?>;
        const months = <?php echo json_encode($months); ?>;
        const monthCounts = <?php echo json_encode($monthCounts); ?>;
        const topFosterNames = <?php echo json_encode($topFosterNames); ?>;
        const topFosterCounts = <?php echo json_encode($topFosterCounts); ?>;

        const primaryColors = ['#28a745', '#20c997', '#17a2b8', '#6f42c1', '#fd7e14', '#dc3545'];
        const statusColors = {'Pending': '#ffc107', 'Approved': '#28a745', 'Rejected': '#dc3545'};

        if (document.getElementById('statusPieChart') && statuses.length > 0) {
            const statusCtx = document.getElementById('statusPieChart').getContext('2d');
            new Chart(statusCtx, { type: 'pie', data: { labels: statuses, datasets: [{ data: statusCounts, backgroundColor: statuses.map(s => statusColors[s] || primaryColors[0]) }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } } });
        }

        if (document.getElementById('monthBarChart') && months.length > 0) {
            const monthCtx = document.getElementById('monthBarChart').getContext('2d');
            new Chart(monthCtx, { type: 'bar', data: { labels: months, datasets: [{ label: 'Applications', data: monthCounts, backgroundColor: primaryColors[0] + '80' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } } });
        }

        if (document.getElementById('topFostersChart') && topFosterNames.length > 0) {
            const topFostersCtx = document.getElementById('topFostersChart').getContext('2d');
            new Chart(topFostersCtx, { type: 'bar', data: { labels: topFosterNames, datasets: [{ label: 'Animals Fostered', data: topFosterCounts, backgroundColor: primaryColors[1] + '80' }] }, options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } } } });
        }
    });
    </script>
</body>
</html>