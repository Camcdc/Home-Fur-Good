<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseConnection.php';
include 'sidebar_admin.php';


$Report_ID = $_GET['crueltyReportID'];

$sql_select = "SELECT * FROM cruelty_report WHERE crueltyReportID= '$Report_ID'";
$result=$conn->query($sql_select);
$editData=$result->fetch_assoc();



if(isset($_POST['update'])){
    $Report_ID = $_GET['crueltyReportID'];
    $User_ID = $_POST['userID'];
    $Animal_Details = $_POST['detailedDescription'];
    $Location= $_POST['location'];
    $Investigation_Status= $_POST['investigationStatus'];
    $Rescue_Status= $_POST['rescueCircumstance'];
   

    $sql_update = "UPDATE cruelty_report SET detailedDescription= '$Animal_Details',
        userID = '$User_ID',
        location='$Location',
        investigationStatus='$Investigation_Status',
        rescueCircumstance='$Rescue_Status'
       WHERE crueltyReportID= '$Report_ID'";

    $query_execute = $conn->query($sql_update);

    if($query_execute){
        echo "<script>alert('✅Record updated successfully')</script>";
        echo "<script>window.location.href='displayAllrecords.php'</script>";

        $conn->close();
    }else{
        echo "<script>alert('❌Record update failed. Retry')</script>";
        echo "<script>window.location.href='displayAllrecords.php'</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en-UK">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cruelty Report</title>
    <link rel="stylesheet" type="text/css" href="crueltyreport.css">
    <link rel="stylesheet" type="text/css" href="sidebar_admin.css">
    <link rel="icon" type="image/x-icon" href="../pictures/logo/favicon.icon">
    <script src="ReportUpdaterecord.js"></script>>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .overlay { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index: 1000; }
        .popup { display: none; position: fixed; top: 10%; left: 50%; transform: translateX(-50%); background: #fff; z-index: 1001; border-radius: 8px; box-shadow: 0 0 10px #333; }
        .popup_inner { padding: 20px; }
        .close-button { float: right; }
    </style>
</head>
<body>
    <h1>Report Animal Cruelty</h1>

    <form action="ReportUpdaterecord.php?crueltyReportID=<?php echo $Report_ID; ?>" method="POST">
        <input type="hidden" name="editid" value="<?php echo isset($editid) ? $editid : ''; ?>">
        <input type="hidden" id="userID" name="userID" value="<?php echo isset($editData) ? $editData['userID'] : ''; ?>">
        <table>

    <tr>
    <td>Detailed Description</td>
    <td><textarea id="detailedDescription" name="detailedDescription" rows="4" cols="50" maxlength="255" ><?php echo isset($editData) ? htmlspecialchars($editData['detailedDescription']) : ''; ?></textarea></td>
    </tr>
    <tr>
    <td>First Name</td>
        <td><input type="text" id="Fname" name="Fname" size="20" maxlength="50"  value="<?php echo isset($editData) ? $editData['fname'] : ''; ?>"></td>
    </tr>
    <td>Surname</td>
        <td><input type="text" id="Lname" name="Lname" size="20" maxlength="50" value="<?php echo isset($editData) ? $editData['lname'] : ''; ?>"></td>
    <tr>
    <tr>
        <td>Contact Number</td>
        <td><input type="text" id="contactNumber" name="contactNumber" size="20" maxlength="15"  value="<?php echo isset($editData) ? $editData['contactNumber'] : ''; ?>"></td>
    </tr>
            <tr>
                <td>Location</td>
                <td><input type="text" id="location" name="location" size="20" maxlength="255"  value="<?php echo isset($editData) ? $editData['location'] : ''; ?>"></td>
            </tr>

            <tr>
                <td>Investigation Status</td>
                <td>
                    <select name="investigationStatus" id="investigationStatus" required>
                        <option value="">Choose Investigation Status</option>
                        <option value="ongoing investigation" <?php if(isset($editData) && $editData['investigationStatus']=='ongoing investigation') echo 'selected'; ?>>Ongoing Investigation</option>
                        <option value="investigation complete" <?php if(isset($editData) && $editData['investigationStatus']=='investigation complete') echo 'selected'; ?>>Investigation Complete</option>
                    </select>
                </td>
            </tr>
           <?php 
if(isset($editData) && $editData['investigationStatus'] == 'investigation complete'){

    // Update the completedDate in the database if it's not already set
    if (empty($editData['completedDate'])) {
        $sql3 = "UPDATE cruelty_report 
                 SET completedDate = NOW() 
                 WHERE crueltyReportID = '$Report_ID'";
        $conn->query($sql3);

        // Optional: update $editData to reflect the change immediately
        $editData['completedDate'] = date('Y-m-d');
    }

    $completedDate = isset($editData['completedDate']) && $editData['completedDate'] != '' 
                     ? $editData['completedDate'] 
                     : date('Y-m-d'); // current date in YYYY-MM-DD format

    echo '<tr>
        <td>Complete Date</td>
        <td><input type="date" id="completedDate" name="completedDate" size="20" maxlength="255" readonly value="' . $completedDate . '"></td>
    </tr>';

}
?>




            <tr>
                <td>Rescue Circumstance</td>
                <td><input type="text" name="rescueCircumstance" size="20" maxlength="255" required value="<?php echo isset($editData) ? $editData['rescueCircumstance'] : ''; ?>"></td>
            </tr>

            <tr>
                <td colspan="2">
                    <input type="submit" name="update" value="Save Report">
                    <a href="displayAllrecords.php"><button  type="button" class="edit-button">All Records</button></a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>