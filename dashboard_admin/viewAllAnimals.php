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

        <button type="submit" class="filter-button">Search</button>
    </form>
</div>




<?php
include 'sidebar_admin.php';
require '../databaseConnection.php';

//SEARCH STUFF
$search = "";
$species = "";
$status = "";

if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
if (isset($_GET['species'])) {
    $species = $_GET['species'];
}
if (isset($_GET['status'])) {
    $status = $_GET['status'];
}

    $search_safe = $conn->real_escape_string($search);
    $species_safe = $conn->real_escape_string($species);
    $status_safe = $conn->real_escape_string($status);

$sql = "SELECT * FROM animal WHERE isDeleted = '0' AND status != 'Adopted'";

//database queries
if (!empty($search) || !empty($species) || !empty($status)) {
            if (!empty($search_safe)) {
                $sql .= " AND (name LIKE '%$search_safe%' OR breed LIKE '%$search_safe%')";
            }
            if (!empty($species_safe)) {
                $sql .= " AND species = '$species_safe'";
            }
            if (!empty($status_safe)) {
                $sql .= " AND status = '$status_safe'";
            }
} else { //IF NOTHING IS IN THERE, DISPLAY ANIMALS NORMALLY
    $sql = "SELECT * FROM animal WHERE isDeleted = '0' AND status != 'Adopted'";
}


$result = $conn->query($sql);

if($result->num_rows > 0){

    echo "<table>";

    echo "
        <tr>
            <th colspan='14' style='
                text-align: left; 
                font-size: 20px; 
                padding: 20px 15px; 
                background-color: #f1f5f9; 
                color: #2c3e50;
            '>
                <div style='display: flex; justify-content: space-between; align-items: center;'>
                    <span>Animals (" . $result->num_rows . ")</span>
                    <a href='animalsAwaitingRetrieval.php' class='updateBtn' style='padding: 8px 16px; background-color: #3a6fa0; color: #fff; border-radius: 6px; text-decoration: none;'>Animals Awaiting Retrieval</a>
                </div>
            </th>
        </tr>";



        echo   "<tr>
                <th>Name</th>
                <th>Species</th>
                <th>Kennel</th> 
                <th>Breed</th>
                <th>Age</th>
                <th>Sex</th>
                <th>Color</th>
                <th>Size</th>
                <th>Status</th>
                <th>Health Status</th>
                <th>Picture</th>

                <th>Update</th>
                <th>Medical Report</th>
                <th>Delete</th>
                </tr>";

                while ($row=$result->fetch_assoc()){ ?>
                    <tr>
                        <td>
                        <?php echo $row["name"];?>
                        </td>

                        <td>
                        <?php echo $row["species"];?>
                        </td>

                        <td>
                        <?php echo $row["kennelID"];?>
                        </td>

                        <td>
                        <?php echo $row["breed"];?>
                        </td>

                        <td>
                        <?php echo $row["age"];?>
                        </td>

                        <td>
                        <?php echo $row["sex"];?>
                        </td>

                        <td>
                        <?php echo $row["colour"];?>
                        </td>

                        <td>
                        <?php echo $row["size"];?>
                        </td>

                        <td class='statusbar'>
                        <?php echo $row["status"];?>
                        </td>

                        <td>
                        <?php echo $row["healthStatus"];?>
                        </td>

                        <td>
                        <img width = '250' height = '200' src="../pictures/animals/<?php echo $row["picture"]; ?>">
                        </td>

                        <td>
                        <a href="animalUpdate.php?animalID=<?php echo $row["animalID"]?>"class="updateBtn">Update</a>
                        </td>

                        <td>
                        <a href="animalReportsAdmin.php?animalID=<?php echo $row["animalID"]?>"class="updateBtn">Medical Report</a>
                        </td>

                        <td>
                        <a href="animalDelete.php?animalID=<?php echo $row["animalID"]?>" class="deleteBtn">Delete</a>                        
                        </td>

                    </tr>
            <?php 
                 }

}else{
    echo "<p>No records found</p>";
}

echo "</table>
    </div>
    </div>";
?>

  
</body>

</html>