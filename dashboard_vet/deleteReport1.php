<?php
require_once '../databaseConnection.php';

if (!isset($_GET['reportID']) || empty($_GET['reportID'])) {
    header("Location: animalReports.php");
    exit();
}

$reportID = intval($_GET['reportID']);


$stmt = $conn->prepare("SELECT animalID FROM medical_report WHERE reportID = ? LIMIT 1");
$stmt->bind_param("i", $reportID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: animalReports.php?error=not_found");
    exit();
}

$row = $result->fetch_assoc();
$animalID = $row['animalID'];
$stmt->close();

// Soft delete the report
$stmt = $conn->prepare("UPDATE medical_report SET isDeleted = 1 WHERE reportID = ?");
$stmt->bind_param("i", $reportID);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: animalReports.php?animalID=" . $animalID . "&msg=deleted");
    exit();
} else {
    $stmt->close();
    header("Location: animalReports.php?animalID=" . $animalID . "&error=delete_failed");
    exit();
}

$conn->close();