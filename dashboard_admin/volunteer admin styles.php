<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
//--- CONFIGURATION AND SETUP ---//

// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include 'sidebar_admin.php';
include '../databaseConnection.php';


//--- DATA PROCESSING LOGIC (HANDLING FORM SUBMISSIONS) ---//

// Handle application status updates (Approve/Reject)
if (isset($_POST['update_application_status'])) {
    // Get data from the form
    $applicationID = $_POST['applicationID'];
    $new_status = $_POST['new_status'];
    
    // Prepare the SQL statement with placeholders (?) to prevent SQL injection
    $stmt = $conn->prepare("UPDATE application SET applicationStatus = ? WHERE applicationID = ?");
    // Bind the variables to the placeholders ('s' for string, 'i' for integer)
    $stmt->bind_param("si", $new_status, $applicationID);
    
    // Execute the statement and provide feedback to the user
    if ($stmt->execute()) {
        echo "<script>alert('Application status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating application status: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    // Close the statement to free up resources
    $stmt->close();
}

// Handle resetting an application status back to 'Pending'
if (isset($_POST['reset_application_status'])) {
    // Get data from the form
    $applicationID = $_POST['applicationID'];
    $pending_status = 'Pending'; // Set the status to 'Pending'
    
    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE application SET applicationStatus = ? WHERE applicationID = ?");
    // Bind the variables
    $stmt->bind_param("si", $pending_status, $applicationID);
    
    // Execute and provide feedback
    if ($stmt->execute()) {
        echo "<script>alert('Application status reset to Pending successfully!');</script>";
    } else {
        echo "<script>alert('Error resetting application status: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    // Close the statement
    $stmt->close();
}

// Handle new task assignment
if (isset($_POST['assign_task'])) {
    // Get all required data from the form
    $animalID = $_POST['animalID'];
    $userID = $_POST['userID']; 
    $taskName = $_POST['taskName'];
    $totalVolunteers = $_POST['totalVolunteers'];
    $completionStatus = 0; // 0 means 'In Progress'

    // Prepare the INSERT statement
    $stmt = $conn->prepare("INSERT INTO assignedtask (animalID, userID, completionStatus, totalVolunteers, taskName) VALUES (?, ?, ?, ?, ?)");
    // Bind variables (i for integer, s for string)
    $stmt->bind_param("iiiss", $animalID, $userID, $completionStatus, $totalVolunteers, $taskName);
    
    // Execute and provide feedback
    if ($stmt->execute()) {
        echo "<script>alert('Task assigned successfully!');</script>";
    } else {
        echo "<script>alert('Error assigning task: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    // Close the statement
    $stmt->close();
}

// Handle task status updates (e.g., marking as complete)
if (isset($_POST['update_task_status'])) {
    // Get data from the form
    $assignTaskID = $_POST['assignTaskID'];
    $completion_status = $_POST['completion_status'];
    
    // Prepare the UPDATE statement
    $stmt = $conn->prepare("UPDATE assignedtask SET completionStatus = ? WHERE assignTaskID = ?");
    // Bind variables
    $stmt->bind_param("ii", $completion_status, $assignTaskID);
    
    // Execute and provide feedback
    if ($stmt->execute()) {
        echo "<script>alert('Task status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating task status: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    // Close the statement
    $stmt->close();
}

// **NEW** Handle resetting a task status back to 'In Progress'
if (isset($_POST['reset_task_status'])) {
    // Get the task ID from the form
    $assignTaskID = $_POST['assignTaskID'];
    $in_progress_status = 0; // 0 means 'In Progress'

    // Prepare the UPDATE statement
    $stmt = $conn->prepare("UPDATE assignedtask SET completionStatus = ? WHERE assignTaskID = ?");
    // Bind the variables
    $stmt->bind_param("ii", $in_progress_status, $assignTaskID);

    // Execute and provide feedback
    if ($stmt->execute()) {
        echo "<script>alert('Task has been reset to In Progress!');</script>";
    } else {
        echo "<script>alert('Error resetting task: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    // Close the statement
    $stmt->close();
}


//--- DATA FETCHING FOR DISPLAY ---//

// Fetch all volunteer applications
$volunteer_applications = $conn->query("SELECT a.*, u.fname, u.sname, u.email, u.cellNumber 
                                       FROM application a 
                                       LEFT JOIN user u ON a.userID = u.userID 
                                       WHERE a.applicationType = 'Volunteer' AND a.isDeleted = 0 
                                       ORDER BY a.applicationID");
                                    
// Fetch users who are approved volunteers to populate dropdowns
$approved_volunteers = $conn->query("SELECT u.userID, u.fname, u.sname, u.email, u.cellNumber 
                                   FROM user u
                                   JOIN application a ON u.userID = a.userID
                                   WHERE a.applicationType = 'Volunteer' 
                                   AND a.applicationStatus = 'Approved'
                                   AND u.isDeleted = 0");

// Fetch all animals for task assignment dropdown
$all_animals = $conn->query("SELECT animalID, name, species, breed FROM animal WHERE isDeleted = 0");

// Fetch all assigned tasks and join with user and animal tables for display
$all_tasks = $conn->query("SELECT 
        at.assignTaskID, at.animalID, at.userID, at.taskName, 
        at.completionStatus, at.totalVolunteers, u.fname, u.sname,
        a.name AS animal_name, a.species
    FROM assignedtask at
    LEFT JOIN user u ON at.userID = u.userID
    LEFT JOIN animal a ON at.animalID = a.animalID
    ORDER BY at.completionStatus, at.assignTaskID DESC");

// Fetch statistics for the header section
$volunteer_stats = $conn->query("SELECT 
    COUNT(*) as total_applications,
    SUM(CASE WHEN applicationStatus = 'Pending' THEN 1 ELSE 0 END) as pending_applications,
    SUM(CASE WHEN applicationStatus = 'Approved' THEN 1 ELSE 0 END) as approved_applications,
    SUM(CASE WHEN applicationStatus = 'Rejected' THEN 1 ELSE 0 END) as rejected_applications
    FROM application WHERE applicationType = 'Volunteer' AND isDeleted = 0")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>SPCA Admin - Volunteer Management</title>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    
    <div class="header">
        <h1>SPCA Admin Dashboard - Volunteer Management</h1>
    </div>
    
    <div class="stats-section">
        <h2>Volunteer Statistics</h2>
        <table class="stats-table">
            <tr>
                <td><strong>Total Applications:</strong></td>
                <td><span class="highlight-number"><?php echo $volunteer_stats['total_applications']; ?></span></td>
            </tr>
            <tr>
                <td><strong>Pending Applications:</strong></td>
                <td><span class="highlight-number status-pending"><?php echo $volunteer_stats['pending_applications']; ?></span></td>
            </tr>
            <tr>
                <td><strong>Approved Volunteers:</strong></td>
                <td><span class="highlight-number status-approved"><?php echo $volunteer_stats['approved_applications']; ?></span></td>
            </tr>
            <tr>
                <td><strong>Rejected Applications:</strong></td>
                <td><span class="highlight-number status-rejected"><?php echo $volunteer_stats['rejected_applications']; ?></span></td>
            </tr>
        </table>
    </div>
    
    <div class="admin-section">
        <h2>Manage Volunteer Applications</h2>
        <table>
            <tr>
                <th>Application ID</th>
                <th>Applicant Name</th>
                <th>Email</th>
                <th>Cell Number</th>
                <th>Current Status</th>
                <th>Action</th>
            </tr>
            <?php
            if ($volunteer_applications->num_rows > 0) {
                while($app = $volunteer_applications->fetch_assoc()) {
                    $status_class = 'status-' . strtolower($app['applicationStatus']);
                    
                    echo "<tr>";
                    echo "<td>".$app['applicationID']."</td>";
                    echo "<td>".$app['fname']." ".$app['sname']."</td>";
                    echo "<td>".$app['email']."</td>";
                    echo "<td>".$app['cellNumber']."</td>";
                    echo "<td><span class='$status_class'>".$app['applicationStatus']."</span></td>";
                    echo "<td>";
                    
                    if ($app['applicationStatus'] == 'Pending') {
                        // Form to approve or reject
                        echo "<form method='POST' style='display:inline;'>";
                        echo "<input type='hidden' name='applicationID' value='".$app['applicationID']."'>";
                        echo "<select name='new_status' required style='width: 120px; display: inline-block; margin-right: 5px;'>";
                        echo "<option value=''>Update Status</option>";
                        echo "<option value='Approved'>Approve</option>";
                        echo "<option value='Rejected'>Reject</option>";
                        echo "</select>";
                        echo "<input type='submit' name='update_application_status' value='Update' class='btn-secondary'>";
                        echo "</form>";
                    } else {
                        // Form to reset status back to pending
                        echo "<em>No action needed</em> ";
                        echo "<form method='POST' style='display:inline; margin-left: 10px;'>";
                        echo "<input type='hidden' name='applicationID' value='".$app['applicationID']."'>";
                        echo "<input type='submit' name='reset_application_status' value='Reset' class='btn-reset' onclick='return confirm(\"Are you sure you want to reset this application status to Pending?\")'>";
                        echo "</form>";
                    }
                    
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No volunteer applications found</td></tr>";
            }
            ?>
        </table>
    </div>
    
    <div class="admin-section">
        <h2>Assign Tasks to Volunteers</h2>
        <form method="POST">
            <table style="width: 70%;">
                <tr>
                    <td>Select Animal:</td>
                    <td>
                        <select name="animalID" required>
                            <option value="">Choose Animal</option>
                            <?php 
                            if ($all_animals->num_rows > 0) {
                                while($animal = $all_animals->fetch_assoc()) {
                                    echo "<option value='".$animal['animalID']."'>ID: ".$animal['animalID']." - ".$animal['name']." (".$animal['species']." - ".$animal['breed'].")</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Assign to Volunteer:</td>
                    <td>
                        <select name="userID" required>
                            <option value="">Choose Volunteer</option>
                         <?php 
                        if ($approved_volunteers->num_rows > 0) {
                            while($volunteer = $approved_volunteers->fetch_assoc()) {
                                echo "<option value='".$volunteer['userID']."'>ID: ".$volunteer['userID']." - ".$volunteer['fname']." ".$volunteer['sname']." (".$volunteer['email'].")</option>";
                             }
                        }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Task Description:</td>
                    <td>
                        <select name="taskName" required>
                            <option value="">Select Task Type</option>
                            <option value="Daily Feeding">Daily Feeding</option>
                            <option value="Dog Walking">Dog Walking</option>
                            <option value="Cage Cleaning">Cage Cleaning</option>
                            <option value="Cat Socialization">Cat Socialization</option>
                            <option value="Grooming">Grooming</option>
                            <option value="Transport to Vet">Transport to Vet</option>
                            <option value="Exercise and Play">Exercise and Play</option>
                            <option value="Administrative Support">Administrative Support</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Total Volunteers Needed:</td>
                    <td><input type="number" name="totalVolunteers" min="1" max="10" value="1" required></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="assign_task" value="Assign Task" class="btn-primary"></td>
                </tr>
            </table>
        </form>
    </div>
    
    <div class="admin-section">
        <h2>Current Volunteer Tasks</h2>
        <table>
            <tr>
                <th>Animal</th>
                <th>Volunteer</th>
                <th>Task</th>
                <th>Volunteers Needed</th>
                <th>Status</th>
                <th>Update Status</th>
            </tr>
            <?php
            if ($all_tasks->num_rows > 0) {
                while($task = $all_tasks->fetch_assoc()) {
                    $status_text = $task['completionStatus'] ? 'Completed' : 'In Progress';
                    $status_class = $task['completionStatus'] ? 'status-completed' : 'status-progress';
                    
                    echo "<tr>";
                    echo "<td>".$task['animal_name']." (".$task['species'].")</td>";
                    echo "<td>".$task['fname']." ".$task['sname']."</td>";
                    echo "<td>".$task['taskName']."</td>";
                    echo "<td>".$task['totalVolunteers']."</td>";
                    echo "<td><span class='$status_class'>$status_text</span></td>";
                    echo "<td>";
                    
                   if (!$task['completionStatus']) {
                       // If task is 'In Progress', show the update form
                        echo "<form method='POST' style='display:inline;'>";
                        echo "<input type='hidden' name='assignTaskID' value='".$task['assignTaskID']."'>";
                        echo "<select name='completion_status' style='width: 120px; display: inline-block; margin-right: 5px;'>";
                        echo "<option value='0'>In Progress</option>";
                        echo "<option value='1'>Mark Complete</option>";
                        echo "</select>";
                        echo "<input type='submit' name='update_task_status' value='Update' class='btn-secondary'>";
                        echo "</form>";
                   } else {
                       //  If task is 'Completed', show the Reset button
                        echo "<em>Task Completed</em>";
                        
                        echo "<form method='POST' margin-top: 5px;'>";
                        echo "<input type='hidden' name='assignTaskID' value='".$task['assignTaskID']."'>";
                        echo "<input type='submit' name='reset_task_status' value='Reset' class='btn-reset' onclick='return confirm(\"Are you sure you want to reset this task to In Progress?\")'>";
                        echo "</form>";
                    }



                }

            } else {
                echo "<tr><td colspan='6'>No tasks assigned yet</td></tr>";
            }
            ?>
        </table>
    </div>
    
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <p><strong>Need to do:</strong></p>
        <ul>
            <li>Review <?php echo $volunteer_stats['pending_applications']; ?> pending volunteer applications</li>
            <li>Check task completion status with volunteers</li>
            <li>Schedule volunteer orientation for newly approved volunteers</li>
            <li>Contact volunteers who haven't been active recently</li>
        </ul>
    </div>
    
    <hr>
    <p><em>SPCA Admin Dashboard | Volunteer Management System</em></p>
    
</body>
</html>

<?php
// Close the database connection at the end of the script
$conn->close();
?>