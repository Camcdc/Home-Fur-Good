<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseconnection.php';

// Total volunteer applications
$totalApplicationsQuery = "SELECT COUNT(*) as total FROM application WHERE applicationType = 'Volunteer' AND isDeleted = 0";
$totalApplicationsResult = $conn->query($totalApplicationsQuery);
$totalApplications = ($totalApplicationsResult && $totalApplicationsResult->num_rows > 0) ? $totalApplicationsResult->fetch_assoc()['total'] : 0;

// Applications by status
$statusQuery = "SELECT applicationStatus, COUNT(*) as count FROM application WHERE applicationType = 'Volunteer' AND isDeleted = 0 GROUP BY applicationStatus";
$statusResult = $conn->query($statusQuery);
$statuses = [];
$statusCounts = [];
while ($row = $statusResult->fetch_assoc()) {
    $statuses[] = $row['applicationStatus'];
    $statusCounts[] = $row['count'];
}

// Applications per month (last 12 months)
$monthQuery = "SELECT DATE_FORMAT(applicationDate, '%Y-%m') as month, COUNT(*) as count FROM application WHERE applicationDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH) AND applicationType = 'Volunteer' AND isDeleted = 0 GROUP BY month ORDER BY month ASC";
$monthResult = $conn->query($monthQuery);
$months = [];
$monthCounts = [];
while ($row = $monthResult->fetch_assoc()) {
    $months[] = date('M Y', strtotime($row['month'] . '-01'));
    $monthCounts[] = $row['count'];
}

// Task completion statistics
$taskStatsQuery = "SELECT 
    COUNT(*) as total_tasks,
    SUM(CASE WHEN completionStatus = 1 THEN 1 ELSE 0 END) as completed_tasks,
    SUM(CASE WHEN completionStatus = 0 THEN 1 ELSE 0 END) as active_tasks
    FROM assignedtask";
$taskStatsResult = $conn->query($taskStatsQuery);
$taskStats = $taskStatsResult->fetch_assoc();
$totalTasks = $taskStats['total_tasks'];
$completedTasks = $taskStats['completed_tasks'];
$activeTasks = $taskStats['active_tasks'];

// Task distribution by type
$taskTypeQuery = "SELECT taskName, COUNT(*) as count FROM assignedtask GROUP BY taskName ORDER BY count DESC LIMIT 10";
$taskTypeResult = $conn->query($taskTypeQuery);
$taskTypes = [];
$taskTypeCounts = [];
while ($row = $taskTypeResult->fetch_assoc()) {
    $taskTypes[] = $row['taskName'];
    $taskTypeCounts[] = $row['count'];
}

// CORRECTED: Top volunteers by task completion
$topVolunteersQuery = "SELECT 
    u.fname, u.sname, u.userID,
    COUNT(ta.assignTaskID) as total_tasks,
    SUM(CASE WHEN at.completionStatus = 1 THEN 1 ELSE 0 END) as completed_tasks
    FROM user u
    JOIN task_assignment ta ON u.userID = ta.userID
    JOIN assignedtask at ON ta.assignTaskID = at.assignTaskID
    GROUP BY u.userID, u.fname, u.sname
    HAVING total_tasks > 0
    ORDER BY completed_tasks DESC
    LIMIT 10";
$topVolunteersResult = $conn->query($topVolunteersQuery);
$topVolunteerNames = [];
$topVolunteerTasks = [];
if ($topVolunteersResult) { // Check if query was successful
    while ($row = $topVolunteersResult->fetch_assoc()) {
        $topVolunteerNames[] = $row['fname'] . ' ' . $row['sname'];
        $topVolunteerTasks[] = $row['completed_tasks'];
    }
}

// Recent applications (last 30 days)
$recentQuery = "SELECT COUNT(*) as recent_count FROM application WHERE applicationDate >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND applicationType = 'Volunteer' AND isDeleted = 0";
$recentResult = $conn->query($recentQuery);
$recentApplications = ($recentResult && $recentResult->num_rows > 0) ? $recentResult->fetch_assoc()['recent_count'] : 0;

