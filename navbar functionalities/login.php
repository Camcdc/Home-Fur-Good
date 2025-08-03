<?php

session_start();

//gets the values from form
$email = $_POST['loginEmail'];
$loginpassword = $_POST['loginPassword'];



echo "Trying login with email: '$email' and password: '$loginpassword'<br>";

//database variables
require_once('../databaseConnection.php');

//connecting to the database
$conn = new mysqli($serverName, $user, $password, $database);

//check if successfully connected
if($conn->connect_error){
    die("<p class=\"error\">Connection to database failed!</p>");
}


//query instructions
$sql = "SELECT * FROM user WHERE Email = '$email' AND Password = '$loginpassword'";
$result = $conn->query($sql);

//check if the query was successful
if($result === FALSE){
    die("<p class=\"error\">Unable to retrieve data!" .$conn->error ."</p>");
}

echo "Rows found: " . $result->num_rows . "<br>";
echo "Email: $email<br>";
echo "Password: $loginpassword<br>";


if($result->num_rows==1){
    $_SESSION['access'] = "yes";

    header("Location: ../dashboard.php");
    exit();
}else{
    header("Location: index.html");
    
}

?>