<?php

// declaring variables
$serverName = "is3-dev.ict.ru.ac.za";
$user = "HyperVipers";
$password = "Hyp3rV1p3r";
$database = "hypervipers";

$currenttime= date('d/m/Y H:i:sa');

// OOP statement
$conn = new mysqli($serverName, $user, $password, $database);

if($conn->connect_error){
    die("Connection to the server and database failed" .$conn->connect_error);
} else {
    echo "Successfully connected to server on" . $currenttime;
}

?>