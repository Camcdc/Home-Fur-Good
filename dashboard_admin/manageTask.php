<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseConnection.php';

// --- FORM HANDLING ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_task_status'])) {
    $assignTaskID = $_POST['assignTaskID'];
    $newStatus = $_POST['new_status'];
    
    $stmt = $conn->prepare("UPDATE assignedtask SET completionStatus = ? WHERE assignTaskID = ?");
    $stmt->bind_param("ii", $newStatus, $assignTaskID);
    $stmt->execute();
    $stmt->close();
    // Preserve filters on redirect
    header("Location: " . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    exit();
}

// --- FILTER, SEARCH, AND SORT LOGIC ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterForStatus = isset($_GET['status']) ? trim($_GET['status']) : '';

$sortColumns = ['volunteer', 'taskName', 'animalName', 'completionStatus'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sortColumns) ? $_GET['sort'] : 'assignTaskID';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

// Helper function to generate sorting links
function sort_link($column, $label, $currentSort, $currentOrder) {
    $order = ($column === $currentSort && strtolower($currentOrder) === 'asc') ? 'desc' : 'asc';
    $arrow = ($column === $currentSort) ? (strtolower($currentOrder) === 'asc' ? ' ↑' : ' ↓') : '';
    
    $queryParams = http_build_query(array_merge($_GET, ['sort' => $column, 'order' => $order]));
    return "<a href='?{$queryParams}'>{$label}{$arrow}</a>";
}

// Build the dynamic SQL query
$query = "SELECT at.assignTaskID, at.taskName, at.completionStatus, 
                 CONCAT(u.fname, ' ', u.sname) as volunteer, a.name as animalName
          FROM assignedtask at
          JOIN user u ON at.userID = u.userID
          LEFT JOIN animal a ON at.animalID = a.animalID
          WHERE 1=1";

$params = [];
$types = "";

if ($search) {
    $query .= " AND (CONCAT(u.fname, ' ', u.sname) LIKE ? OR at.taskName LIKE ? OR a.name LIKE ?)";
    $searchTerm = '%' . $search . '%';
    array_push($params, $searchTerm, $searchTerm, $searchTerm);
    $types .= "sss";
}

if ($filterForStatus !== '') {
    $query .= " AND at.completionStatus = ?";
    $params[] = $filterForStatus;
    $types .= "i";
}

$query .= " ORDER BY $sort $order";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$tasks_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPCA Admin - Manage Volunteer Tasks</title>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="../dashboard_vet/manageMedicalReport.css"> 
</head>
<body>
    <?php include 'sidebar_admin.php'; ?>
    <div class="page-heading">
        <h2>Manage Volunteer Tasks</h2>
    </div>

    <div class="content-container">
        <div class="section-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="section-title" style="margin: 0;">Filter and Sort Tasks</h2>
                <a href="volunteer_admin.php" class="filter-button">← Back to Dashboard</a>
            </div>
            <form method="GET" class="filters-form">
                <input type="text" name="search" placeholder="Search by volunteer, task, or animal" 
                       value="<?php echo htmlspecialchars($search); ?>" class="filter-input">

                <select name="status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="0" <?php if ($filterForStatus === '0') echo 'selected'; ?>>In Progress</option>
                    <option value="1" <?php if ($filterForStatus === '1') echo 'selected'; ?>>Completed</option>
                </select>

                <button type="submit" class="filter-button">Filter</button>
                <a href="manageTasks.php" class="filter-button" style="background-color:#9CA3AF;">Reset</a>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th><?php echo sort_link('volunteer', 'Volunteer', $sort, $order); ?></th>
                    <th><?php echo sort_link('taskName', 'Task', $sort, $order); ?></th>
                    <th><?php echo sort_link('animalName', 'Animal', $sort, $order); ?></th>
                    <th><?php echo sort_link('completionStatus', 'Status', $sort, $order); ?></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tasks_result && $tasks_result->num_rows > 0): ?>
                    <?php while ($task = $tasks_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['volunteer']); ?></td>
                        <td><?php echo htmlspecialchars($task['taskName']); ?></td>
                        <td><?php echo htmlspecialchars($task['animalName'] ?? 'N/A'); ?></td>
                        <td>
                            <?php $status = $task['completionStatus'] == 1 ? 'Completed' : 'In Progress'; ?>
                            <span style="font-weight: 600; color: <?php echo $status === 'Completed' ? '#166534' : '#92400e'; ?>;">
                                <?php echo $status; ?>
                            </span>
                        </td>
                        <td class='actions-cell'>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='assignTaskID' value='<?php echo $task['assignTaskID']; ?>'>
                                <?php if ($task['completionStatus'] == 0): ?>
                                    <input type='hidden' name='new_status' value='1'>
                                    <button type='submit' name='update_task_status' class='updateBtn'>Mark as Complete</button>
                                <?php else: ?>
                                    <input type='hidden' name='new_status' value='0'>
                                    <button type='submit' name='update_task_status' class='updateBtn' style="background-color:#6c757d;">Reset</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No tasks found matching your criteria.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>