<html>

<head>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="viewAllAnimals.css">
</head>

<body>
    <h2>View Animals</h2>

    <div class="content-container">
    <div class="section-box">
    <h2 class="section-title">Filters</h2>
    <form method="GET" action="" class="filters-form">
        <input type="text" name="search" placeholder="Search by name or breed..." 
            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" 
            class="filter-input"
        >

        <select name="status" class="filter-select">
            <option value="">All Statuses</option>
            <option value="Available" <?php if(isset($_GET['status']) && $_GET['status'] == 'Available') echo 'selected'; ?>>Available</option>
            <option value="Adopted" <?php if(isset($_GET['status']) && $_GET['status'] == 'Adopted') echo 'selected'; ?>>Adopted</option>
        </select>

        <select name="species" class="filter-select">
            <option value="">All Species</option>
            <option value="Dog" <?php if(isset($_GET['species']) && $_GET['species'] == 'Dog') echo 'selected'; ?>>Dog</option>
            <option value="Cat" <?php if(isset($_GET['species']) && $_GET['species'] == 'Cat') echo 'selected'; ?>>Cat</option>
        </select>

        <button type="submit" class="filter-button">Search</button>
    </form>
</div>




<?php
include 'sidebar_admin.php';
require '../databaseConnection.php';

//SEARCH STUFF
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}


//database queries
if (!empty($search)) { //CHECKS SEARCH FIELD
    $sql = "SELECT * FROM animal 
            WHERE isDeleted = '0' 
            AND (name LIKE '%$search%' 
             OR species LIKE '%$search%' 
             OR breed LIKE '%$search%')";
} else { //IF NOTHING IS IN THERE, DISPLAY ANIMALS NORMALLY
    $sql = "SELECT * FROM animal WHERE isDeleted = '0'";
}


$result = $conn->query($sql);

if($result->num_rows > 0){

    echo "<table>";

    echo 
    "<tr>
        <th colspan='13' style='text-align: left; font-size: 20px; padding: 20px 15px; background-color: #f1f5f9; color: #2c3e50;'>
            Animals (" . $result->num_rows . ")
        </th>
    </tr>";

        echo   "<tr>
                <th>Name</th>
                <th>Species</th>
                <th>Breed</th>
                <th>Age</th>
                <th>Sex</th>
                <th>Color</th>
                <th>Kennel ID</th>
                <th>Size</th>
                <th>Status</th>
                <th>Picture</th>

                <th>Update</th>
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
                        <?php echo $row["kennelID"];?>
                        </td>

                        <td>
                        <?php echo $row["size"];?>
                        </td>

                        <td class='statusbar'>
                        <?php echo $row["status"];?>
                        </td>

                        <td>
                        <img width = '250' height = '200' src="../pictures/animals/<?php echo $row["picture"]; ?>">
                        </td>

                        <td>
                        <a href="animalUpdate.php?animalID=<?php echo $row["animalID"]?>"class="updateBtn">Update</a>
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