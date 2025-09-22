<?php
require_once "../databaseConnection.php";

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

// display totals
echo "Total Reports: $totalReports<br>";
echo "Total Animals: $totalAnimals<br>";
echo "Recent Procedures: $recentProcedures<br>";
echo "Total Euthanised: $totalEuthanised<br>"; 

?>