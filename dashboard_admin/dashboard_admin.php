<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}

// Check if user is logged in and is an Administrator
if (!isset($_SESSION['userID']) || strtolower($_SESSION['Role']) !== 'administrator') {
    echo "<script>
            alert('Please login as an Administrator to view this page');
            window.location.href = '../landing pages/browseAnimals.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="dashboard_admin.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>

<body>

<?php 
    include 'sidebar_admin.php';
    include '../databaseConnection.php';

    // STATISTIC QUERIES
    $sql1 = "SELECT COUNT(*) AS 'Total Animals' FROM animal WHERE isDeleted = '0'";
    $sql2 = "SELECT COUNT(*) AS 'Total Applications' FROM hypervipers.application WHERE isDeleted = '0' and applicationStatus = 'Pending'";
    $sql3 = "SELECT COUNT(*) AS 'Total Reports' FROM cruelty_report";

    $result1 = $conn->query($sql1);
    $result2 = $conn->query($sql2);
    $result3 = $conn->query($sql3);

    // Check for new cruelty reports
    $newReportQuery = "SELECT createDate, userID, location, detailedDescription, investigationStatus
                        FROM cruelty_report
                        WHERE isDeleted = 0
                        ORDER BY createDate DESC LIMIT 1";
  
    $newReportResult = $conn->query($newReportQuery);
    $newReportNotification = '';

    if ($newReportResult && $newReportResult->num_rows > 0) {
        $newReport = $newReportResult->fetch_assoc();
        $newReportNotification = '<div class="admin-notification">New Cruelty Report submitted at ' . htmlspecialchars($newReport['location']) . ' on ' . htmlspecialchars($newReport['createDate']) . '.</div>';
    }
?>

<div class="container">
    <?php if (!empty($newReportNotification)) echo $newReportNotification; ?>

    <div class="dashboard-heading">
        <h3>Dashboard</h3>
    </div>

    <div class="dashboard-container">

        <div class="stat-cards">
            <div class="card">
                <h3>Total Animals</h3>
                <p><?php if ($row=$result1->fetch_assoc()){ echo $row['Total Animals']; }?></p>
            </div>

            <div class="card">
                <h3>Pending Applications</h3>
                <p><?php if ($row=$result2->fetch_assoc()){ echo $row['Total Applications']; }?></p>
            </div>

            <div class="card">
                <h3>Pending Reports</h3>
                <p><?php if ($row=$result3->fetch_assoc()){ echo $row['Total Reports']; }?></p>
            </div>
        </div>

        <div class="dashboard-section">
            <!-- RECENT ANIMALS -->
            <div class="section">
                <div class="section-header">
                    <h4>Recently Animals</h4>
                    <div class="buttons">
                        <a href="animalRegistrationForm.php" class="add-btn">
                            <span class="material-symbols-outlined">add</span>
                            <span class="btn-text">Add Animal</span>
                        </a>
                        <a href="viewAllAnimals.php" class="add-btn">
                            <span class="material-symbols-outlined">menu</span>
                            <span class="btn-text">View Animals</span>
                        </a>
                    </div>
                </div>
                <div class="section-list">
                    <?php
                    $query1 = "SELECT name, species, breed, age, intakeDate FROM animal WHERE isDeleted = 0 ORDER BY intakeDate DESC LIMIT 5";
                    $result1 = $conn->query($query1);

                    if ($result1 && $result1->num_rows > 0) {
                        while ($row1 = $result1->fetch_assoc()) {
                            echo '<div class="list-item">';
                            echo '<span class="item-title">' . htmlspecialchars($row1['name']) . '<br>' .'</span>';
                            echo '<span class="item-subtitle">' . htmlspecialchars($row1['species']) . '  ' . htmlspecialchars($row1['breed']) . '  ' . htmlspecialchars($row1['age']) . ' yrs</span>';
                            echo '<span class="item-date">Added: ' . htmlspecialchars($row1['intakeDate']) . '</span>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="list-item"><span class="item-subtitle">No animals found.</span></div>';
                    }
                    ?>
                </div>
            </div>

            <!-- RECENT APPLICATIONS -->
            <div class="section">
                <div class="section-header">
                    <h4>Recent Applications</h4>
                    <div class="buttons">
                        <a href="displayAllrecords.php" class="add-btn">
                            <span class="material-symbols-outlined">menu</span>
                            <span class="btn-text">View Applications</span>
                        </a>
                    </div>
                </div>
                <div class="section-list">
                    <?php
                    $query3 = "SELECT applicationID, userID, animalID, createDate, applicationStatus FROM application WHERE isDeleted = 0 ORDER BY createDate DESC LIMIT 5";
                    $result3 = $conn->query($query3);

                    if ($result3 && $result3->num_rows > 0) {
                        while ($row3 = $result3->fetch_assoc()) {
                            echo '<div class="list-item">';
                            echo '<span class="item-title">' . htmlspecialchars($row3['applicationID']) . '<br>' .'</span>';
                            echo '<span class="item-subtitle">' . htmlspecialchars($row3['userID']).'</span>';
                            echo '<span class="item-date">' . htmlspecialchars($row3['animalID']) . '</span>';
                            echo '<span class="item-date">' . htmlspecialchars($row3['applicationStatus']) . '</span>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="list-item"><span class="item-subtitle">No reports found.</span></div>';
                    }
                    ?>
                </div>
            </div>

            <!-- RECENT REPORTS -->
            <div class="section">
                <div class="section-header">
                    <h4>Recent Reports</h4>
                    <div class="buttons">
                        <a href="displayAllrecords.php" class="add-btn">
                            <span class="material-symbols-outlined">menu</span>
                            <span class="btn-text">View Reports</span>
                        </a>
                    </div>
                </div>
                <div class="section-list">
                    <?php
                    // Query to fetch recent cruelty reports without JOIN
                    $query2 = "SELECT crueltyReportID, userID, detailedDescription, location, createDate, investigationStatus, 
                                      rescueCircumstance, picture, completedDate
                               FROM cruelty_report 
                               WHERE isDeleted = 0
                               ORDER BY createDate DESC LIMIT 5"; // Get the 5 most recent reports

                    $result2 = $conn->query($query2);

                    if ($result2 && $result2->num_rows > 0) {
                        while ($row2 = $result2->fetch_assoc()) {
                            // Displaying report details
                            echo '<div class="list-item">';
                            echo '<span class="item-title">' . htmlspecialchars($row2['userID']) . '</span>';
                            echo '<span class="item-subtitle">Location: ' . htmlspecialchars($row2['location']) . '</span>';
                            echo '<span class="item-date">Reported: ' . htmlspecialchars($row2['createDate']) . '</span>';
                            echo '<span class="item-subtitle">Status: ' . htmlspecialchars($row2['investigationStatus']) . '</span>';
                            echo '<span class="item-subtitle">Rescue Circumstance: ' . htmlspecialchars($row2['rescueCircumstance']) . '</span>';
                            echo '<span class="item-subtitle">Completed Date: ' . ($row2['completedDate'] ? htmlspecialchars($row2['completedDate']) : 'N/A') . '</span>';

                            echo '</div>';
                        }
                    } else {
                        echo '<div class="list-item"><span class="item-subtitle">No reports found.</span></div>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>
