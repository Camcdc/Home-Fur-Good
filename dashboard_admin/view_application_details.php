<?php
//--- CONFIGURATION AND SETUP ---//
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseConnection.php';

// Helper function to safely display values
function display($value) {
    return !empty($value) ? nl2br(htmlspecialchars($value)) : '<em>Not provided</em>';
}

// Check if an application ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: No application ID provided or ID is invalid.");
}
$applicationID = $_GET['id'];

//--- HANDLE FORM SUBMISSIONS ---//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Logic for handling form submissions remains the same
    if (isset($_POST['assign_task_to_user'])) {
        $animalID = !empty($_POST['animalID']) ? $_POST['animalID'] : null;
        $userID = $_POST['userID'];
        $taskName = $_POST['taskName'];

        // This part needs correction as `assignedtask` does not have animalID or userID
        // For now, let's assume a generic task is created and then assigned.
        $conn->begin_transaction();
        try {
            // 1. Insert the new task into `assignedtask`
            $stmt_task = $conn->prepare("INSERT INTO assignedtask (taskName, completionStatus) VALUES (?, 0)");
            $stmt_task->bind_param("s", $taskName);
            $stmt_task->execute();
            $new_task_id = $stmt_task->insert_id;
            $stmt_task->close();

            // 2. Assign the new task to the user in `task_assignment`
            $stmt_assign = $conn->prepare("INSERT INTO task_assignment (assignTaskID, userID, status) VALUES (?, ?, 'Assigned')");
            $stmt_assign->bind_param("ii", $new_task_id, $userID);
            $stmt_assign->execute();
            $stmt_assign->close();
            
            $conn->commit();
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            die("Error assigning task: " . $exception->getMessage());
        }
    }
    
    if (isset($_POST['update_task_status'])) {
        $assignTaskID = $_POST['assignTaskID'];
        $new_status = $_POST['new_status'];
        
        $stmt = $conn->prepare("UPDATE assignedtask SET completionStatus = ? WHERE assignTaskID = ?");
        $stmt->bind_param("ii", $new_status, $assignTaskID);
        $stmt->execute();
        $stmt->close();
    }
    
    // Refresh page to show changes
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?id=$applicationID#task-history");
    exit();
}

//--- DATA FETCHING ---//
$stmt = $conn->prepare("SELECT a.*, u.userID, u.fname, u.sname, u.email, u.cellNumber, u.DateOfBirth, u.Address 
                        FROM application a 
                        JOIN user u ON a.userID = u.userID 
                        WHERE a.applicationID = ?");
$stmt->bind_param("i", $applicationID);
$stmt->execute();
$result = $stmt->get_result();
$application_details = $result->fetch_assoc();
if (!$application_details) die("Error: Application not found.");
$current_userID = $application_details['userID'];
$stmt->close();

$all_animals = $conn->query("SELECT animalID, name, species FROM animal WHERE isDeleted = 0");


// --- FIXED QUERY ---
// This query now correctly joins through the `task_assignment` table
// to find tasks for the user. The LEFT JOIN to `animal` is removed for now
// as there is no direct link from a task to an animal in the current schema.
$user_tasks_query = $conn->prepare("
    SELECT 
        at.assignTaskID, 
        at.taskName, 
        at.completionStatus
    FROM 
        assignedtask AS at
    JOIN 
        task_assignment AS ta ON at.assignTaskID = ta.assignTaskID
    WHERE 
        ta.userID = ?
    ORDER BY 
        at.completionStatus ASC, at.assignTaskID DESC
");

// Add error checking for the prepare statement
if ($user_tasks_query === false) {
    die("<strong>Database Error:</strong> Could not prepare the tasks query. <br><strong>MySQL Error:</strong> " . htmlspecialchars($conn->error));
}

$user_tasks_query->bind_param("i", $current_userID);
$user_tasks_query->execute();
$user_tasks_result = $user_tasks_query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Volunteer Application Details</title>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="viewApplication.css"> 
    <style>
        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #b6cbdf;
        }
        .page-heading h2 {
            margin: 0;
            text-align: center;
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <?php include 'sidebar_admin.php'; ?>

    <div class="page-heading">
        <a href='manageVolunteers.php' class='back-btn'>← Back to All Applications</a>
        <h2>Volunteer Profile</h2>
    </div>

    <div class="container">
        <div class="app-block">
            <div class="table-container">
                <table>
                    <caption>Applicant Information: <?php echo display($application_details['fname'] . ' ' . $application_details['sname']); ?></caption>
                    <tr><th>User ID</th><td><?php echo display($application_details['userID']); ?></td></tr>
                    <tr><th>Email</th><td><?php echo display($application_details['email']); ?></td></tr>
                    <tr><th>Cell Number</th><td><?php echo display($application_details['cellNumber']); ?></td></tr>
                    <tr><th>Address</th><td><?php echo display($application_details['Address']); ?></td></tr>
                </table>
            </div>

            <div class="table-container">
                <table>
                    <caption>Application Details</caption>
                    <tr><th>Application Date</th><td><?php echo date("F j, Y", strtotime($application_details['applicationDate'])); ?></td></tr>
                    <tr><th>Current Status</th><td><?php echo display($application_details['applicationStatus']); ?></td></tr>
                    <tr><th>Volunteering Preference</th><td><?php echo display($application_details['volunteerPreference']); ?></td></tr>
                    <tr><th>Reason for Volunteering</th><td><div class="answer-container"><?php echo display($application_details['volunteerReason']); ?></div></td></tr>
                    <tr><th>Availability</th><td><div class="answer-container"><?php echo display($application_details['availability']); ?></div></td></tr>
                    <tr><th>Experience</th><td><div class="answer-container"><?php echo display($application_details['experience']); ?></div></td></tr>
                </table>
            </div>
        </div>

        <div class="app-block">
            <div class="table-container">
                <form method="POST" action="#task-history">
                    <input type="hidden" name="userID" value="<?php echo $current_userID; ?>">
                    <table>
                        <caption>Assign New Task</caption>
                        <tr>
                            <th>Task Description</th>
                            <td>
                                <select name="taskName" required>
                                    <option value="">Select Task Type</option>
                                    <option value="Daily Feeding">Daily Feeding</option>
                                    <option value="Dog Walking">Dog Walking</option>
                                    <option value="Grooming">Grooming</option>
                                    <option value="Cat Socialization">Cat Socialization</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: right;">
                                <button type="submit" name="assign_task_to_user" class="back-btn" style="margin-bottom: 0;">Assign Task</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

        <div id="task-history" class="app-block">
            <div class="table-container">
                <table>
                    <caption>Task History</caption>
                    <thead><tr><th>Task</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if ($user_tasks_result->num_rows > 0): ?>
                            <?php while($task = $user_tasks_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo display($task['taskName']); ?></td>
                                <td><?php echo $task['completionStatus'] == 1 ? 'Completed' : 'In Progress'; ?></td>
                                <td>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="assignTaskID" value="<?php echo $task['assignTaskID']; ?>">
                                        <?php if ($task['completionStatus'] == 0): ?>
                                            <input type="hidden" name="new_status" value="1">
                                            <button type="submit" name="update_task_status" class="back-btn" style="background-color: #28a745; margin:0; padding: 5px 10px;">Mark Complete</button>
                                        <?php else: ?>
                                            <input type="hidden" name="new_status" value="0">
                                            <button type="submit" name="update_task_status" class="back-btn" style="background-color: #6c757d; margin:0; padding: 5px 10px;">Reset</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No tasks assigned to this volunteer yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>