// Pending applications older than 14 days
$overdueQuery = "SELECT COUNT(*) as overdue_count FROM application WHERE applicationStatus = 'Pending' AND applicationType = 'Volunteer' AND applicationDate < DATE_SUB(NOW(), INTERVAL 14 DAY) AND isDeleted = 0";
$overdueResult = $conn->query($overdueQuery);
$overdueApplications = ($overdueResult && $overdueResult->num_rows > 0) ? $overdueResult->fetch_assoc()['overdue_count'] : 0;

// Approval rate
$approvedQuery = "SELECT COUNT(*) as approved_count FROM application WHERE applicationStatus = 'Approved' AND applicationType = 'Volunteer' AND isDeleted = 0";
$approvedResult = $conn->query($approvedQuery);
$approvedApplications = ($approvedResult && $approvedResult->num_rows > 0) ? $approvedResult->fetch_assoc()['approved_count'] : 0;
$approvalRate = $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 1) : 0;

// Task completion rate
$taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

// CORRECTED: Average tasks per volunteer
$avgTasksQuery = "SELECT AVG(task_count) as avg_tasks FROM (SELECT COUNT(assignTaskID) as task_count FROM task_assignment GROUP BY userID) as volunteer_tasks";
$avgTasksResult = $conn->query($avgTasksQuery);
$avgTasksPerVolunteer = ($avgTasksResult && $avgTasksResult->num_rows > 0) ? round($avgTasksResult->fetch_assoc()['avg_tasks'], 1) : 0;

// CORRECTED: Volunteer retention (volunteers who completed more than 5 tasks)
$retentionQuery = "SELECT COUNT(DISTINCT ta.userID) as active_volunteers 
                   FROM task_assignment ta
                   JOIN assignedtask at ON ta.assignTaskID = at.assignTaskID
                   WHERE at.completionStatus = 1 
                   GROUP BY ta.userID 
                   HAVING COUNT(ta.assignTaskID) >= 5";
$retentionResult = $conn->query($retentionQuery);
$activeVolunteers = $retentionResult ? $retentionResult->num_rows : 0;

// REMOVED: Animals helped query, as `animalID` does not exist in `assignedtask`. Set to 0.
$animalsHelped = 0;

