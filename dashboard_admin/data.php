<?php
include '../databaseConnection.php'; 
header('Content-Type: application/json');

// KENNEL OCCUPANCY BY SPECIES
$sql1 = "SELECT kennelType, SUM(occupation) as occupied, SUM(capacity - occupation) as available 
         FROM kennel 
         GROUP BY kennelType";
$result1 = $conn->query($sql1);
if (!$result1) {
    die('Error with query 1: ' . $conn->error);
}

$kennelByType = [["Animal Type", "Occupied", "Available"]];
while ($row = $result1->fetch_assoc()) {
    $kennelByType[] = [
        $row['kennelType'],
        (int)$row['occupied'],
        (int)$row['available']
    ];
}
$data["kennelByType"] = $kennelByType;

// ANIMAL INTAKE BY RESCUE CIRCUMSTANCE
$sql2 = "SELECT rescueCircumstance, COUNT(*) as Total 
        FROM animal
        GROUP BY rescueCircumstance";
$result2 = $conn->query($sql2);
if (!$result2) {
    die('Error with query 2: ' . $conn->error);
}

$rescueCircumstanceByType = [["Rescue Circumstance", "Total"]];
while($row = $result2->fetch_assoc()){
    $rescueCircumstanceByType[] = [
        $row['rescueCircumstance'],
        (int)$row['Total']
    ];
}
$data["rescueCircumstanceByType"] = $rescueCircumstanceByType;

// ANIMAL INTAKE BY MONTH
$sqlIntake = "
    SELECT DATE_FORMAT(intakeDate, '%Y-%m') as month, COUNT(*) as totalIntake
    FROM animal
    GROUP BY month
    ORDER BY month
";

$resultIntake = $conn->query($sqlIntake);

$intakeData = [["Month", "Intake"]];
while($row = $resultIntake->fetch_assoc()){
    $intakeData[] = [
        $row['month'],
        (int)$row['totalIntake']
    ];
}

// Adoption by Month
$sqlAdoption = "
    SELECT DATE_FORMAT(adoptionDate, '%Y-%m') as month, COUNT(*) as totalAdopted
    FROM adoption
    GROUP BY month
    ORDER BY month
";

$resultAdoption = $conn->query($sqlAdoption);

$adoptionData = [["Month", "Adopted"]];
while($row = $resultAdoption->fetch_assoc()){
    $adoptionData[] = [
        $row['month'],
        (int)$row['totalAdopted']
    ];
}

// Merge Data and Add Missing Months
$allMonths = [];
// Get unique months from both intake and adoption data
foreach (array_merge($intakeData, $adoptionData) as $entry) {
    if ($entry[0] !== 'Month') {  // Skip the header row
        $allMonths[] = $entry[0];
    }
}


$allMonths = array_unique($allMonths);
sort($allMonths);  // Sort months chronologically


$mergedIntakeAdoptionData = [["Month", "Intake", "Adopted"]];
foreach ($allMonths as $month) {
    $intakeCount = 0;
    $adoptedCount = 0;

    
    foreach ($intakeData as $entry) {
        if ($entry[0] == $month) {
            $intakeCount = $entry[1];
        }
    }

    foreach ($adoptionData as $entry) {
        if ($entry[0] == $month) {
            $adoptedCount = $entry[1];
        }
    }

    // Add the month data, even if it's zero for one of the categories
    $mergedIntakeAdoptionData[] = [$month, $intakeCount, $adoptedCount];
}

// Add the merged data to the output
$data["mergedIntakeAdoptionData"] = $mergedIntakeAdoptionData;

// Return the data as JSON
echo json_encode($data);

?>