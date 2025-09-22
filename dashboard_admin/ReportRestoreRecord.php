<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseConnection.php';

if(isset($_GET['crueltyReportID'])){
    $Report_ID=$_GET['crueltyReportID'];
    $sql_delete="Update cruelty_report set isDeleted=0 where crueltyReportID=$Report_ID";

    $query_delete=$conn->query($sql_delete);

    if($query_delete ){
        echo "<script>alert('✅ Record restored successfully')</script>";
        echo "<script>window.location.href='displayAllrecords.php'</script>";
    }

    $conn->close();

}else{
    echo "<script>alert('❌ Record could not be restored. Retry')</script>";
    echo "<script>window.location.href='displayAllrecords.php'</script>";
}

$conn->close();
?>