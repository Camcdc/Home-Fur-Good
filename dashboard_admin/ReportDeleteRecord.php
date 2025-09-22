<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseConnection.php';

if(isset($_GET['crueltyReportID']) && is_numeric($_GET['crueltyReportID'])) {
    $Report_ID = intval($_GET['crueltyReportID']);

    // Use prepared statement for safety
    $stmt = $conn->prepare("UPDATE cruelty_report SET isDeleted = 1 WHERE crueltyReportID = ? AND investigationStatus = 'investigation complete'");
    $stmt->bind_param("i", $Report_ID);
    $stmt->execute();

    if($stmt->affected_rows > 0) {
        echo "<script>alert('✅ Record deleted successfully'); window.location.href='displayAllrecords.php';</script>";
    } else {
        echo "<script>alert('❌ Record could not be deleted. Either it does not exist or rescue is not closed.'); window.location.href='displayAllrecords.php';</script>";
    }

    $stmt->close();
    $conn->close();

} else {
    echo "<script>alert('❌ Invalid Record ID'); window.location.href='displayAllrecords.php';</script>";
    $conn->close();
}
?>
