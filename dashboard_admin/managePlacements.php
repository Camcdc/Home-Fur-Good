<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
?>
<html>

<head>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="viewAllAnimals.css">
</head>

<body>
    <div class="page-heading">
    <h2>View Animals</h2>
    </div>
    <div class="content-container">
    <div class="section-box">
    <h2 class="section-title">Filters</h2>
    <form method="GET" action="" class="filters-form">
        <input type="text" name="search" placeholder="Search by name or breed..." 
            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" 
            class="filter-input"
        >

        <select name="status" class="filter-select">
            <option value="">Status</option>
            <option value="Available" <?php if(isset($_GET['status']) && $_GET['status'] == 'Active') echo 'selected'; ?>>Available</option>
            <option value="Awaiting Eligibility" <?php if(isset($_GET['status']) && $_GET['status'] == 'Inactive') echo 'selected'; ?>>Awaiting Eligibility</option>
        </select>

        <select name="species" class="filter-select">
            <option value="">All Species</option>
            <option value="Dog" <?php if(isset($_GET['species']) && $_GET['species'] == 'Dog') echo 'selected'; ?>>Dog</option>
            <option value="Cat" <?php if(isset($_GET['species']) && $_GET['species'] == 'Cat') echo 'selected'; ?>>Cat</option>
            <option value="Horse" <?php if(isset($_GET['species']) && $_GET['species'] == 'Horse') echo 'selected'; ?>>Horse</option>
            <option value="Donkey" <?php if(isset($_GET['species']) && $_GET['species'] == 'Donkey') echo 'selected'; ?>>Donkey</option>
            <option value="Goat" <?php if(isset($_GET['species']) && $_GET['species'] == 'Goat') echo 'selected'; ?>>Goat</option>
        </select>


    </form>
</div>




<?php
include 'sidebar_admin.php';
require '../databaseConnection.php';



// Fostered animals query with joins
$sql = "SELECT 
    a.animalID,
    a.name AS animal_name,
    a.species,
    a.breed,
    a.status,
    u.userID,
    u.Fname,
    u.Sname,
    app.applicationID,
    app.applicationStatus AS application_status
FROM animal a
JOIN application app ON a.animalID = app.animalID
JOIN user u ON app.userID = u.userID
WHERE a.status = 'Fostered' 
  AND a.isDeleted = 0      
  AND app.applicationStatus = 'Approved'";


$result = $conn->query($sql);

if($result && $result->num_rows > 0){
    echo "<table>";
    echo "
        <tr>
            <th colspan='10' style='
                text-align: left; 
                font-size: 20px; 
                padding: 20px 15px; 
                background-color: #f1f5f9; 
                color: #2c3e50;
            '>
                <div style='display: flex; justify-content: space-between; align-items: center;'>
                    <span>Fostered Animals (" . $result->num_rows . ")</span>
                </div>
            </th>
        </tr>";
    echo   "<tr>
            <th>Animal Name</th>
            <th>Species</th>
            <th>Breed</th>
            <th>Status</th>
            <th>Foster First Name</th>
            <th>Foster Last Name</th>
            <th>Application ID</th>
            <th>Application Status</th>
            <th>Update</th>
            <th>Medical Report</th>
        </tr>";
    while ($row=$result->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $row["animal_name"];?></td>
            <td><?php echo $row["species"];?></td>
            <td><?php echo $row["breed"];?></td>
            <td class='statusbar'><?php echo $row["status"];?></td>
            <td><?php echo $row["Fname"];?></td>
            <td><?php echo $row["Sname"];?></td>
            <td><?php echo $row["applicationID"];?></td>
            <td><?php echo $row["application_status"];?></td>
            <td><a href="animalUpdate.php?animalID=<?php echo $row["animalID"]?>" class="updateBtn">Update</a></td>
            <td><a href="animalReportsAdmin.php?animalID=<?php echo $row["animalID"]?>" class="updateBtn">Medical Report</a></td>
        </tr>
    <?php }
    echo "</table>";
} else {
    echo "<p>No fostered animal records found</p>";
}
?>

  
</body>

</html>