<?php
include 'databaseconnection.php';

$Report_ID=$_GET['crueltyID']; 

$sql_select = "SELECT * FROM cruelty_report WHERE crueltyID= '$Report_ID'";
$result=$conn->query($sql_select);
$row2=$result->fetch_assoc();

echo "Cruelty ID idnumber =". $Report_ID. " extracted from the URL using GET</br>";

echo "Cruelty ID idnumber =". $row2['crueltyID']." extracted from database using query";

if(isset($_POST['update'])){
    $Report_ID = $_GET['crueltyID'];
    $Staff_ID = $_POST['staffID'];
    $Animal_Details = $_POST['animalDetails'];
    $Location= $_POST['location'];
    $Investigation_Status= $_POST['investigationStatus'];
    $Rescue_Status= $_POST['rescueStatus'];
   

    $sql_update = "UPDATE cruelty_report SET animalDetails= '$Animal_Details',
        staffID = '$Staff_ID',
        `location`='$Location',
        investigationStatus='$Investigation_Status',
        rescueStatus='$Rescue_Status',
       WHERE crueltyID= '$Report_ID'";

    $query_execute = $conn->query($sql_update);

    if($query_execute){

        header("Location:displayAllrecords.php");

        $conn->close();
    }else{
        echo "<script>alert('Record update failed. Retry')</script>";
        header("Location:displayAllrecords.php");
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Update Records</title>
        <link rel="stylesheet" type="text/css" href="crueltyreport.css">
    </head>
    <body>
        <h2>Cruelty Report update form:</h2>
        <hr>

    <form action="" method="POST" enctype="multipart/form-data">
    
        <table width = 25%>
            <tr><td>Report ID: </td><td> <input type="text" id="crueltyID" name="crueltyID" value="<?php echo $row2['crueltyID'];?>"></td></tr>
            <tr><td>Staff ID: </td><td> <input type="text" id="staffID" name="staffID" value="<?php echo $row2['staffID'];?>"></td></tr>
            <tr><td>Animal Details: </td><td> <input type="text" id="animalDetails" name="aniamlDetails" value="<?php echo $row2['animalDetails'];?>"></td></tr>
            <tr><td>Location: </td><td> <input type="text" id="location" name="location" value="<?php echo $row2['location'];?>"></td></tr>
            <tr><td>Investigation Status: </td><td> <input type="text" id="investigationStatus" name="investigationStatus" value="<?php echo $row2['investigationStatus'];?>"></td></tr>
            <tr><td>Rescue Status: </td><td> <input type="text" id="rescueStatus" name="rescueStatus" size="4" value="<?php echo $row2['rescueStatus'];?>"></td></tr>
            <tr><td>Image: </td><td> <input type="file" id="picture" name="picture"></td></tr>

            <tr><td colspan="2"><input type="submit" name="update" value="Update dog record"></td></tr>
        </table>
    </form>

<hr>
</body>
</html>