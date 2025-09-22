<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }

    include '../databaseConnection.php';


    $kennelID = $_POST['kennelID'];
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $colour = $_POST['colour'];
    $size = $_POST['size'];
    $rescueCircumstance = $_POST['rescueCircumstance'];
    $healthStatus = $_POST['healthStatus'];
    $picture = $_FILES['picture']['name'];
    $description = $_POST['description'];

    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
    
    //Get the original extension
    $fileExtension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);

    //Generate a new filename with timestamp
    $timestamp = date("Ymd_His"); // Year Month Day _ Hour Minute Second
    $picture = "animal_" . $timestamp . "." . $fileExtension;

    //Save the file
    move_uploaded_file($_FILES['picture']['tmp_name'], "../pictures/animals/" . $picture);
    }

    $sql0 = "SELECT * FROM kennel";
    $result0 = $conn->query($sql0);

    $sql0 = "SELECT * FROM kennel WHERE kennelID = '$kennelID'";
$result0 = $conn->query($sql0);

if ($result0 && $result0->num_rows > 0) {
    $row = $result0->fetch_assoc();
    $sizeRequirement = $row['sizeRequirement'];

    if ($sizeRequirement !== $size) {
        die("Kennel does not meet requirements for size of animal");
    } else {
        $sql1 = "INSERT INTO animal(
            kennelID, name, species, breed, age, sex, colour, size, picture, description, rescueCircumstance, healthStatus
        ) VALUES (
            '$kennelID', '$name', '$species', '$breed', '$age', '$sex', '$colour', '$size', '$picture', '$description', '$rescueCircumstance', '$healthStatus'
        )";

        $result = $conn->query($sql1);

        if ($result == false) { // if animal already exists or insert failed
            header("Location: animalRegistrationForm.php?error=1");
            exit();
        } else { // if animal is successfully created
            include_once 'occupancyUpdate.php';
            updateKennelOccupancy($conn, $kennelID);
            header("Location: animalRegistrationForm.php?success=1");
            exit();
        }
    }
} else {
    die("Selected kennel not found.");
}



    /*$sql1 = "INSERT INTO animal(
            kennelID, name, species, breed, age, sex, colour, size, picture, description, rescueCircumstance, healthStatus
        ) VALUES (
            '$kennelID', '$name', '$species', '$breed', '$age', '$sex', '$colour', '$size', '$picture', '$description', '$rescueCircumstance', '$healthStatus'
        )";

    $result = $conn->query($sql1);

    if($result == false){ // if animal already exists
        header("Location: animalRegistrationForm.php?error=1");
        exit();
    } else { // if animal is successfully created
        
        include_once 'occupancyUpdate.php';
        updateKennelOccupancy($conn, $kennelID);
        header("Location: animalRegistrationForm.php?success=1");
        exit();
    }*/

?>
