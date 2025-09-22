<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}

// Normalize role to lowercase for checking
$role = strtolower(trim($_SESSION['Role'] ?? ''));
$allowed_roles = ['user', 'volunteer', 'fosterer'];

// Redirect if not logged in or not an allowed role
if (!isset($_SESSION['userID']) || !in_array($role, $allowed_roles)) {
    header("Location: ../register/userLoginC.php?error=Please+log+in+to+view+this+page");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <link rel="stylesheet" href="myProfile.css">
    <title>My Profile</title>
</head>
<body>

<?php include '../navbar functionalities/navbar.php'; ?>

<div class="page-container">
    <div class="profile-container">
        <div class="profile-heading">
            <h2>My Profile</h2>
        </div>
        <div class="profile-fields">

            <div class="fields">
                <p><label for="Fname">First Name:</label></p>
                <input type="text" id="Fname" value="<?= htmlspecialchars($_SESSION['Fname'] ?? '') ?>" readonly>
            </div>

            <div class="fields">
                <p><label for="Sname">Last Name:</label></p>
                <input type="text" id="Sname" value="<?= htmlspecialchars($_SESSION['Sname'] ?? '') ?>" readonly>
            </div>

            <div class="fields">
                <p><label for="DateOfBirth">Date of Birth:</label></p>
                <input type="text" id="DateOfBirth" value="<?= htmlspecialchars($_SESSION['DateOfBirth'] ?? '') ?>" readonly>
            </div>

            <div class="fields">
                <p><label for="CellNumber">Phone:</label></p>
                <input type="text" id="CellNumber" value="<?= htmlspecialchars($_SESSION['CellNumber'] ?? '') ?>" readonly>
            </div>

            <div class="fields">
                <p><label for="Address">Address:</label></p>
                <input type="text" id="Address" value="<?= htmlspecialchars($_SESSION['Address'] ?? '') ?>" readonly>
            </div>

            <div class="fields">
                <p><label for="Role">Role:</label></p>
                <input type="text" id="Role" value="<?= htmlspecialchars($_SESSION['Role'] ?? '') ?>" readonly>
            </div>
        </div>
    </div>
</div>

</body>
</html>
