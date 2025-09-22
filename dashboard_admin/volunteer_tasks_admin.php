<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseConnection.php';

// Form handling logic for task status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_task_status'])) {
    $assignTaskID = $_POST['assignTaskID'];
    $newStatus = $_POST['new_status']; // Will be 0 for reset, 1 for complete
    
    $stmt = $conn->prepare("UPDATE assignedtask SET completionStatus = ? WHERE assignTaskID = ?");
    $stmt->bind_param("ii", $newStatus, $assignTaskID);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']); // Reload the page to see changes
    exit();
}

// Fetch all assigned tasks for display
$tasks_sql = "SELECT at.assignTaskID, at.taskName, at.completionStatus, 
                     u.Fname as fname, u.Sname as sname, a.name as animalName
              FROM assignedtask at
              JOIN user u ON at.userID = u.userID
              LEFT JOIN animal a ON at.animalID = a.animalID
              ORDER BY at.completionStatus ASC, at.assignTaskID DESC";
$tasks_result = $conn->query($tasks_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPCA Admin - Volunteer Tasks</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>Volunteer Task Management</h1>
        <p>Oversee all current and completed volunteer tasks</p>
    </div>
    
    <div class="admin-section">
        <a href="volunteer_admin.php" class="btn btn-secondary" style="margin-bottom: 20px;">← Back to Dashboard</a>
        <h2>Overview of All Current Volunteer Tasks</h2>
        <table>
            <thead>
                <tr>
                    <th>Volunteer</th>
                    <th>Task</th>
                    <th>Related Animal</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tasks_result && $tasks_result->num_rows > 0): ?>
                    <?php while ($task = $tasks_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['fname'] . ' ' . $task['sname']); ?></td>
                        <td><?php echo htmlspecialchars($task['taskName']); ?></td>
                        <td><?php echo htmlspecialchars($task['animalName'] ?? 'N/A'); ?></td>
                        <td>
                            <?php $status = $task['completionStatus'] == 1 ? 'Completed' : 'In Progress'; ?>
                            <span class='status status-<?php echo strtolower(str_replace(' ', '-', $status)); ?>'><?php echo $status; ?></span>
                        </td>
                        <td class='actions-cell'>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='assignTaskID' value='<?php echo $task['assignTaskID']; ?>'>
                                <?php if ($task['completionStatus'] == 0): ?>
                                    <input type='hidden' name='new_status' value='1'>
                                    <button type='submit' name='update_task_status' class='btn btn-primary btn-small'>Mark as Complete</button>
                                <?php else: ?>
                                    <input type='hidden' name='new_status' value='0'>
                                    <button type='submit' name='update_task_status' class='btn btn-secondary btn-small'>Reset</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No tasks have been assigned yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>