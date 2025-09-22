<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Navbar</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="login-register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="topnav">
    <div class="topnav-left">
        <div class="brand">
            <img src="../pictures/logo/Log.jpg" alt="Logo" class="logo">
            <h3>Home Fur Good</h3>
        </div>

        <a href="../landing pages/homepage.php" class="split">Home</a>

        <div class="dropdown">
            <a href="../landing pages/browseAnimals.php">Browse Animals</a>

        </div>

        <div class="dropdown">
            <a href="../dashboard_user/CrueltyReportInfor.php">Report Cruelty</a>
        </div>

        <div class="dropdown">
            <a href="">How to help <i class="fa fa-caret-down"></i></a>
            <div class="dropdown-content">
                <a href="../landing pages/volunteerLanding.php">Volunteer</a>
                <a href="../landing pages/foster_landing.php">Foster an animal</a>
            </div>
        </div>

        <a href="../landing pages/about.php">About Us</a>
        <a href="../landing pages/contact.php">Contact Us</a>
    </div>

    <div class="topnav-center">
        <?php if(isset($_SESSION['userID']) && in_array($_SESSION['Role'] ?? '', ['User'])): ?>
        <a id="Donate" href="../dashboard_user/donations.php">Donate</a>
        <?php endif; ?>
    </div>

    <div class="topnav-right">
    <?php if (isset($_SESSION['userID']) && in_array($_SESSION['Role'] ?? '', ['User', 'Volunteer', 'Fosterer'])): ?>
        <div class="dropdown user-dropdown">
            <a href="#">Welcome, <?php echo " " . $_SESSION['Fname'];?> <i class="fa fa-caret-down"></i></a>
            <div class="dropdown-content right-dropdown">
                <a href="../dashboard_user/myProfile.php">My Profile</a>
                <a href="../dashboard_user/adoptionStatus.php">Adoptions</a>
                <a href="../dashboard_user/volunteer_dashboard.php">Volunteering</a>
                <a href="../dashboard_user/foster_dashboard.php">Fostering</a>
                <a href="../dashboard_user/crueltyreportreceipt.php">My Cruelty Reports</a>
                <a href="../dashboard_user/donationreceipt.php?userID=<?php echo urlencode($_SESSION['userID']); ?>">My Donations</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    <?php else: ?>
        <a href="../navbar functionalities/userLoginC.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Login</a>
        <a href="../navbar functionalities/userRegisterC.php">Register</a>
    <?php endif; ?>
</div>

</div>

</body>
</html>

<script>
function toggleProfileSidebar() {
    document.getElementById('profile-sidebar').classList.toggle('active');
}

window.addEventListener('click', function(event) {
    const sidebar = document.getElementById('profile-sidebar');
    const toggle = document.querySelector('.profile-toggle');

    if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
        sidebar.classList.remove('active');
    }
});
</script>

</body>
</html>
