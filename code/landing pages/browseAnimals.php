<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../navbar functionalities/login-register.css">
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <link rel="stylesheet" href="browseAnimals.css">
    <title>Browse Animals</title>
</head>

<body>

<?php include '../navbar functionalities/navbar.php'; ?>
<?php include '../databaseConnection.php'; ?>

<h1>Animals Available for Adoption</h1>

<div class="container">

    <!-- SIDEBAR FOR FILTERS -->
    <div class="sidebar">
        <h3>Filters</h3>
        <label><input type="checkbox"> Dogs</label><br>
        <label><input type="checkbox"> Cats</label><br>
        <label><input type="checkbox"> Other</label>
    </div>

    <!-- ANIMAL BLOCKS -->
    <div class="content">
        <?php

        //Fetch animals from database
        $sql = "SELECT * FROM animal WHERE isDeleted = '0'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card'>";
                
                //CHECKS TO SEE IF THERES PICTURE 
                if (!empty($row['picture'])) {
                    echo "<img src='../pictures/animals/" . $row['picture'] . "' alt='" . $row['name'] . "' />";
                } else {
                    echo "<img src='images/default.jpg' alt='Animal' />";
                }

                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<p>" . htmlspecialchars($row['breed']) . " • " . htmlspecialchars($row['age']) . " years old</p>";
                echo "<button>View " . $row['name'] . "</button>";
                echo "</div>";
            }
        } else {
            echo "<p>No animals available at the moment.</p>";
        }

        $conn->close();
        ?>
    </div>
</div>

</body>
</html>
