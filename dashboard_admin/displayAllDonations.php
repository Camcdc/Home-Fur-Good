<?php  
 session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
require_once '../databaseConnection.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$causeType = isset($_GET['causeType']) ? trim($_GET['causeType']) : '';

$sortColumns = ['donationID', 'amount', 'date', 'recurring', 'userID', 'causeType', 'status'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sortColumns) ? $_GET['sort'] : 'donationID';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';

$query = "
    SELECT * FROM donations 
    WHERE isDeleted = 0
";
$params = [];
$types = "";

if ($search) {
    $query .= " AND (donationID LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $types .= "s";
}

if ($status) {
    $query .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

if ($causeType) {
    $query .= " AND causeType = ?";
    $params[] = $causeType;
    $types .= "s";
}

$query .= " ORDER BY $sort $order";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

function sort_link($column, $label, $currentSort, $currentOrder, $search, $status, $causeType) {
    $order = 'asc';
    $arrow = ' ↑'; // Default arrow (ascending order)

    if ($column === $currentSort) {
        // If the current column is the one being sorted, toggle the arrow direction
        $order = strtolower($currentOrder) === 'asc' ? 'desc' : 'asc';
        $arrow = strtolower($currentOrder) === 'asc' ? ' ↓' : ' ↑';
    }

    $url = "?sort=$column&order=$order";

    if ($search) $url .= "&search=" . urlencode($search);
    if ($status) $url .= "&status=" . urlencode($status);
    if ($causeType) $url .= "&causeType=" . urlencode($causeType);

    return "<a href='$url'>$label$arrow</a>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>All Donation Records</title>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="displayAllRecords.css">
    <style>
        td.cause-cell {
            max-width: 180px;
            min-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        th {
            cursor: pointer;
        }

        .sort-arrow {
            font-size: 12px;
            margin-left: 5px;
        }

        .asc {
            content: " ↑";
        }

        .desc{
            content: " ↓";
        }
    </style>
</head>

<body>
<?php
    require '../databaseconnection.php';
    include 'sidebar_admin.php';
?>

<div class="page-heading">
    <h2>View Donations</h2>
</div>

<?php
    // Filters UI
    echo '<div class="content-container">
        <div class="section-box">
            <h2 class="section-title">Filters</h2>
            <form method="GET" action="" class="filters-form">
                <input type="text" name="search" placeholder="Search Donation ID..." 
                    value="' . (isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '') . '" 
                    class="filter-input"
                >
                <select name="status" class="filter-select">
                    <option value="">Status</option>
                    <option value="pending" ' . ((isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : '') . '>Pending</option>
                    <option value="confirmed" ' . ((isset($_GET['status']) && $_GET['status'] === 'confirmed') ? 'selected' : '') . '>Confirmed</option>
                </select>
                <select name="causeType" class="filter-select">
                    <option value="">All Causes</option>
                    <option value="Animal Protection" ' . ((isset($_GET['causeType']) && $_GET['causeType'] === 'Animal Protection') ? 'selected' : '') . '>Animal Protection</option>
                    <option value="Animal Rights" ' . ((isset($_GET['causeType']) && $_GET['causeType'] === 'Animal Rights') ? 'selected' : '') . '>Animal Rights</option>
                    <option value="Animal Welfare" ' . ((isset($_GET['causeType']) && $_GET['causeType'] === 'Animal Welfare') ? 'selected' : '') . '>Animal Welfare</option>
                </select>
                <button type="submit" class="filter-button">Search</button>
            </form>
        </div>';

    if ($result && $result->num_rows > 0) {
        echo "<center>
        <div class='notification'>
    <p><strong>Note:</strong> You can sort the donation records by clicking on the column headers (e.g., Donation ID, Amount, Date). The table will toggle between ascending and descending order each time you click the header.</p>
</div>

            <table width='75%' bgcolor='lightblue'>
                <tr bgcolor='orange'>
                    <th>" . sort_link('donationID', 'Donation ID', $sort, $order, $search, $status, $causeType) . "</th>
                    <th>" . sort_link('amount', 'Amount (R)', $sort, $order, $search, $status, $causeType) . "</th>
                    <th>" . sort_link('date', 'Date', $sort, $order, $search, $status, $causeType) . "</th>
                    <th>" . sort_link('recurring', 'Recurring', $sort, $order, $search, $status, $causeType) . "</th>
                    <th>" . sort_link('userID', 'User ID', $sort, $order, $search, $status, $causeType) . "</th>
                    <th class='cause-cell'>" . sort_link('causeType', 'Cause Type', $sort, $order, $search, $status, $causeType) . "</th>
                    <th>" . sort_link('status', 'Status', $sort, $order, $search, $status, $causeType) . "</th>
                    <th>Acknowledgement</th>
                    <th>Update</th>
                </tr>";

        // Display the rows
        while ($row = $result->fetch_assoc()) {
            if ($row["isDeleted"] == 0) { ?>
            <tr>
                <td><?php echo $row["donationID"];?></td>
                <td><?php echo 'R' . number_format($row["amount"], 2);?></td>
                <td><?php echo htmlspecialchars($row["date"]);?></td>
                <td><?php echo ($row["recurring"] ? 'Yes' : 'No');?></td>
                <td><?php echo htmlspecialchars($row["userID"]);?></td>
                <td class="cause-cell"><?php echo htmlspecialchars($row["causeType"]);?></td>
                <td><?php echo htmlspecialchars($row["status"]);?></td>
                <td>
                    <?php
                    if ($row["amount"] >= 10000) {
                        echo '<span style="color: #d35400; font-weight: bold;">🌟 Thank you for your generosity!</span>';
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td>
                    <a href="DonationUpdaterecord.php?donationID=<?php echo $row["donationID"] ?>">
                        <button class="update-button" onclick="return confirm('Are you sure you want to update this record?');">Update</button>
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
