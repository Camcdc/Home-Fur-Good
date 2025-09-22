<?php
require_once '../databaseConnection.php';
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
    // Redirect to homepage if not logged in or not a vet
    header("Location: ../landing pages/homepage.php");
    exit;
}

// TOTAL ANIMALS
$totalAnimals = "SELECT COUNT(*) AS total FROM animal WHERE isDeleted = 0";
$totalAnimalsResult = mysqli_query($conn, $totalAnimals);
if ($totalAnimalsResult) {
    $row = mysqli_fetch_assoc($totalAnimalsResult);
    $total = $row['total']; // total number of animals
} else {
    $total = 0;
}

// RECENT INTAKES (7 days)
$recentIntakes = "SELECT COUNT(*) AS totalRecents FROM animal WHERE isDeleted = 0 AND intakeDate >= NOW() - INTERVAL 7 DAY";
$recentIntakesResult = mysqli_query($conn, $recentIntakes);
if ($recentIntakesResult) {
    $recentRow = mysqli_fetch_assoc($recentIntakesResult);
    $totalrecent = $recentRow['totalRecents'];
} else {
    $totalrecent = 0;
}

// ADOPTION RATES
$totalAdopted = "SELECT COUNT(*) AS totalAdopted FROM adoption WHERE isDeleted = 0";
$totalAdoptedResult = mysqli_query($conn, $totalAdopted);
if ($totalAdoptedResult) {
    $totalRow = mysqli_fetch_assoc($totalAdoptedResult);
    $adopttotal = $totalRow['totalAdopted'];
} else {
    $adopttotal = 0;
}

// % OF ANIMALS AWAITING ADOPTION PROCEDURES
$adoptionProcedureRate = "SELECT COUNT(*) AS totalAwaiting FROM animal WHERE isDeleted = 0 AND status = 'Awaiting Eligibility'";
$adoptionProcedureRateResult = mysqli_query($conn, $adoptionProcedureRate);
if ($adoptionProcedureRateResult) {
    $resultTotalrow = mysqli_fetch_assoc($adoptionProcedureRateResult);
    $resultTotal = $resultTotalrow['totalAwaiting'];
} else {
    $resultTotal = 0;
}
$resultTotal = ($total > 0) ? ($resultTotal / $total) * 100 : 0;
$resultTotal = number_format($resultTotal, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | SPCA</title>
  <script src="https://www.gstatic.com/charts/loader.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="animalAnalytics.css">
  <link rel="stylesheet" href="sidebar_admin.css">
</head>
<body>
<?php include 'sidebar_admin.php'; ?>



<div class="container">
  <h1><i class="fa-solid fa-chart-line"></i> Animal Analytics Dashboard</h1>
    <!-- Key Performance Indicators -->
    <div class="stats-cards">
        <div class="stats-card">
            <div class="stat-icon"><i class="fa-solid fa-cat"></i></div>
            <h2>Total Animals</h2>
            <p class="stat-number"><?php echo $total; ?></p>
        </div>
        <div class="stats-card">
            <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
            <h2>Recent Intakes (7 days)</h2>
            <p class="stat-number"><?php echo $totalrecent; ?></p>
        </div>
        <div class="stats-card">
            <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
            <h2>Total Adopted Animals</h2>
            <p class="stat-number"><?php echo $adopttotal; ?></p>
        </div>
        <div class="stats-card">
            <div class="stat-icon"><i class="fa-solid fa-user-slash"></i></div>
            <h2>% Of Animals Awaiting Eligibility</h2>
            <p class="stat-number"><?php echo $resultTotal . "%"; ?></p>
        </div>
    </div>

    <!-- Primary Charts Row -->
    <div class="charts-section">
        <div class="chart-card">
            <h3><i class="fa-solid fa-chart-simple"></i></i>Kennel Occupancy</h3>
            <div class="chart-box" id="kennel_by_type"></div>
        </div>
        <div class="chart-card">
            <h3><i class="fa-solid fa-chart-pie"></i> Animal Intake by Rescue Circumstance</h3>
            <canvas id="rescueByCircumstanceChart" width="400" height="400"></canvas>
        </div>
    </div>

    <!-- Secondary Charts Row -->
    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-line"></i> Intake vs Adoption Over Time</h3>
        <div class="chart-box" id="intake_adoption_chart"></div>
    </div>
</div>

<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
    fetch('data.php')
      .then(response => response.json())
      .then(data => {

        // --- Kennel Occupancy (Bar Chart) ---
        const typeData = google.visualization.arrayToDataTable(data.kennelByType);
        const typeOptions = {
          isStacked: true,
          colors: ['#3a6fa0', '#B6CBDF'],
          legend: { position: 'bottom' },
          animation: {
            duration: 1500,  // Duration of the animation
            easing: 'out',   // Ease-out animation (smooth deceleration)
            startup: true     // Start animation when chart loads
          },
          hAxis: {
            title: 'Kennel Type',
            minValue: 0
          },
          vAxis: {
            title: 'Occupancy',
            minValue: 0
          }
        };
        const typeChart = new google.visualization.ColumnChart(
          document.getElementById('kennel_by_type')
        );
        typeChart.draw(typeData, typeOptions);

        // --- Animal Intake by Rescue Circumstance (Chart.js - Pie Chart) ---
        const rescueData = {
          labels: data.rescueCircumstanceByType.map(item => item[0]),  // Labels from first column
          datasets: [{
            data: data.rescueCircumstanceByType.map(item => item[1]), // Data from second column
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],  // You can customize colors here
            hoverBackgroundColor: ['#FF5A5F', '#36A2EB', '#FFCE56', '#4BC0C0']
          }]
        };

        const rescueOptions = {
          responsive: true,
          animation: {
            duration: 1500,
            easing: 'easeOutBounce',
            animateScale: true,
            animateRotate: true
          }
        };

        const ctx = document.getElementById('rescueByCircumstanceChart').getContext('2d');
        new Chart(ctx, {
          type: 'pie',
          data: rescueData,
          options: rescueOptions
        });

        // --- Intake vs Adoption Over Time (Line Chart) ---
        const intakeAdoptionDataTable = google.visualization.arrayToDataTable(data.mergedIntakeAdoptionData);
        const intakeAdoptionOptions = {
            curveType: 'function',
            legend: { position: 'bottom' },
            colors: ['#3a6fa0', '#34a853'],
            animation: {
              duration: 1500,   // Duration of the animation
              easing: 'out',    // Ease-out animation (smooth deceleration)
              startup: true      // Start animation when chart loads
            },
            hAxis: {
                title: 'Date',
                format: 'MMM dd',
            },
            vAxis: {
                title: 'Count',
                minValue: 0
            }
        };
        const intakeAdoptionChart = new google.visualization.LineChart(
            document.getElementById('intake_adoption_chart')
        );
        intakeAdoptionChart.draw(intakeAdoptionDataTable, intakeAdoptionOptions);
      }) 
      .catch(err => {
        console.error("Error fetching chart data:", err);
        alert("Failed to load dashboard data.");
      });
}

</script>

</body>
</html>
