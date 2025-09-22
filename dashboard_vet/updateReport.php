<?php
require_once '../databaseConnection.php';

if (!isset($_GET['reportID']) || empty($_GET['reportID'])) {
    header("Location: animalReports.php");
    exit();
}

$reportID = intval($_GET['reportID']);


$procedureID = $animalID = $reportDate = $userID = $diagnosis = $medication = $notes = "";


$stmt = $conn->prepare("SELECT reportID, procedureID, animalID, userID, reportDate, diagnosis, medication, notes 
                        FROM medical_report 
                        WHERE reportID = ? AND isDeleted = 0 
                        LIMIT 1");
$stmt->bind_param("i", $reportID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: animalReports.php?error=not_found");
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();


$procedureID = $row['procedureID'];
$animalID = $row['animalID'];
$reportDate = $row['reportDate'];
$userID = $row['userID'];
$diagnosis = $row['diagnosis'];
$medication = $row['medication'];
$notes = $row['notes'];

// If form is submitted, update the report
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $procedureID = $_POST['procedureID'];
    $animalID = $_POST['animalID'];
    $userID = $_POST['userID'];
    $reportDate = $_POST['reportDate'];
    $diagnosis = $_POST['diagnosis'];
    $medication = $_POST['medication'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("UPDATE medical_report 
                            SET procedureID = ?, animalID = ?, userID = ?, reportDate = ?, 
                                diagnosis = ?, medication = ?, notes = ?
                            WHERE reportID = ?");
    $stmt->bind_param("iiissssi", 
                      $procedureID, $animalID, $userID, $reportDate, 
                      $diagnosis,$medication, $notes, $reportID);

    if ($stmt->execute()) {
        header("Location: viewReport1.php?reportID=" . $reportID);
        exit();
    } else {
        $error = "Failed to update the medical report. Please try again.";
    }
}


$procedureResult = $conn->query("SELECT procedureID, procedureName FROM medicalprocedure");
$animalResult = $conn->query("SELECT animalID, name FROM animal WHERE isDeleted = 0");
$userResult = $conn->query("SELECT userID, Fname, Sname FROM user WHERE Role='Staff' AND isDeleted=0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <title>Edit Medical Report</title>
    <link rel="stylesheet" href="sidebar_vet.css">
    <link rel="stylesheet" href="updateReport.css">
</head>
<body>
<?php
include 'sidebar_vet.php';
?>
<div class="form-wrapper">
    <h1>Edit Medical Report</h1>

    <?php if (isset($error)) {
        echo "<p class='form-error'>" . htmlspecialchars($error) . "</p>";
    } ?>

    <form method="POST">

        <div class="form-group">
            <label for="procedureID">Procedure:</label>
            <select id="procedureID" name="procedureID" required>
                <?php while ($procedureOutput = $procedureResult->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($procedureOutput['procedureID']); ?>"
                        <?php if ($procedureOutput['procedureID'] == $procedureID) echo "selected"; ?>>
                        <?php echo htmlspecialchars($procedureOutput['procedureName']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="animalID">Animal:</label>
            <select id="animalID" name="animalID" required>
                <?php while ($animalOutput = $animalResult->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($animalOutput['animalID']); ?>"
                        <?php if ($animalOutput['animalID'] == $animalID) echo "selected"; ?>>
                        <?php echo htmlspecialchars($animalOutput['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="userID">Staff:</label>
            <select id="userID" name="userID" required>
                <?php while ($userOutput = $userResult->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($userOutput['userID']); ?>"
                        <?php if ($userOutput['userID'] == $userID) echo "selected"; ?>>
                        <?php echo htmlspecialchars($userOutput['Fname'] . " " . $userOutput['Sname']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="reportDate">Report Date:</label>
            <input type="date" id="reportDate" name="reportDate" value="<?php echo htmlspecialchars($reportDate); ?>" required>
        </div>

        <div class="form-group full-width">
            <label for="diagnosis">Diagnosis:</label>
            <textarea id="diagnosis" name="diagnosis"><?php echo htmlspecialchars($diagnosis); ?></textarea>
        </div>

        <div class="form-group full-width">
            <label for="medication">Medication:</label>
            <textarea id="medication" name="medication"><?php echo htmlspecialchars($medication); ?></textarea>
        </div>

        <div class="form-group full-width">
            <label for="notes">Notes:</label>
            <textarea id="notes" name="notes"><?php echo htmlspecialchars($notes); ?></textarea>
        </div>

        <div class="button-group">
            <button type="submit">Update Report</button>
            <a class="form-button" href="animalReports.php?animalID=<?php echo $animalID; ?>">Cancel</a>
        </div>

    </form>
</div>



</body>
</html>

<?php $conn->close(); ?>