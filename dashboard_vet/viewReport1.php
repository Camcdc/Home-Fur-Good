<?php
require_once '../databaseConnection.php';


if (!isset($_GET['reportID']) || empty($_GET['reportID'])) {
    header("Location: animalReports.php");
    exit();
}

$reportID = intval($_GET['reportID']);

// Fetch full report details
$stmt = $conn->prepare("
    SELECT medical_report.reportID,
           medical_report.reportDate,
           medical_report.diagnosis,
           medical_report.medication,
           medical_report.notes,
           user.Fname,
           user.Sname,
           animal.name AS animalName,
           animal.species,
           animal.age,
           medicalprocedure.procedureName,
           medical_report.animalID
    FROM medical_report, user, animal, medicalprocedure
    WHERE medical_report.reportID = ?
      AND medical_report.userID = user.userID
      AND medical_report.animalID = animal.animalID
      AND medical_report.procedureID = medicalprocedure.procedureID
      AND medical_report.isDeleted = 0
    LIMIT 1
");

$stmt->bind_param("i", $reportID);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 0) {
    
    $noAnimalID = isset($_GET['animalID']) ? intval($_GET['animalID']) : 0;
    header("Location: animalReports.php?animalID=" . $noAnimalID);
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <title>View Medical Report</title>
    <link rel="stylesheet" href="viewReport.css">
    <link rel="stylesheet" href="sidebar_vet.css">

</head>
<body>
<?php
include 'sidebar_vet.php';
?>

    <div class="report-container">
        <h1>Medical Report Details</h1>
        <div>
            <div class="label">Report ID:</div>
            <div class="value"><?= htmlspecialchars($row['reportID']); ?></div>
        </div>

        <div>
            <div class="label">Report Date:</div>
            <div class="value"><?= htmlspecialchars($row['reportDate']); ?></div>
        </div>

        <div>
            <div class="label">Procedure:</div>
            <div class="value"><?= htmlspecialchars($row['procedureName']); ?></div>
        </div>

        <div>
            <div class="label">Animal Name:</div>
            <div class="value"><?= htmlspecialchars($row['animalName']); ?></div>
        </div>

        <div>
            <div class="label">Species:</div>
            <div class="value"><?= htmlspecialchars($row['species']); ?></div>
        </div>

        <div>
            <div class="label">Age:</div>
            <div class="value"><?= htmlspecialchars($row['age']); ?></div>
        </div>

        <div class="full-width">
            <div class="label">Staff:</div>
            <div class="value"><?= htmlspecialchars($row['Fname'] . " " . $row['Sname']); ?></div>
        </div>

        <div>
            <div class="label">Diagnosis:</div>
            <div class="value"><?= htmlspecialchars($row['diagnosis']); ?></div>
        </div>

        <div>
            <div class="label">Medication:</div>
            <div class="value"><?= htmlspecialchars($row['medication']); ?></div>
        </div>

        <div>
            <div class="label">Notes:</div>
            <div class="value"><?= htmlspecialchars($row['notes']); ?></div>
        </div>

        <div class="actions">
            <a href="animalReports.php?animalID=<?php echo $row['animalID']; ?>" class="back-button">
            Back to Animal Reports
            </a>

        </div>
    </div>
</body>

</html>

<?php $conn->close(); ?>