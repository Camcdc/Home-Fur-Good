<html>

<head>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="viewAllAnimals.css">
</head>

<body>
<?php
include 'sidebar_admin.php';
require '../databaseConnection.php';


//database queries
$sql = "SELECT * FROM animal";

$result = $conn->query($sql);

if($result->num_rows > 0){

    echo "<h2>View Animals</h2>";
    echo "<center>
            <table>";

    echo "
    <tr>
        <th colspan='13' style='text-align: left; font-size: 20px; padding: 20px 15px; background-color: #f1f5f9; color: #2c3e50;'>
            Animals (" . $result->num_rows . ")
        </th>
    </tr>";

        echo   "<tr>
                <th>Animal ID</th>
                <th>Kennel ID</th>
                <th>Status ID</th>
                <th>Name</th>
                <th>Species</th>
                <th>Breed</th>
                <th>Age</th>
                <th>Sex</th>
                <th>Color</th>
                <th>Size</th>
                <th>Picture</th>

                <th>Update</th>
                <th>Delete</th>
                </tr>";

                while ($row=$result->fetch_assoc()){ ?>
                    <tr>
                        <td>
                        <?php echo $row["animalID"];?>
                        </td>

                        <td>
                        <?php echo $row["kennelID"];?>
                        </td>

                        <td>
                        <?php echo $row["statusID"];?>
                        </td>

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
                        <?php echo $row["size"];?>
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
        </center>";
?>

  
</body>

</html>