<?php
require_once '../databaseConnection.php';
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Veterinarian') {
    // Redirect to homepage if not logged in or not a vet
    header("Location: ../landing pages/homepage.php");
    exit;
}
$animalIDURL = isset($_GET['animalID']) ? intval($_GET['animalID']) : null;

$procedureID = $animalID = $reportDate = $userID = $diagnosis = $medication = $notes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $procedureID = $_POST['procedureID'];
    $animalID = $_POST['animalID'];
    $reportDate = $_POST['reportDate'];
    $userID = $_POST['userID'];
    $diagnosis = $_POST['diagnosis'];
    $medication = $_POST['medication'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO medical_report
        (procedureID, animalID, reportDate, userID, diagnosis, medication, notes, isDeleted)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("iisssss", $procedureID, $animalID, $reportDate, $userID, $diagnosis, $medication, $notes);

    if ($stmt->execute()) {
        $reportID = $stmt->insert_id;
        header("Location: viewReport1.php?reportID=" . $reportID);
        exit();
    } else {
        $error = "Failed to create medical report. Please try again.";
    }
}

$procedureResult = $conn->query("SELECT procedureID, procedureName FROM medicalprocedure");
$animalResult = $conn->query("SELECT animalID, name FROM animal WHERE isDeleted = 0");
$userResult = $conn->query("SELECT userID, Fname, Sname FROM user WHERE Role='Veterinarian' AND isDeleted=0");

if ($animalIDURL) {
    $stmt = $conn->prepare("SELECT name FROM animal WHERE animalID = ?");
    $stmt->bind_param("i", $animalIDURL);
    $stmt->execute();
    $result = $stmt->get_result();
    $animal = $result->fetch_assoc();
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <title>Create Medical Report</title>
    <link rel="stylesheet" href="sidebar_vet.css">
    <link rel="stylesheet" href="createReport.css">
</head>
<body>
<?php
include 'sidebar_vet.php';
?>
<div class="form-wrapper">
    <h1>Create Medical Report</h1>

    <?php if (isset($error)) {
        echo "<p class='form-error'>" . htmlspecialchars($error) . "</p>";
    } ?>

    <form method="POST">

        <div class="form-group">
            <label>Procedure:</label>
            <select name="procedureID" required>
                <?php while ($procedureOutput = $procedureResult->fetch_assoc()) { ?>
                    <option value="<?= htmlspecialchars($procedureOutput['procedureID']) ?>"
                        <?= $procedureOutput['procedureID'] == $procedureID ? "selected" : "" ?>>
                        <?= htmlspecialchars($procedureOutput['procedureName']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Animal:</label>
            <?php if ($animalIDURL) { ?>
                <input type="text" value="<?= htmlspecialchars($animal['name']) ?>" disabled>
                <input type="hidden" name="animalID" value="<?= $animalIDURL ?>">
            <?php } else { ?>
                <select name="animalID" required>
                    <?php while ($animalOutput = $animalResult->fetch_assoc()) { ?>
                        <option value="<?= htmlspecialchars($animalOutput['animalID']) ?>"
                            <?= $animalOutput['animalID'] == $animalID ? "selected" : "" ?>>
                            <?= htmlspecialchars($animalOutput['name']) ?>
                        </option>
                    <?php } ?>
                </select>
            <?php } ?>
        </div>

        <div class="form-group">
            <label>Date:</label>
            <input type="date" name="reportDate" value="<?= htmlspecialchars($reportDate) ?>" required>
        </div>

        <div class="form-group">
            <label>Staff:</label>
            <select name="userID" required>
                <?php while ($userOutput = $userResult->fetch_assoc()) { ?>
                    <option value="<?= htmlspecialchars($userOutput['userID']) ?>"
                        <?= $userOutput['userID'] == $userID ? "selected" : "" ?>>
                        <?= htmlspecialchars($userOutput['Fname'] . " " . $userOutput['Sname']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Diagnosis:</label>
            <textarea name="diagnosis"><?= htmlspecialchars($diagnosis) ?></textarea>
        </div>

        <div class="form-group">
            <label>Medication:</label>
            <textarea name="medication"><?= htmlspecialchars($medication) ?></textarea>
        </div>

        <div class="form-group">
            <label>Notes:</label>
            <textarea name="notes"><?= htmlspecialchars($notes) ?></textarea>
        </div>

        <div class="button-group">
            <button type="submit">Save Medical Report</button>
            <a class="form-button" href="animalReports.php?animalID=<?= $animalIDURL ?>">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
