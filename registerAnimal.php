<?php

    include 'databaseConnection.php';


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


    $sql1 = "INSERT INTO animal(animalID, kennelID, statusID, name, species, breed, age, sex, colour, size)
            VALUES('$animalID','$kennelID', '$statusID', '$name','$species','$breed', '$age','$sex','$colour','$size')";

    echo "<br>";
    
    $result = $conn->query($sql1);
    if($result==false){
        die("Unable to create the record. A similar record may exist");
    }else {
        echo "<br> Record successfully created";
    }
    echo "SQL Query: $sql1<br>";


?>
