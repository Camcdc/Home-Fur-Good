<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>All records results</title>
    <link rel="stylesheet" type="text/css" href="displayAllRecords.css">
    <link rel="stylesheet" href="sidebar_admin.css">
</head>
<body>
    <p><h2>All Cruelty Deleted Report Records in the system </h2> </p>
    <div class="content-container">
        <div class="section-box">
            <h2 class="section-title">Filters</h2>
            <form method="GET" action="" class="filters-form">
                <input type="text" name="search" placeholder="Search Cruelty Report ID..." 
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                    class="filter-input"
                >

                <select name="status" class="filter-select">
                    <option value="">Investigation Status</option>
                    <option value="Active" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                    <option value="Closed" <?php echo (isset($_GET['status']) && $_GET['status'] === 'Closed') ? 'selected' : ''; ?>>Closed</option>
                </select>

                <select name="Inspector" class="filter-select">
                    <option value="">All Inspectors</option>
                    <?php
                    $inspector_options = ['Inspector 1', 'Inspector 2', 'Inspector 3', 'Inspector 4'];
                    foreach ($inspector_options as $inspector_option) {
                        $selected = (isset($_GET['Inspector']) && $_GET['Inspector'] === $inspector_option) ? 'selected' : '';
                        echo "<option value=\"$inspector_option\" $selected>$inspector_option</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="filter-button">Search</button>
            </form>
        </div>
    </div>
</body>
<?php
    include 'sidebar_admin.php';
    require '../databaseConnection.php';

    $search="";
    $status="";
    $inspector="";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    }
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
    }
    if (isset($_GET['Inspector'])) {
        $inspector = $_GET['Inspector'];
    }

    $search_safe = $conn->real_escape_string($search);
    $status_safe = $conn->real_escape_string($status);
    $inspector_safe = $conn->real_escape_string($inspector);


    $sql = "SELECT * FROM cruelty_report WHERE isDeleted = 1 AND investigationStatus = 'Closed'";

    $result = $conn->query($sql);

   if($result->num_rows > 0){
    // Button positioned above the table for better visibility
    echo "<center>
        <div style='width: 75%; text-align: right; margin-bottom: 10px;'>
            <a href='displayAllrecords.php' class='updateBtn' style='padding: 10px 20px; background-color: #3a6fa0; color: #fff; border-radius: 6px; text-decoration: none; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);'>
                📋 View All Records
            </a>
        </div>
        
        <table width='75%' bgcolor='lightgray'>
            <tr bgcolor='red'>
                <th>Cruelty Report ID</th>
                <th>User ID</th>
                <th>Description</th>
                <th>Date Reported</th>
                <th>Location</th>
                <th>Status</th>
                <th>Rescue Circumstance</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>";

     while($row = $result->fetch_assoc()){
        if($row["isDeleted"] == 1) {?>
        <tr>
            <td><?php echo $row["crueltyReportID"];?></td>
            <td><?php echo $row["userID"];?></td>
         <td class="description-cell"><?php echo htmlspecialchars($row["detailedDescription"]);?></td>
            <td><?php echo $row["createDate"];?></td>
            <td><?php echo $row["location"];?></td>
            <td><?php echo $row["investigationStatus"];?></td>
            <td><?php echo $row["rescueCircumstance"];?></td>
            <td>
                <img src="../pictures/cruelty/<?php echo $row['picture']; ?>" alt="Image of cruelty" title="Click to view full image">
            </td>
            <td>
                <a href="ReportRestoreRecord.php?crueltyReportID=<?php echo $row["crueltyReportID"] ?>">
                    <button class="restore-button" onclick="return confirm('Are you sure you want to restore this record?');">Restore</button>
                </a>
                 <a href="viewFullCrueltyReport.php?crueltyReportID=<?php echo $row["crueltyReportID"] ?>">
                        <button class="view-button" onclick="return confirm('View full report details?');">Full Report</button>
                    </a>
            </td>
        </tr>
        <?php
        }
    }
     echo "</table></center>";
} else {
    echo "<p>No matching record found. Try using other search criteria</p>";
}

$conn->close();
?>

</body>
</html>
