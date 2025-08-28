<!DOCTYPE html>
<html lang="en">

<head>
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>All records results</title>
    <link rel="stylesheet" type="text/css" href="displayAllRecords.css">
</head>
<body>
    <form method="GET" action="" class="filters-form">
        <input type="text" name="search" placeholder="Search by name or breed..." 
            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" 
            class="filter-input"
        >

        <select name="Status" class="filter-select">
            <option value="">All Statuses</option>
            <option value="Inactive" <?php if(isset($_GET['status']) && $_GET['status'] == 'Available') echo 'selected'; ?>>Available</option>
            <option value="Active" <?php if(isset($_GET['status']) && $_GET['status'] == 'Adopted') echo 'selected'; ?>>Adopted</option>
        </select>

        <select name="species" class="filter-select">
            <option value="">All Species</option>
            <option value="Dog" <?php if(isset($_GET['species']) && $_GET['species'] == 'Dog') echo 'selected'; ?>>Dog</option>
            <option value="Cat" <?php if(isset($_GET['species']) && $_GET['species'] == 'Cat') echo 'selected'; ?>>Cat</option>
        </select>

        <button type="submit" class="filter-button">Search</button>
    </form>
</body>

<?php
    require '../databaseconnection.php';

    $sql="SELECT * FROM cruelty_report";

    $result = $conn->query($sql);

    if($result ->num_rows >0){

        echo "<p><h2>All Cruelty Report Records in the system </h2> </p>";
        echo "<center><table width =\"75%\" bgcolor = \"lightblue\"><tr bgcolor=\"orange\">
            <th>Cruelty Report ID</th> <th>User ID</th><th>animal Details</th><th>Location</th><th>Investigation Status</th>
            <th>Rescue Circumstance</th><th>image</th><th>Update</th><th>Delete</th></tr>";

            while($row = $result->fetch_assoc() ){
                if($row["isDeleted"] == 0) {?>
                <tr>
                    <td>
                        <?php echo $row["crueltyReportID"];?>
                    </td>

                    <td>
                        <?php echo $row["userID"];?>
                    </td>
                    <td>
                        <?php echo $row["animalDetails"];?>
                    </td>
                    <td>
                        <?php echo $row["location"];?>
                    </td>
                    <td>
                        <?php echo $row["investigationStatus"];?>
                    </td>
                    <td>
                        <?php echo $row["rescueCircumstance"];?>
                    </td>
                    <td>
                        <img src="./images/<?php echo $row["picture"];?>">
                    </td>
                    <td>
                        <a href="ReportUpdaterecord.php?crueltyReportID=<?php echo $row["crueltyReportID"] ?>"><button class="update-button" onclick="return confirm('Are you sure you want to update this record?');">Update</button></a>
                    </td>
                    <td>
                        <a href="ReportDeleteRecord.php?crueltyReportID=<?php echo $row["crueltyReportID"] ?>"><button class="delete-button" onclick="return confirm('Are you sure you want to delete this record?');">Delete </button></a>
                    </td>

                </tr>

                <?php
            }
        }

            }else{
                echo "<p>No matching record found. Try using other search criteria </p>";
            }

            $conn->close();
            echo "</table></center>";
            echo" <p><a href= \"displayAllrecords.php\"><button>Display All Cruelty Records </button></a></p>";
            echo" <p> <a href=\"loginForm.html\"><button>Back to Login</button></a></p>";

?>
</body>
</html>
