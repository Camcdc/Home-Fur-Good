<!DOCTYPE html>
<html lang="en-UK">
<head>
    <title>Create Cruelty Report</title>
    <link rel="icon" type="image/x-icon" href="C:\Users\Somila\Downloads\a488be7e-a2d8-4e81-8565-a1faedd1ac38.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="crueltyreport.css">
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>
    <?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}
include '../navbar functionalities/navbar.php';
?>
    <!-- Breadcrumbs -->
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ul>
            <li><a href="../landing pages/homepage.php">Home</a></li>
            <li>Create Cruelty Report</li>
        </ul>
    </nav>

    <div class="header">
        <h1>Animal Cruelty Report System</h1>
    </div>
    
    <div class="row">
        <div class="column"><img src="../pictures/logo/phone.jpg">
            <p>Please ensure that your cellphone is on at all times</p>
        </div>
        <div class="column"><img src="../pictures/logo/location.jpg">
            <p>Please ensure that you know the location of where the incident took place.</p>
        </div>
        <div class="column"><img src="../pictures/logo/checklist.jpg">
            <p>Please ensure that the information is accurate for a successful rescue operation</p>
        </div>
        <div class="column"><img src="../pictures/logo/customerSupport.jpg">
            <br><a href="../landing pages/contact.php">Contact Us</a>
            <p>If you are experiencing any issues please contact our local support</p>
        </div>
    </div>

    <div class="Form">
        <h2>Creating a Cruelty Report</h2>
        <table>
            <form action="CreateCrueltyReport.php" method="POST" enctype="multipart/form-data">
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <h3>Please fill in the form below to create a cruelty report</h3>
                    </td>
                </tr>
                <tr>
                    <td><label for="Fname">First Name:</label></td>
                    <td><input type="text" id="Fname" name="Fname" value="<?php echo isset($_SESSION['Fname']) ? htmlspecialchars($_SESSION['Fname']) : ''; ?>" required readonly></td>
                </tr>
                <tr>
                    <td><label for="Lname">Last Name:</label></td>
                    <td><input type="text" id="Lname" name="Lname" value="<?php echo isset($_SESSION['Sname']) ? htmlspecialchars($_SESSION['Sname']) : ''; ?>" required readonly></td>
                </tr>
                <tr>
                    <td><label for="contactNumber">Cell Number :</label></td>
                    <td><input type="text" id="contactNumber" name="contactNumber" value="<?php echo isset($_SESSION['CellNumber']) ? htmlspecialchars($_SESSION['CellNumber']) : ''; ?>" required readonly
                    ></td>
                </tr> 
                <tr>
                    <td><label for="picture">Picture:</label></td>
                    <td><input type="file" id="picture" name="picture" accept=".jpg, .png, .jpeg " ></td>
                </tr>
                <tr>
                    <td><label for="detailedDescription">Detailed Description:</label></td>
                    <td><textarea id="detailedDescription" name="detailedDescription" max="255" required placeholder="Please provide as much details about the animal and the incident.For example, color, legs,size and how the animal was abused"></textarea></td>
                </tr>
                <tr>
                    <td><label for="location">Location:</label></td>
                    <td><input type="text" id="location" name="location" required placeholder="Please provide the location of the incident,E.g 12 Huntley Street, Makhanda)"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <input type="submit" value="Submit Report">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                <button type="button" onclick="window.location.href='crueltyreportreceipt.php'">View Existing Cruelty Report</button></td>
                </tr>
            </form>
        </table>
    </div>
</body>
</html>
<?php
include_once "../databaseConnection.php";

$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Fname = $_POST["Fname"];
    $Lname = $_POST["Lname"];
    $contactNumber = $_POST["contactNumber"];
    $detailedDescription  = $_POST["detailedDescription"];
    $location = $_POST["location"];
    
    $fileName = null;

    // Handle optional file upload
    if (isset($_FILES["picture"]) && $_FILES["picture"]["error"] == 0) {
        $targetDir = "../pictures/cruelty/";
        $fileName = time() . "_" . basename($_FILES["picture"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (!move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
            echo "<script>alert('❌ Error uploading file.');</script>";
            exit();
        }
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO cruelty_report 
        (userID, fname, lname, contactNumber, picture, detailedDescription, location, isDeleted, createDate, investigationStatus) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW(), 'ongoing investigation')");
    
    $stmt->bind_param(
        "issssss",
        $userID,
        $Fname,
        $Lname,
        $contactNumber,
        $fileName,
        $detailedDescription,
        $location
    );

    if ($stmt->execute()) {
        $newID = $stmt->insert_id;
        echo "<script>alert('✅ Cruelty report submitted successfully!'); window.location.href='viewSingleReport.php?crueltyReportID=" . $newID . "';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Database Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
