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
    <div class="page-heading" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
        <a href='viewAllAnimals.php' class='updateBtn'>← Back to All Animals</a>
        <h2 style="margin: 0 auto; text-align: center; flex: 1;">Animals Awaiting Retrieval</h2>
    </div>

    <div class="content-container">
        <div class="section-box">
            <h2 class="section-title">Filters</h2>
            <form method="GET" action="" class="filters-form">
                <input type="text" name="search" placeholder="Search by name or breed..."
                    value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" 
                    class="filter-input">

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

// 🟢 Corrected: use `occupation` not `occupancy`
function decreaseKennelOccupancy($conn, $kennelID) {
    $sql = "UPDATE kennel SET occupation = GREATEST(occupation - 1, 0) WHERE kennelID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $kennelID);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Prepare failed in decreaseKennelOccupancy: " . $conn->error);
    }
}

// Get filter values
$search = isset($_GET['search']) ? $_GET['search'] : "";
$species = isset($_GET['species']) ? $_GET['species'] : "";

$search_safe = $conn->real_escape_string($search);
$species_safe = $conn->real_escape_string($species);

// Handle retrieval POST
if (isset($_POST['retrieve']) && isset($_POST['followUpID'])) {
    $followUpID = intval($_POST['followUpID']);

    // Mark animal as retrieved in postfollowup
    $update_sql = "UPDATE postfollowup SET retrieved = 'Retrieved' WHERE followUpID = ?";
    $update_stmt = $conn->prepare($update_sql);
    if ($update_stmt) {
        $update_stmt->bind_param("i", $followUpID);
        $update_stmt->execute();
        $update_stmt->close();

        // Get animalID + kennelID
        $animal_sql = "SELECT a.animalID, a.kennelID 
                       FROM postfollowup pf
                       JOIN adoption ad ON pf.adoptionID = ad.adoptionID
                       JOIN animal a ON ad.animalID = a.animalID
                       WHERE pf.followUpID = ?";
        $animal_stmt = $conn->prepare($animal_sql);
        $animal_stmt->bind_param("i", $followUpID);
        $animal_stmt->execute();
        $animal_result = $animal_stmt->get_result();

        if ($animal_row = $animal_result->fetch_assoc()) {
            $animalID = $animal_row['animalID'];
            $kennelID = $animal_row['kennelID'];

            // Decrease kennel occupation
            if (!empty($kennelID)) {
                decreaseKennelOccupancy($conn, $kennelID);
            }

            // 🟢 Mark animal as deleted (soft delete)
            $delete_animal_sql = "UPDATE animal SET isDeleted = 1 WHERE animalID = ?";
            $delete_animal_stmt = $conn->prepare($delete_animal_sql);
            $delete_animal_stmt->bind_param("s", $animalID);
            $delete_animal_stmt->execute();
            $delete_animal_stmt->close();
        }
        $animal_stmt->close();

        echo "<script>alert('Animal retrieved and marked as deleted'); window.location.href = window.location.href;</script>";
    } else {
        die("Prepare failed: " . $conn->error);
    }
}

// Build SQL query
$sql = "SELECT a.*, pf.followUpID, pf.retrieved
        FROM animal a
        JOIN adoption ad ON a.animalID = ad.animalID
        JOIN postfollowup pf ON ad.adoptionID = pf.adoptionID
        WHERE pf.retrieved = 'Pending' AND ad.status = 1 AND a.isDeleted = 0";

if (!empty($search_safe)) {
    $sql .= " AND (a.name LIKE '%$search_safe%' OR a.breed LIKE '%$search_safe%')";
}
if (!empty($species_safe)) {
    $sql .= " AND a.species = '$species_safe'";
}

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error . "<br>SQL: " . $sql);
}

// Display table
if ($result->num_rows > 0) {
    echo "<table>";
    echo "
        <tr>
            <th colspan='12' style='
                text-align: left; 
                font-size: 20px; 
                padding: 20px 15px; 
                background-color: #f1f5f9; 
                color: #2c3e50;
            '>
                Animals (" . $result->num_rows . ")
            </th>
        </tr>";
    echo "<tr>
            <th>Name</th>
            <th>Species</th>
            <th>Breed</th>
            <th>Age</th>
            <th>Sex</th>
            <th>Color</th>
            <th>Kennel</th>
            <th>Size</th>
            <th>Status</th>
            <th>Health Status</th>
            <th>Picture</th>
            <th>Retrieve Animal</th>
        </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['name']}</td>
            <td>{$row['species']}</td>
            <td>{$row['breed']}</td>
            <td>{$row['age']}</td>
            <td>{$row['sex']}</td>
            <td>{$row['colour']}</td>
            <td>{$row['kennelID']}</td>
            <td>{$row['size']}</td>
            <td>{$row['status']}</td>
            <td>{$row['healthStatus']}</td>
            <td><img width='250' height='200' src='../pictures/animals/{$row['picture']}'></td>
            <td>
                <form method='POST'>
                    <input type='hidden' name='followUpID' value='{$row['followUpID']}'>
                    <button type='submit' name='retrieve' class='updateBtn'>Retrieve Animal</button>
                </form>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No records found</p>";
}

echo "</div></div>";
?>

</body>
</html>
