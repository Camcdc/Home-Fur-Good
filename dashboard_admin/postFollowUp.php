<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include 'sidebar_admin.php';
require '../databaseConnection.php';

// Set default filter (if no filter selected)
$filter = isset($_GET['criteriaMeet']) ? $_GET['criteriaMeet'] : '';

// Prepare SQL query
$sql = "SELECT a.*, pf.*, ad.*, u.Fname, u.Sname
        FROM animal a
        INNER JOIN adoption ad ON a.animalID = ad.animalID
        INNER JOIN postfollowup pf ON ad.adoptionID = pf.adoptionID
        INNER JOIN user u ON ad.userID = u.userID
        WHERE pf.isDeleted = 0";

// Add condition for filtering by 'criteriaMeet'
if ($filter != '') {
    // Using prepared statements for safety
    $sql .= " AND pf.criteriaMeet = ?";
}

// Optional: Sort by criteriaMeet
$sql .= " ORDER BY FIELD(pf.criteriaMeet, 'Yes', 'Pending', 'No')"; // Optional: Custom sort order

// Prepare the query
$stmt = $conn->prepare($sql);

// If filter is set, bind the parameter
if ($filter != '') {
    $stmt->bind_param('s', $filter);  // 's' indicates the filter is a string
}

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="viewAllAnimals.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>

<body>
    <div class="page-heading">
        <h2>Post-Follow Ups</h2>
    </div>

    <div class="content-container">
    <div class="filter-container">
        <div class="filter-bar">
    <form method="GET" action="">
        <label for="criteriaMeet" class="filter-label">Filter by Criteria Met:</label>
        <select name="criteriaMeet" id="criteriaMeet" class="filter-select" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="Yes" <?php echo ($filter == 'Yes' ? 'selected' : ''); ?>>Yes</option>
            <option value="Pending" <?php echo ($filter == 'Pending' ? 'selected' : ''); ?>>Pending</option>
            <option value="No" <?php echo ($filter == 'No' ? 'selected' : ''); ?>>No</option>
        </select>
    </form>
</div>
</div>


        <?php
        if ($result->num_rows > 0) {
            echo "<table>";

            echo "
                <tr>
                    <th colspan='13' style='
                        text-align: left; 
                        font-size: 20px; 
                        padding: 20px 15px; 
                        background-color: #f1f5f9; 
                        color: #2c3e50;
                    '>
                    </th>
                </tr>";

            echo "<tr>
                    <th>User</th>
                    <th>Animal Adopted</th>
                    <th>Date Adopted</th> 
                    <th>Follow Up Date</th>
                    <th>Criteria Met?</th>
                    <th>Outcome</th>
                    <th>Update</th>
                </tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row['Fname'] . ' ' . $row['Sname']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['adoptionDate']) . "</td>
                    <td>" . htmlspecialchars($row['followUpDate']) . "</td>
                    <td>" . htmlspecialchars($row['criteriaMeet']) . "</td>
                    <td>" . htmlspecialchars($row['outcome']) . "</td>
                    <td><a href='followUpUpdate.php?followUpID=" . $row['followUpID'] . "' class='updateBtn'>Update</a></td>
                </tr>";
            }

            echo "</table>";

        } else {
            echo "<p>No records found</p>";
        }
        ?>
    </div>
</body>

</html>
