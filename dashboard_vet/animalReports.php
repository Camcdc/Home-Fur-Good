<?php
require_once '../databaseConnection.php';


if (!isset($_GET['animalID']) || empty($_GET['animalID'])) {
    echo "<p>No animal selected.</p>";
    exit();
}

$animalID = intval($_GET['animalID']);

// Fetch animal details
$stmt = $conn->prepare("SELECT * FROM animal WHERE animalID = ? AND isDeleted = 0 LIMIT 1");
$stmt->bind_param("i", $animalID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Animal not found.</p>";
    exit();
}

$animal = $result->fetch_assoc();
$stmt->close();

// Filter inputs
$sortOption       = (isset($_GET['sort']) && $_GET['sort'] == 'desc') ? 'DESC' : 'ASC';
$filterProcedure  = $_GET['procedure'] ?? '';
$filterStartDate  = $_GET['startDate'] ?? '';
$filterEndDate    = $_GET['endDate'] ?? '';

// Build WHERE conditions dynamically
$where = "WHERE medical_report.animalID = ? AND medical_report.isDeleted = 0";
$params = [$animalID];
$types = "i";

if (!empty($filterProcedure)) {
    $where .= " AND medicalprocedure.procedureName = ?";
    $params[] = $filterProcedure;
    $types .= "s";
}

if ($filterStartDate && $filterEndDate) {
    $where .= " AND medical_report.reportDate BETWEEN ? AND ?";
    $params[] = $filterStartDate;
    $params[] = $filterEndDate;
    $types .= "ss";
} elseif ($filterStartDate) {
    $where .= " AND medical_report.reportDate >= ?";
    $params[] = $filterStartDate;
    $types .= "s";
} elseif ($filterEndDate) {
    $where .= " AND medical_report.reportDate <= ?";
    $params[] = $filterEndDate;
    $types .= "s";
}

// Final SQL with join
$sql = "
    SELECT 
        medical_report.reportID,
        medical_report.reportDate,
        medical_report.diagnosis,
        CONCAT(user.Fname, ' ', user.Sname) AS staffName,
        medicalprocedure.procedureName
    FROM medical_report
    JOIN user ON medical_report.userID = user.userID
    JOIN medicalprocedure ON medical_report.procedureID = medicalprocedure.procedureID
    $where
    ORDER BY medical_report.reportID $sortOption
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$reports = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <title>Medical Reports for <?php echo htmlspecialchars($animal['name']); ?></title>
    <link rel="stylesheet" href="sidebar_vet.css">
    <link rel="stylesheet" href="animalReports.css">
</head>
<body>
<?php
include 'sidebar_vet.php';
?>
<a href="manageMedicalReport.php" class="back-link-top">← Back to all Medical Reports</a>

<h1>Animal Report</h1>

<!-- Animal Details -->
<div class="animal-details-card">
    <div class="animal-image">
        <img src="../pictures/animals/<?php echo htmlspecialchars($animal['picture']); ?>" alt="Animal Picture">
    </div>
    <div class="animal-info">
        <div class="animal-name-header">
        <h2><?php echo htmlspecialchars($animal['name']); ?></h2>
        <hr class="animal-name-underline">
        </div>
        <div class="info-group">
            <div><strong>Age:</strong> <?php echo htmlspecialchars($animal['age']); ?></div>
            <div><strong>Sex:</strong> <?php echo htmlspecialchars($animal['sex']); ?></div>
            <div><strong>Species:</strong> <?php echo htmlspecialchars($animal['species']); ?></div>
            <div><strong>Size:</strong> <?php echo htmlspecialchars($animal['size']); ?></div>
            <div><strong>Breed:</strong> <?php echo htmlspecialchars($animal['breed']); ?></div>
            <div><strong>Color:</strong> <?php echo htmlspecialchars($animal['colour']); ?></div>
        </div>
    </div>
</div>


<a href="createReport1.php?animalID=<?php echo $animalID; ?>" class="create-floating">+ New Report</a>



<!-- Filter Form -->
 <h2>Medical Reports</h2>
<div class="filter-container">
    <form class="filter-form" method="GET">
        <input type="hidden" name="animalID" value="<?php echo $animalID; ?>">

        <div class="filter-form-group">
            <label for="sort">Sort by Report ID:</label>
            <select name="sort">
                <option value="asc" <?php if ($sortOption == 'ASC') echo 'selected'; ?>>Ascending</option>
                <option value="desc" <?php if ($sortOption == 'DESC') echo 'selected'; ?>>Descending</option>
            </select>
        </div>

        <div class="filter-form-group">
            <label for="procedure">Procedure:</label>
            <select name="procedure">
                <option value="">All Procedures</option>
                <?php
                $procedures = ['Vaccination', 'Surgery', 'Health Checkup', 'Wound Treatment', 'Euthanasia'];
                foreach ($procedures as $procedure) {
                    $selected = $filterProcedure === $procedure ? 'selected' : '';
                    echo "<option value=\"$procedure\" $selected>$procedure</option>";
                }
                ?>
            </select>
        </div>

        <div class="filter-form-group">
            <label for="startDate">Start Date:</label>
            <input type="date" name="startDate" value="<?php echo htmlspecialchars($filterStartDate); ?>">
        </div>

        <div class="filter-form-group">
            <label for="endDate">End Date:</label>
            <input type="date" name="endDate" value="<?php echo htmlspecialchars($filterEndDate); ?>">
        </div>

        <div class="filter-form-group">
            <button type="submit">Apply Filters</button>
        </div>
    </form>
</div>

<!-- Medical Reports Table -->
<?php if ($reports->num_rows > 0): ?>
    <table>
        <tr>
            <th>Report ID</th>
            <th>Report Date</th>
            <th>Procedure</th>
            <th>Staff</th>
            <th>Diagnosis</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $reports->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['reportID']); ?></td>
                <td><?php echo htmlspecialchars($row['reportDate']); ?></td>
                <td><?php echo htmlspecialchars($row['procedureName']); ?></td>
                <td><?php echo htmlspecialchars($row['staffName']); ?></td>
                <td><?php echo htmlspecialchars($row['diagnosis']); ?></td>
                <td>
                    <a href="viewReport1.php?reportID=<?php echo $row['reportID']; ?>" class="action-link viewBtn">View</a>
                    <a href="updateReport.php?reportID=<?php echo $row['reportID']; ?>" class="action-link updateBtn">Update</a>
                    <a href="deleteReport1.php?reportID=<?php echo $row['reportID']; ?>" class="action-link deleteBtn" onclick="return confirm('Are you sure you want to delete this report?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No medical reports found for this animal.</p>
<?php endif; ?>


<?php 
$stmt->close();
$conn->close();
?>

</body>
</html>
