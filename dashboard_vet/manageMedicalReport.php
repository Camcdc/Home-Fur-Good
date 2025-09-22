<?php  
require_once '../databaseConnection.php';
session_start();
if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Veterinarian') {
    // Redirect to homepage if not logged in or not a vet
    header("Location: ../landing pages/homepage.php");
    exit;
}
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterForSpecies = isset($_GET['species']) ? trim($_GET['species']) : '';
$filterForSize = isset($_GET['size']) ? trim($_GET['size']) : '';
$filterForMedicalStatus = isset($_GET['medicalStatus']) ? trim($_GET['medicalStatus']) : '';

if (isset($_GET['reset']) && $_GET['reset'] == 'true') {
    $search = '';
    $filterForSpecies = '';
    $filterForSize = '';
    $filterForMedicalStatus = '';
}

$sortColumns = ['name', 'species', 'breed', 'age', 'sex', 'colour', 'size'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sortColumns) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';

$query = "
    SELECT animalID, name, species, breed, age, sex, colour, size, picture, healthStatus
    FROM animal 
    WHERE isDeleted = 0
";
$params = [];
$types = "";

if ($search) {
    $query .= " AND (name LIKE ? OR species LIKE ? OR breed LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if ($filterForSpecies) {
    $query .= " AND species = ?";
    $params[] = $filterForSpecies;
    $types .= "s";
}

if ($filterForSize) {
    $query .= " AND size = ?";
    $params[] = $filterForSize;
    $types .= "s";
}

$query .= " ORDER BY $sort $order";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

function sort_link($column, $label, $currentSort, $currentOrder, $search, $filterForSpecies, $filterForSize) {
    $order = 'asc';
    if ($column === $currentSort) {
        $order = strtolower($currentOrder) === 'asc' ? 'desc' : 'asc';
    }

    $url = "?sort=$column&order=$order";

    if ($search) $url .= "&search=" . urlencode($search);
    if ($filterForSpecies) $url .= "&species=" . urlencode($filterForSpecies);
    if ($filterForSize) $url .= "&size=" . urlencode($filterForSize);
    if (isset($_GET['medicalStatus'])) $url .= "&medicalStatus=" . urlencode($_GET['medicalStatus']);

    $arrow = ($column === $currentSort) ? (strtolower($currentOrder) === 'asc' ? ' ↑' : ' ↓') : '';
    return "<a href='$url'>$label$arrow</a>";
}

// Filter rows based on medical status
$filteredRows = [];

