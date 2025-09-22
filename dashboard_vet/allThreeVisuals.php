<div class="container"> 

<h1>Reports and Analytics</h1>

<?php
require_once "../databaseConnection.php";
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Veterinarian') {
    // Redirect to homepage if not logged in or not a vet
    header("Location: ../landing pages/homepage.php");
    exit;
}

// 1. Fetch number of procedures (for Pie & Bar charts)
$procedureQuery = "
    SELECT medicalprocedure.procedureName, COUNT(*) AS total
    FROM medicalprocedure
    JOIN medical_report ON medical_report.procedureID = medicalprocedure.procedureID
    WHERE isDeleted = 0
    GROUP BY procedureName
    ORDER BY total DESC
    
";
$procedureResult = $conn->query($procedureQuery);

$procedureData = [['Procedure', 'Total']];
if ($procedureResult->num_rows > 0) {
    while ($row = $procedureResult->fetch_assoc()) {
        $procedureData[] = [$row['procedureName'], (int)$row['total']];
    }
}

// 2. Fetch number of animals per status (for Donut chart)
$statusQuery = "
    SELECT status, COUNT(*) AS total
    FROM animal
    WHERE isDeleted = 0
    GROUP BY status
";
$statusResult = $conn->query($statusQuery);

$statusData = [['Status', 'Total']];
if ($statusResult->num_rows > 0) {
    while ($row = $statusResult->fetch_assoc()) {
        $statusData[] = [$row['status'], (int)$row['total']];
    }
}

// Total Reports
$totalReportsResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM medical_report WHERE isDeleted = 0");

if (!$totalReportsResult) {
    die("Total Reports query failed: " . mysqli_error($conn));
}

$totalReports = mysqli_fetch_assoc($totalReportsResult)['total'] ?? 0;

// Total Animals
$totalAnimalsResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM animal WHERE isDeleted = 0");

if (!$totalAnimalsResult) {
    die("Total Animals query failed: " . mysqli_error($conn));
}

$totalAnimals = mysqli_fetch_assoc($totalAnimalsResult)['total'] ?? 0;

// Total recent rocedures in the last 7 days
$recentProceduresResult = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM medical_report
    WHERE reportDate BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AND CURRENT_DATE
      AND isDeleted = 0
");

if (!$recentProceduresResult) {
    die("Recent Procedures query failed: " . mysqli_error($conn));
}

$recentProcedures = mysqli_fetch_assoc($recentProceduresResult)['total'] ?? 0;

// Total Animals Euthanised 
$totalEuthanisedResult = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM medical_report
    WHERE procedureID IN (
        SELECT procedureID 
        FROM medicalprocedure
        WHERE procedureName = 'Euthanasia'
    )
    AND isDeleted = 0
");

if (!$totalEuthanisedResult) {
    die("Euthanised query failed: " . mysqli_error($conn));
}

$totalEuthanised = mysqli_fetch_assoc($totalEuthanisedResult)['total'] ?? 0;

$conn->close();



?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <link rel="stylesheet" href="sidebar_vet.css">
    <link rel="stylesheet" href="medicalReport.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php
include 'sidebar_vet.php';
?>


<div class="stats-cards">
    <!-- Total Reports -->
    <div class="stats-card">
        <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
        <h2>Total Reports</h2>
        <p class="stat-number"><?php echo $totalReports; ?></p>
    </div>

    <!-- Total Animals -->
    <div class="stats-card">
        <div class="stat-icon"><i class="fa-solid fa-paw"></i></div>
        <h2>Total Animals</h2>
        <p class="stat-number"><?php echo $totalAnimals; ?></p>
    </div>

    <!-- Recent Procedures -->
    <div class="stats-card">
        <div class="stat-icon"><i class="fa-solid fa-syringe"></i></div>
        <h2>Recent Procedures</h2>
        <p class="stat-number"><?php echo $recentProcedures; ?></p>
    </div>

    <!-- Total Euthanised -->
    <div class="stats-card">
        <div class="stat-icon"><i class="fa-solid fa-skull"></i></div>
        <h2>Total Euthanised</h2>
        <p class="stat-number"><?php echo $totalEuthanised; ?></p>
    </div>
</div>


</body>
</html>


<!-- Chart -->
<div class="charts-container">
    <div class="pie-donut-group">
        <div id="piechart"></div>
        <div id="donutchart"></div>
    </div>
    <div class="bar-chart-container">
        <div id="barchart"></div>
    </div>
</div>

<!--get Google Charts-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
    // PIE CHART
    var pieData = google.visualization.arrayToDataTable(<?php echo json_encode($procedureData); ?>);
    var pieOptions = {
        title: 'Number of Procedures by Type',
        legend: { position: 'bottom' }
    };
    var pieChart = new google.visualization.PieChart(document.getElementById('piechart'));
    pieChart.draw(pieData, pieOptions);

    // DONUT CHART
    var donutData = google.visualization.arrayToDataTable(<?php echo json_encode($statusData); ?>);
    var donutOptions = {
        title: 'Adoption Eligibility',
        pieHole: 0.4,
        colors: ['#4CAF50', '#F44336'],
        legend: { position: 'bottom' }
    };
    var donutChart = new google.visualization.PieChart(document.getElementById('donutchart'));
    donutChart.draw(donutData, donutOptions);

    // BAR CHART

    var barData = google.visualization.arrayToDataTable(<?php echo json_encode($procedureData); ?>);
    var barOptions = {
        title: 'Number of Procedures per Day',
        legend: { position: 'none' },
        hAxis: { title: 'Procedure Type' },
        vAxis: { title: 'Number of Procedures per Day' },
        colors: ['#4CAF50'],
        
    };

    var barChart = new google.visualization.ColumnChart(document.getElementById('barchart'));
    barChart.draw(barData, barOptions);
}
</script>