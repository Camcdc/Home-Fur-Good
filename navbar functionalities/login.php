<?php

session_start();

//gets the values from form
$email = $_POST['loginEmail'];
$loginpassword = $_POST['loginPassword'];

// Check for redirect flag (from GET or POST)
$redirect = '';
if (isset($_GET['redirect'])) {
    $redirect = $_GET['redirect'];
} elseif (isset($_POST['redirect'])) {
    $redirect = $_POST['redirect'];
}

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

        // Handle redirect to cruelty report if flag is set
        if ($redirect === 'cruelty') {
            header("Location: ../dashboard_user/CreateCrueltyReport.php");
            exit();
        }

        // Redirect based on role
        if($_SESSION['role'] === 'Administrator'){
            header("Location: ../dashboard_admin/dashboard_admin.php");
        }elseif($_SESSION['role'] === 'User'){
            header("Location: ../dashboard_user/dashboard_user.php");
        }elseif($_SESSION['role'] === 'Veterinarian'){
            header("Location: ../dashboard_vet/dashboard_veterinarian.php");
        }else{
            // Fallback for unknown role
            header("Location: ../dashboard_user/dashboard_user.php");
        }
        exit();
    }else{
        echo "<p class=\"error\">Incorrect password.</p>";
    }
}else{
    echo "<p class=\"error\">Email not found.</p>";
}

?>