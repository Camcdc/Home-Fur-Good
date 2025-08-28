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

$email = $conn->real_escape_string($email);

//query instructions
$sql = "SELECT * FROM user WHERE Email = '$email'";
$result = $conn->query($sql);

//check if the query was successful
if($result === FALSE){
    die("<p class=\"error\">Unable to retrieve data!" .$conn->error ."</p>");
}

if($result->num_rows==1){
    $row = $result->fetch_assoc();
    $hashedPassword = $row['Password'];

    //verify password
    if(password_verify($loginpassword, $hashedPassword)){
    
    
    //ROLE HANDLING
    $_SESSION['access'] = "yes";
    $_SESSION['userID'] = $row['userID'];
    $_SESSION['Fname'] = $row['Fname'];
    $_SESSION['Sname'] = $row['Sname'];
    $_SESSION['role'] = $row['Role'];

    if($_SESSION['role'] === 'Administrator'){
        header("Location: ../dashboard_admin/dashboard_admin.php");
    }elseif($_SESSION['role'] === 'User'){
        header("Location: ../dashboard_admin/dashboard_user.php");
    }elseif($_SESSION['role'] === 'Veterinarian'){
        header("Location: ../dashboard_admin/dashboard_veterinarian.php");
    } 
     
    
    
    
    
    exit();
        }else{
            echo "<p class=\"error\">Incorrect password.</p>";
        }    

    }else{
        echo "<p class=\"error\">Email not found.</p>";
    }

?>