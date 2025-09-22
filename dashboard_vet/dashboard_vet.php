<?php 
include '../databaseConnection.php';
session_start();
// Check if user is logged in and has the 'vet' role
if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Veterinarian') {
    // Redirect to homepage if not logged in or not a vet
    header("Location: ../landing pages/homepage.php");
    exit;
}

// Additional code for the protected page goes here...

// Recently registered animals (last 7 days, using intakeDate)
$recentAnimalsQuery = "SELECT COUNT(*) AS recent_count FROM animal WHERE intakeDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$recentResult = mysqli_query($conn, $recentAnimalsQuery);
$recentCount = ($recentResult && mysqli_num_rows($recentResult) > 0) ? mysqli_fetch_assoc($recentResult)['recent_count'] : 0;

// Total animals
$totalAnimalsQuery = "SELECT COUNT(*) AS total_animals FROM animal";
$totalAnimalsResult = mysqli_query($conn, $totalAnimalsQuery);
$totalAnimals = ($totalAnimalsResult && mysqli_num_rows($totalAnimalsResult) > 0) ? mysqli_fetch_assoc($totalAnimalsResult)['total_animals'] : 0;

// Total appointments
$totalAppointmentsQuery = "SELECT COUNT(*) AS total_appointments FROM medical_report";
$totalAppointmentsResult = mysqli_query($conn, $totalAppointmentsQuery);
$totalAppointments = ($totalAppointmentsResult && mysqli_num_rows($totalAppointmentsResult) > 0) ? mysqli_fetch_assoc($totalAppointmentsResult)['total_appointments'] : 0;

// Total medical reports
$totalReportsQuery = "SELECT COUNT(*) AS total_reports FROM medical_report";
$totalReportsResult = mysqli_query($conn, $totalReportsQuery);
$totalReports = ($totalReportsResult && mysqli_num_rows($totalReportsResult) > 0) ? mysqli_fetch_assoc($totalReportsResult)['total_reports'] : 0;

// Recent animals (last 5, using intakeDate)
$recentAnimalsListQuery = "SELECT name, species, intakeDate FROM animal ORDER BY intakeDate DESC LIMIT 5";
$recentAnimalsListResult = mysqli_query($conn, $recentAnimalsListQuery);

// Recent medical reports (last 5) with procedure name
$recentReportsListQuery = "SELECT * FROM medical_report ORDER BY reportDate DESC LIMIT 5
";
$recentReportsListResult = mysqli_query($conn, $recentReportsListQuery);

// Example: Animals needing follow-up (dummy logic, adjust as needed)
$followUpQuery = "SELECT name, species FROM animal WHERE needs_followup = 1 LIMIT 3";
$followUpResult = mysqli_query($conn, $followUpQuery);

// Today's appointments (animals with intakeDate today)
$todaysAppointmentsQuery = "SELECT COUNT(*) AS todays_appointments FROM animal WHERE DATE(intakeDate) = CURDATE()";
$todaysAppointmentsResult = mysqli_query($conn, $todaysAppointmentsQuery);
$todaysAppointments = ($todaysAppointmentsResult && mysqli_num_rows($todaysAppointmentsResult) > 0) ? mysqli_fetch_assoc($todaysAppointmentsResult)['todays_appointments'] : 0;

// Animals needing appointments (registered in last 7 days, no medical report, using intakeDate)
$newAnimalsNoReportQuery = "
    SELECT a.id, a.name, a.species, a.intakeDate
    FROM animal a
    LEFT JOIN medical_report r ON a.id = r.animal_id
    WHERE a.intakeDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
      AND r.id IS NULL
    ORDER BY a.intakeDate DESC
";
$newAnimalsNoReportResult = mysqli_query($conn, $newAnimalsNoReportQuery);
?>
<!DOCTYPE html>
<html lang="uk-eng">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <title>Vet Dashboard</title>
    <link rel="stylesheet" href="dashboard_vet.css">
    <link rel="stylesheet" href="sidebar_vet.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   
</head>
<body>
    <div class="container">
    <div class="dashboard-layout">
        <?php include 'sidebar_vet.php'; ?>
        <div class="container">
            <h1>Welcome to the Vet Dashboard</h1>
            <div class="welcome-msg">
                Here you can manage animals, appointments, and medical reports.
            </div>
            <?php if ($newAnimalsNoReportResult && mysqli_num_rows($newAnimalsNoReportResult) > 0): ?>
                <div class="dashboard-alert">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    New animals have been registered and require medical records:
                    <ul>
                        <?php while ($row = mysqli_fetch_assoc($newAnimalsNoReportResult)): ?>
                            <li>
                                <?php echo htmlspecialchars($row['name']); ?> (<?php echo htmlspecialchars($row['species']); ?>) - Registered: <?php echo htmlspecialchars($row['registration_date']); ?>
                                <a href="createReport1.php?animal_id=<?php echo $row['id']; ?>">Create Medical Report</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="stat-cards">
                <div class="card">
                    <h2>Recently Registered Animals</h2>
                    <p><strong><?php echo $recentCount; ?></strong></p>
                </div>
                <div class="card">
                    <h2>Total Animals</h2>
                    <p><strong><?php echo $totalAnimals; ?></strong></p>
                </div>
                <div class="card">
                    <h2>Total Medical Reports</h2>
                    <p><strong><?php echo $totalReports; ?></strong></p>
                </div>
            </div>
            <div class="dashboard-section">
                <div class="section">
                    <div class="section-header">
                        <span><i class="fa-solid fa-bolt"></i> Quick Actions</span>
                    </div>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a class="add-btn" href="createReport1.php">
                            <span class="material-symbols-outlined">add_circle</span>
                            <span class="btn-text">Create Medical Report</span>
                        </a>
                        <a class="add-btn" href="manageMedicalReport.php">
                            <span class="material-symbols-outlined">pets</span>
                            <span class="btn-text">View Animals</span>
                        </a>
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        <span><i class="fa-solid fa-clock-rotate-left"></i> Recent Animals</span>
                    </div>
                    <div class="section-list">
                        <?php if ($recentAnimalsListResult && mysqli_num_rows($recentAnimalsListResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($recentAnimalsListResult)): ?>
                                <div class="list-item">
                                    <div class="item-title"><?php echo htmlspecialchars($row['name']); ?> (<?php echo htmlspecialchars($row['species']); ?>)</div>
                                    <div class="item-date">Registered: <?php echo htmlspecialchars($row['intakeDate']); ?></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="list-item">No recent animals found.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="section">
                    <div class="section-header">
                        <span><i class="fa-solid fa-file-medical"></i> Recent Medical Reports</span>
                    </div>
                    <div class="section-list">
                        <?php if ($recentReportsListResult && mysqli_num_rows($recentReportsListResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($recentReportsListResult)): ?>
                                <div class="list-item">
                                    <div class="item-title">
                                        Animal ID: <?php echo htmlspecialchars($row['animalID']); ?> 
                                    </div>
                                    <div class="item-subtitle">
                                        Procedure: <?php echo htmlspecialchars($row['procedureID']); ?>
                                    </div>
                                    <div class="item-date">
                                        Created: <?php echo htmlspecialchars($row['reportDate']); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="list-item">No recent reports found.</div>
                        <?php endif; ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
?>
<html>

<head>
    <link rel="stylesheet" href="sidebar_vet.css">
</head>

<body>

