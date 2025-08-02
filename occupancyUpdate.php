<?php

//FUNCTION TO UPDATE THE OCCUPANCY IN THE ANIMAL REGISTRATION FORM
function updateKennelOccupancy($conn, $kennelID) {
    $check = $conn->prepare("SELECT occupation, capacity FROM kennel WHERE kennelID = ?"); //? = placeholder
    $check->bind_param("s", $kennelID);//s = string //assigns kennelID to the '?' on check statement
    $check->execute(); //executes the check statement
    $result = $check->get_result(); //gets check result data

    if ($result && $row = $result->fetch_assoc()) {
        if ($row['occupation'] < $row['capacity']) { //if occuption is less than capacity
            $update = $conn->prepare("UPDATE kennel SET occupation = occupation + 1 WHERE kennelID = ?");//prepare an increment of the occupation
            $update->bind_param("s", $kennelID);
            $update->execute();
        }
    }
}
?>
