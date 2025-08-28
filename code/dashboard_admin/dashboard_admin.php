<?php
session_start();

// Ensures user is logged in and is admin
if (!isset($_SESSION['access']) || $_SESSION['role'] !== 'Administrator') {
    echo "<script>
            alert('Please login to view this page');
            window.location.href = '../landing pages/browseAnimals.php';
          </script>";
    exit();
}
?>


<html>

<head>
    <link rel="stylesheet" href="sidebar_admin.css">
</head>

<body>
<?php
include 'sidebar_admin.php';
require '../databaseConnection.php';
?>


<h1>Welcome to the Admin Dashboard</h1>
<p>This page is only accessible by admins.</p>

</body>