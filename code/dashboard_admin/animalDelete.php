<?php

include '../databaseConnection.php';
include 'occupancyUpdate.php'; 

if (isset($_REQUEST['animalID'])) {
    $animalID = $_REQUEST['animalID'];

    // Get the kennelID of the animal 
    $getKennel = $conn->prepare("SELECT kennelID FROM animal WHERE animalID = ?");
    $getKennel->bind_param("i", $animalID);
    $getKennel->execute();
    $result = $getKennel->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $kennelID = $row['kennelID'];

        // Mark animal as deleted
        $sql_delete = "UPDATE animal SET isDeleted = '1' WHERE animalID = ?";
        $query_delete = $conn->prepare($sql_delete);
        $query_delete->bind_param("i", $animalID);

        if ($query_delete->execute()) {
            // Decrease kennel occupancy
            decreaseKennelOccupancy($conn, $kennelID);

            header("Location: viewAllAnimals.php");
            exit();
        } else {
            echo "<script>alert('Record could not be deleted. Retry')</script>";
            header("Location: viewAllAnimals.php");
            exit();
        }
    }
}

$conn->close();
?>
