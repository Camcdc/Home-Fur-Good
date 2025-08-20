<?php

include '../databaseConnection.php';

if(isset($_REQUEST['animalID'])){
    $animalID = $_REQUEST['animalID'];
    $sql_delete = "DELETE FROM animal WHERE animalID = '$animalID'";

    $query_delete =$conn->query($sql_delete);

    if($query_delete){
        header("Location:viewAllAnimals.php");
        $conn->close();
    }else{
        echo "<script>alert('Record could not be deleted. Retry')</script>";
        header("Location:viewAllAnimals.php");
    }
}

$conn->close();

?>