// Volunteer activity by day of week (last 30 days)
$dayQuery = "SELECT DAYNAME(applicationDate) as day_name, COUNT(*) as count FROM application WHERE applicationType = 'Volunteer' AND applicationDate >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND applicationDate IS NOT NULL GROUP BY DAYOFWEEK(applicationDate), DAYNAME(applicationDate) ORDER BY DAYOFWEEK(applicationDate)";
$dayResult = $conn->query($dayQuery);
$days = [];
$dayCounts = [];
while ($row = $dayResult->fetch_assoc()) {
    $days[] = $row['day_name'];
    $dayCounts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Analytics</title>
    <link rel="stylesheet" href="volunteer analytics.css">
    <link rel="stylesheet" href="sidebar_admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'sidebar_admin.php'; ?>
    <div class="container">
        <h1><i class="fa-solid fa-chart-line"></i> Volunteer Analytics Dashboard</h1>
        
        <div class="stats-cards">
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <h2>Total Applications</h2>
                <p class="stat-number"><?php echo $totalApplications; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
                <h2>Active Volunteers</h2>
                <p class="stat-number"><?php echo $approvedApplications; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                <h2>Recent (30 days)</h2>
                <p class="stat-number"><?php echo $recentApplications; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-percentage"></i></div>
                <h2>Approval Rate</h2>
                <p class="stat-number"><?php echo $approvalRate; ?>%</p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-list-check"></i></div>
                <h2>Total Tasks</h2>
                <p class="stat-number"><?php echo $totalTasks; ?></p>
            </div>
            <div class="stats-card">
                <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
                <h2>Task Completion</h2>
                <p class="stat-number"><?php echo $taskCompletionRate; ?>%</p>
            </div>
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
            <div class="chart-card">
                <h3><i class="fa-solid fa-tasks"></i> Task Distribution</h3>
                <canvas id="taskTypeChart"></canvas>
            </div>
            <div class="chart-card">
                <h3><i class="fa-solid fa-chart-simple"></i> Task Completion Status</h3>
                <canvas id="taskCompletionChart"></canvas>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-card full-width">
                <h3><i class="fa-solid fa-star"></i> Top Volunteers by Tasks Completed</h3>
                <canvas id="topVolunteersChart"></canvas>
            </div>
        </div>

        <?php if (!empty($days)): ?>
        <div class="charts-section">
            <div class="chart-card full-width">
                <h3><i class="fa-solid fa-calendar-week"></i> Application Activity by Day (Last 30 Days)</h3>
                <canvas id="dayChart"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <div class="insights-section">
            <div class="insight-card">
                <h3><i class="fa-solid fa-lightbulb"></i> Key Insights</h3>
                <ul class="insights-list">
                    <?php if ($overdueApplications > 0): ?>
                    <li class="insight-warning">
                        <i class="fa-solid fa-exclamation-circle"></i>
                        <?php echo $overdueApplications; ?> pending applications are older than 14 days
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($approvalRate >= 70): ?>
                    <li class="insight-success">
                        <i class="fa-solid fa-trophy"></i>
                        High approval rate of <?php echo $approvalRate; ?>%
                    </li>
                    <?php elseif ($approvalRate < 50): ?>
                    <li class="insight-warning">
                        <i class="fa-solid fa-chart-line-down"></i>
                        Low approval rate of <?php echo $approvalRate; ?>% - review criteria
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($taskCompletionRate >= 80): ?>
                    <li class="insight-success">
                        <i class="fa-solid fa-check-double"></i>
                        Excellent task completion rate of <?php echo $taskCompletionRate; ?>%
                    </li>
                    <?php elseif ($taskCompletionRate < 60): ?>
                    <li class="insight-warning">
                        <i class="fa-solid fa-tasks"></i>
                        Task completion rate of <?php echo $taskCompletionRate; ?>% needs improvement
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($recentApplications > 0): ?>
                    <li class="insight-info">
                        <i class="fa-solid fa-trending-up"></i>
                        <?php echo $recentApplications; ?> new applications in the last 30 days
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($animalsHelped > 0): ?>
                    <li class="insight-success">
                        <i class="fa-solid fa-paw"></i>
                        Volunteers have helped <?php echo $animalsHelped; ?> different animals
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($avgTasksPerVolunteer > 0): ?>
                    <li class="insight-info">
                        <i class="fa-solid fa-calculator"></i>
                        Average of <?php echo $avgTasksPerVolunteer; ?> tasks per volunteer
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($activeVolunteers > 0): ?>
                    <li class="insight-info">
                        <i class="fa-solid fa-user-friends"></i>
                        <?php echo $activeVolunteers; ?> highly engaged volunteers (5+ completed tasks)
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="insight-card">
                <h3><i class="fa-solid fa-chart-column"></i> Additional Statistics</h3>
                <div class="stats-grid-small">
                    <div class="stat-item">
                        <span class="stat-label">Active Tasks:</span>
                        <span class="stat-value"><?php echo $activeTasks; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Completed Tasks:</span>
                        <span class="stat-value"><?php echo $completedTasks; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Animals Helped:</span>
                        <span class="stat-value"><?php echo $animalsHelped; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Avg Tasks/Volunteer:</span>
                        <span class="stat-value"><?php echo $avgTasksPerVolunteer; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data from PHP
        const statuses = <?php echo json_encode($statuses); ?>;
        const statusCounts = <?php echo json_encode($statusCounts); ?>;
        const months = <?php echo json_encode($months); ?>;
        const monthCounts = <?php echo json_encode($monthCounts); ?>;
        const taskTypes = <?php echo json_encode($taskTypes); ?>;
        const taskTypeCounts = <?php echo json_encode($taskTypeCounts); ?>;
        const topVolunteerNames = <?php echo json_encode($topVolunteerNames); ?>;
        const topVolunteerTasks = <?php echo json_encode($topVolunteerTasks); ?>;
        const completedTasks = <?php echo json_encode($completedTasks); ?>;
        const activeTasks = <?php echo json_encode($activeTasks); ?>;
        const days = <?php echo json_encode($days); ?>;
        const dayCounts = <?php echo json_encode($dayCounts); ?>;
    </script>
    <script src="volunteer analytics.js"></script>
</body>
</html>