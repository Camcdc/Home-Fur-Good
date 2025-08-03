<!DOCTYPE html>
<html lang="en-UK">
<head>
<Title>Creating system user</Title>
<link rel="icon" type="ímage/x-icon" href="C:\Users\Somila\Downloads\a488be7e-a2d8-4e81-8565-a1faedd1ac38.png">
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php
include "Databaseconnection.php";//

$firstName = $_POST["firstName"];
$lastName = $_POST["lastName"];
$contactNumber = $_POST["contactNumber"];
$picture = $_POST["picture"];
$animalDetails  = $_POST["animalDetails"];
$location= $_POST["location"];

$sqli = "INSERT INTO cruelty_report(firstName, lastName, contactNumber, picture, animalDetails, `location`)
        VALUES ('$firstName','$lastName','$contactNumber','$picture','$animalDetails','$location')";

echo "<br>";
$result = $conn-> query($sqli);
if($result == FALSE){
    die("Unable to create the record. There is an exsiting reocrd");
}else{
    echo "<br> The record was successfully created";
     echo "</table></center>";
            echo"<p><a href= \"AddCrueltyForm.html\"><button>Edit another report</button></a></p>";
            echo" <p><a href= \"displayAllrecords.php\"><button>Display Cruelty Records </button></a></p>";
            echo"<p><a href=\"dogRecordSearchForm.html\"><button>Search a cruelty record</button></a></p>";
            echo" <p> <a href=\"loginForm.html\"><button>Back to Login</button></a></p>";
}
?>