while ($row = $result->fetch_assoc()) {
    // Initialize procedure flags
    $hasBeenVaccinated = false;
    $hasBeenSpayed = false;
    $hasBeenNeutered = false;
    $hasHadEuthanasia = false;  // New flag for euthanasia procedure

    // Query medical report for procedures
    $checkMedicalStatus = $conn->prepare("
        SELECT DISTINCT mp.procedureID, mp.procedureName
        FROM medical_report mr
        JOIN medicalprocedure mp ON mr.procedureID = mp.procedureID
        WHERE mr.animalID = ? AND mr.isDeleted = 0
    ");
    $checkMedicalStatus->bind_param("i", $row['animalID']);
    $checkMedicalStatus->execute();
    $checkOutput = $checkMedicalStatus->get_result();

    while ($procedure = $checkOutput->fetch_assoc()) {
        if ($procedure['procedureName'] === 'Vaccination') {
            $hasBeenVaccinated = true;
        }
        if ($procedure['procedureName'] === 'Spaying') {
            $hasBeenSpayed = true;
        }
        if ($procedure['procedureName'] === 'Neutering') {
            $hasBeenNeutered = true;
        }

        // Check for euthanasia procedure (procedureID = 9)
        if ($procedure['procedureID'] == 9) {
            $hasHadEuthanasia = true;  // Set the flag if euthanasia is performed
        }
    }
    $checkMedicalStatus->close();

    // If euthanasia has been performed, set healthStatus to 'Dead'
    if ($hasHadEuthanasia) {
        $row['healthStatus'] = 'Dead';  // Set health status to Dead
    }

    // Check if animal is complete for adoption eligibility
    $isComplete = $hasBeenVaccinated && ($hasBeenSpayed || $hasBeenNeutered);
    $matchesFilter = true;

    if ($filterForMedicalStatus === 'complete' && !$isComplete) {
        $matchesFilter = false;
    } elseif ($filterForMedicalStatus === 'pending' && $isComplete) {
        $matchesFilter = false;
    }

    if ($matchesFilter) {
        // If complete, show the status as complete
        $row['medicalStatusLabel'] = $isComplete 
            ? "<span style='color:green ;'>Medical complete</span>" 
            : "<span style='color:orange;'>Pending procedures</span>";

        // If euthanasia has been performed, set the availability status
        $row['availabilityStatus'] = $hasHadEuthanasia ? "Not Available" : "Available";

        // Update health status in the database
        $updateStatus = $conn->prepare("UPDATE animal SET healthStatus = ?, status = ? WHERE animalID = ?");
        $updateStatus->bind_param("ssi", $row['healthStatus'], $row['availabilityStatus'], $row['animalID']);
        $updateStatus->execute();
        $updateStatus->close();

        $filteredRows[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <title>Manage Medical Reports</title>
    <link rel="stylesheet" href="sidebar_vet.css">
    <link rel="stylesheet" href="manageMedicalReport.css">
</head>
<body>
<?php
include 'sidebar_vet.php';
?>
<div class="page-heading">
    <h2>Manage Medical Reports</h2>
</div>

<div class="content-container">
    <div class="section-box">
        <h2 class="section-title">Filters</h2>
        <form method="GET" class="filters-form">
            <input type="text" name="search" placeholder="Search by name, species, or breed" 
                value="<?php echo htmlspecialchars($search); ?>" 
                class="filter-input"
            >

            <select name="species" class="filter-select">
                <option value="">All Species</option>
                <option value="Dog" <?php if ($filterForSpecies == 'Dog') echo 'selected'; ?>>Dog</option>
                <option value="Cat" <?php if ($filterForSpecies == 'Cat') echo 'selected'; ?>>Cat</option>
                <option value="Horse" <?php if ($filterForSpecies == 'Horse') echo 'selected'; ?>>Horse</option>
                <option value="Donkey" <?php if ($filterForSpecies == 'Donkey') echo 'selected'; ?>>Donkey</option>
                <option value="Goat" <?php if ($filterForSpecies == 'Goat') echo 'selected'; ?>>Goat</option>
            </select>

            <select name="size" class="filter-select">
                <option value="">All Sizes</option>
                <option value="Small" <?php if ($filterForSize == 'Small') echo 'selected'; ?>>Small</option>
                <option value="Medium" <?php if ($filterForSize == 'Medium') echo 'selected'; ?>>Medium</option>
                <option value="Large" <?php if ($filterForSize == 'Large') echo 'selected'; ?>>Large</option>
            </select>

            <select name="medicalStatus" class="filter-select">
                <option value="">All Medical Statuses</option>
                <option value="complete" <?php if ($filterForMedicalStatus === 'complete') echo 'selected'; ?>>Medical Complete</option>
                <option value="pending" <?php if ($filterForMedicalStatus === 'pending') echo 'selected'; ?>>Pending Procedures</option>
            </select>

            <button type="submit" class="filter-button">Search</button>
            <a href="manageMedicalReport.php?reset=true" class="filter-button" style="background-color:#9CA3AF;">Reset</a>
        </form>
    </div>

    <?php 
    if ($msg) {
        echo "<p style='color:green; font-weight: 600'>$msg</p>";
    } elseif ($error) {
        echo "<p style='color:red; font-weight: 600'>$error</p>";
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>Picture</th>
                <th><?php echo sort_link('name', 'Name', $sort, $order, $search, $filterForSpecies, $filterForSize); ?></th>
                <th><?php echo sort_link('species', 'Species', $sort, $order, $search, $filterForSpecies, $filterForSize); ?></th>
                <th><?php echo sort_link('breed', 'Breed', $sort, $order, $search, $filterForSpecies, $filterForSize); ?></th>
                <th><?php echo sort_link('age', 'Age', $sort, $order, $search, $filterForSpecies, $filterForSize); ?></th>
                <th><?php echo sort_link('sex', 'Sex', $sort, $order, $search, $filterForSpecies, $filterForSize); ?></th>
                <th><?php echo sort_link('colour', 'Color', $sort, $order, $search, $filterForSpecies, $filterForSize); ?></th>
                <th><?php echo sort_link('size', 'Size', $sort, $order, $search, $filterForSpecies, $filterForSize); ?></th>
                <th>Procedures Required for Adoption</th> <!-- Existing Column for Adoption Procedures -->
                <th>Health Status</th> <!-- New Column for Health Status -->
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (count($filteredRows) > 0) {
                foreach ($filteredRows as $row) {
                    $imagePath = "../pictures/animals/" . htmlspecialchars($row['picture']);
                    $imageTag = (file_exists($imagePath) && !empty($row['picture'])) 
                        ? "<img src='$imagePath' alt='Animal Image'>" 
                        : "<img src='../pictures/paw.jpg' alt='Default Paw Image'>";

                    // Define the color based on healthStatus
                    $healthStatusColor = 'black';  // Default color
                    $healthStatusText = htmlspecialchars($row['healthStatus']);
                    
                    if ($healthStatusText == 'Healthy') {
                        $healthStatusColor = 'green'; // Healthy = Green
                    } elseif ($healthStatusText == 'Injured' || $healthStatusText == 'Sick') {
                        $healthStatusColor = 'red';  // Injured/Sick = Red
                    }

                    echo "<tr>
                        <td>$imageTag</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['species']) . "</td>
                        <td>" . htmlspecialchars($row['breed']) . "</td>
                        <td>" . htmlspecialchars($row['age']) . "</td>
                        <td>" . htmlspecialchars($row['sex']) . "</td>
                        <td>" . htmlspecialchars($row['colour']) . "</td>
                        <td>" . htmlspecialchars($row['size']) . "</td>
                        <td>{$row['medicalStatusLabel']}</td>
                        <td style='color: $healthStatusColor;font-weight: 600'>$healthStatusText</td> <!-- Health Status Column -->
                        <td><a href='animalReports.php?animalID=" . $row['animalID'] . "' class='updateBtn'>View Reports</a></td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='11'>No animals found matching the criteria.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php $stmt->close(); ?>
