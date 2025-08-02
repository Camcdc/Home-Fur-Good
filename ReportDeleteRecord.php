<?php
include 'databaseconnection.php';

if(isset($_GET['crueltyID'])){
    $Report_ID=$_GET['crueltyID'];
    $sql_delete="DELETE FROM cruelty_report WHERE crueltyID='$Report_ID'";

    $query_delete=$conn->query($sql_delete);

    if($query_delete)
    header("Location:displayAllrecords.php");

    $conn->close();

}else{
    echo "<script>alert('Record could not be deleted. Retry')</script>";
    header("Location:displayAllrecords.php");
}

$conn->close();
?>