<?php

    include '../databaseConnection.php';


    $animalID = $_POST['animalID'];
    $kennelID = $_POST['kennelID'];
    $statusID = $_POST['statusID'];
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $colour = $_POST['colour'];
    $size = $_POST['size'];
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




    $sql1 = "INSERT INTO animal(kennelID, name, species, breed, age, sex, colour, size, picture, description)
            VALUES('$kennelID', '$name','$species','$breed', '$age','$sex','$colour','$size','$picture', '$description')";

    echo "<br>";
    
    $result = $conn->query($sql1);
    if($result==false){ //if animal already exists
        header("Location: animalRegistrationForm.php?error=1"); //will redirect to registration form (with error[URL])
    }else { //if animal is successfully created
        include_once 'occupancyUpdate.php';
        updateKennelOccupancy($conn, $kennelID);
        header("Location: animalRegistrationForm.php?success=1"); //will redirect to registration form (with success[URL])
    }
    echo "SQL Query: $sql1<br>";


?>
