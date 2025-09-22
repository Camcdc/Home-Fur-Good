<?php
session_start();
ob_start(); // Start output buffering

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../navbar functionalities/userLoginC.php");
    exit();
}

include '../databaseConnection.php'; 

$userID = $_SESSION['userID'];
$message = '';
$messageType = '';

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $phoneNumber = trim($_POST['phoneNumber']);
    $address = trim($_POST['address']);
    
    if (empty($firstName) || empty($lastName) || empty($email)) {
        $message = "First name, last name, and email are required.";
        $messageType = 'error';
    } else {
        $stmt_check = $conn->prepare("SELECT userID FROM user WHERE Email = ? AND userID != ?");
        $stmt_check->bind_param("si", $email, $userID);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $message = "This email address is already in use by another account.";
            $messageType = 'error';
        } else {
            $stmt_update = $conn->prepare("UPDATE user SET Fname = ?, Sname = ?, Email = ?, CellNumber = ?, Address = ? WHERE userID = ?");
            $stmt_update->bind_param("sssssi", $firstName, $lastName, $email, $phoneNumber, $address, $userID);
            if ($stmt_update->execute()) {
                $message = "Profile updated successfully!";
                $messageType = 'success';
            } else {
                $message = "Error updating profile.";
                $messageType = 'error';
            }
            $stmt_update->close();
        }
        $stmt_check->close();
    }
}


// --- DATA FETCHING WITH ROBUST ERROR CHECKING ---

// Fetch User Information
$stmt_user = $conn->prepare("SELECT Fname, Sname, Email, CellNumber, Address FROM user WHERE userID = ?");
if ($stmt_user === false) { 
    die("<strong>Database Error:</strong> Could not prepare the user info query. <br><strong>MySQL Error:</strong> " . htmlspecialchars($conn->error));
}
$stmt_user->bind_param("i", $userID);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

if (!$user) {
    session_destroy();
    header("Location: ../navbar functionalities/userLoginC.php");
    exit();
}

// Fetch Volunteer Applications
$stmt_apps = $conn->prepare("SELECT applicationID, volunteerPreference, applicationStatus, applicationDate FROM application WHERE userID = ? AND applicationType = 'Volunteer' ORDER BY applicationDate DESC");
if ($stmt_apps === false) { 
    die("<strong>Database Error:</strong> Could not prepare the applications query. <br><strong>MySQL Error:</strong> " . htmlspecialchars($conn->error));
}
$stmt_apps->bind_param("i", $userID);
$stmt_apps->execute();
$applications = $stmt_apps->get_result();

// Fetch Assigned Tasks
$stmt_tasks = $conn->prepare("
    SELECT 
        at.assignTaskID, 
        at.taskName, 
        at.completionStatus 
    FROM 
        task_assignment AS ta
    JOIN 
        assignedtask AS at ON ta.assignTaskID = at.assignTaskID
    WHERE 
        ta.userID = ?
    ORDER BY 
        at.completionStatus ASC, at.assignTaskID DESC
");

if ($stmt_tasks === false) { 
    die("<strong>Database Error:</strong> Could not prepare the tasks query. <br><strong>MySQL Error:</strong> " . htmlspecialchars($conn->error));
}
$stmt_tasks->bind_param("i", $userID);
$stmt_tasks->execute();
$tasks_result = $stmt_tasks->get_result();
$assigned_tasks = [];
$tasks_completed = 0;
while ($row = $tasks_result->fetch_assoc()) {
    $assigned_tasks[] = $row;
    if ($row['completionStatus'] == 1) {
        $tasks_completed++;
    }
}
$stmt_tasks->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Volunteer Dashboard - SPCA</title>
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #AE9787;
            --accent-color: #2c3e50;
            --text-dark: #34495e;
            --bg-light: #f4f7f6;
            --white: #ffffff;
            --border-color: #dee2e6;
            --success-color: #28a745;
            --error-color: #dc3545;
            --pending-color: #ffc107;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            padding-top: 70px;
        }

        .dashboard-header {
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(44, 62, 80, 0.8)), url('slideshow1.jpg') no-repeat center center;
            background-size: cover;
            color: var(--white);
            padding: 50px 20px;
            text-align: center;
            border-bottom: 5px solid var(--primary-color);
        }
        .dashboard-header h1 { font-size: 2.8em; margin-bottom: 10px; }

        .dashboard-container { max-width: 1200px; margin: auto; padding: 30px 20px; }
        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .dashboard-card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.07);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .card-header {
            background: var(--accent-color);
            color: var(--white);
            padding: 15px 20px;
            font-weight: 600;
            font-size: 1.2em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header i { margin-right: 10px; }
        .card-content { padding: 25px; flex-grow: 1; }
        .full-width { grid-column: 1 / -1; }

        /* MODIFIED: Profile Card Styling */
        .profile-details-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 15px 20px;
        }
        .profile-picture {
            grid-row: 1 / 4;
            flex-shrink: 0;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--primary-color);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
            font-weight: 700;
        }
        .profile-details h3 { margin: 0; font-size: 1.5em; grid-column: 2; }
        .profile-info { grid-column: 2; display: flex; flex-direction: column; gap: 8px; }
        .profile-info p { margin: 0; color: #7f8c8d; }
        .profile-info p i { margin-right: 8px; width: 15px; text-align: center; color: var(--primary-color); }
        
        /* At a Glance Card */
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: center; }
        .stat-item .stat-number { font-size: 2.5em; font-weight: 700; color: var(--primary-color); }
        .stat-item .stat-label { font-size: 0.9em; color: #7f8c8d; }

        /* Application & Task Tables */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
        .data-table th { font-weight: 600; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover { background-color: #f8f9fa; }
        
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.8em; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved, .status-complete { background: #d4edda; color: #155724; }
        .status-rejected, .status-incomplete { background: #f8d7da; color: #721c24; }

        .task-row.completed td:not(:first-child) { text-decoration: line-through; color: #7f8c8d; }
        .task-checkbox { transform: scale(1.2); cursor: pointer; }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary-color);
            color: var(--white);
            border: 2px solid var(--primary-color);
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn:hover { background: var(--accent-color); border-color: var(--accent-color); transform: translateY(-2px); }
        .btn-secondary { background: transparent; color: var(--primary-color); }
        .btn-sm { padding: 5px 12px; font-size: 0.9em; } /* For smaller buttons like 'Edit' */

        .empty-state { text-align: center; padding: 40px; }
        .empty-state i { font-size: 3em; color: #ccc; margin-bottom: 15px; }

        /* MODAL STYLES */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; display: none; align-items: center; justify-content: center; }
        .modal-content { background: var(--white); border-radius: 12px; width: 90%; max-width: 600px; padding: 30px; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .modal-close { position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.5em; cursor: pointer; color: #ccc; }
        .modal-close:hover { color: var(--text-dark); }
        .modal-header h2 { color: var(--accent-color); margin-bottom: 20px; }
        
        /* Form styles for modal */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; box-sizing: border-box; }
        .form-actions { text-align: right; margin-top: 20px; }
        
        .message { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .message.success { background: #d4edda; color: #155724; }
        .message.error { background: #f8d7da; color: #721c24; }
        
        @media (max-width: 992px) { .dashboard-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include '../navbar functionalities/navbar.php'; ?>

    <div class="dashboard-header">
        <h1>My Volunteer Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($user['Fname']); ?>! Thank you for your commitment to helping animals.</p>
    </div>

    <div class="dashboard-container">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <span><i class="fas fa-user-circle"></i> My Profile</span>
                    <button class="btn btn-sm" onclick="openEditModal()">Edit Profile</button>
                </div>
                <div class="card-content">
                    <div class="profile-details-grid">
                        <div class="profile-picture"><?php echo strtoupper(substr($user['Fname'], 0, 1)); ?></div>
                        <div class="profile-details">
                            <h3><?php echo htmlspecialchars($user['Fname'] . ' ' . $user['Sname']); ?></h3>
                        </div>
                        <div class="profile-info">
                            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['Email']); ?></p>
                            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['CellNumber'] ?: 'Not provided'); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($user['Address'] ?: 'Not provided'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header"><i class="fas fa-chart-line"></i> Impact At-a-Glance</div>
                <div class="card-content stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $tasks_completed; ?></div>
                        <div class="stat-label">Tasks Completed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $applications->num_rows; ?></div>
                        <div class="stat-label">Active Applications</div>
                    </div>
                </div>
            </div>

            <div class="dashboard-card full-width">
                <div class="card-header"><i class="fas fa-file-alt"></i> My Applications</div>
                <div class="card-content">
                    <?php if ($applications->num_rows > 0): ?>
                        <table class="data-table">
                            <thead><tr><th>Preference</th><th>Status</th><th>Date</th></tr></thead>
                            <tbody>
                                <?php while ($app = $applications->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($app['volunteerPreference']); ?></td>
                                        <td><span class="status-badge status-<?php echo strtolower($app['applicationStatus']); ?>"><?php echo htmlspecialchars($app['applicationStatus']); ?></span></td>
                                        <td><?php echo date('d M Y', strtotime($app['applicationDate'])); ?></td>
                                        </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h3>No Applications Found</h3>
                            <p>Ready to make a difference? Submit your application today!</p>
                            <a href="../landing pages/volunteer_application.php" class="btn" style="margin-top: 15px;">Apply Now</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="dashboard-card full-width">
                <div class="card-header"><i class="fas fa-tasks"></i> My Assigned Tasks</div>
                <div class="card-content">
                     <?php if (!empty($assigned_tasks)): ?>
                        <table class="data-table">
                            <thead><tr><th>Status</th><th>Task</th><th>Related Animal</th></tr></thead>
                            <tbody>
                                <?php foreach ($assigned_tasks as $task): ?>
                                    <tr id="task-row-<?php echo $task['assignTaskID']; ?>" class="task-row <?php if($task['completionStatus']) echo 'completed'; ?>">
                                        <td>
                                            <input type="checkbox" class="task-checkbox" data-task-id="<?php echo $task['assignTaskID']; ?>" <?php if($task['completionStatus']) echo 'checked'; ?>>
                                        </td>
                                        <td><?php echo htmlspecialchars($task['taskName']); ?></td>
                                      <td>N/A</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-clipboard-check"></i>
                            <h3>No Tasks Assigned</h3>
                            <p>Check back soon! New tasks are assigned regularly.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div id="editProfileModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
            <div class="modal-header"><h2>Edit Profile Information</h2></div>
            <form method="POST" action="volunteer_dashboard.php">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['Fname']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['Sname']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($user['CellNumber']); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['Address']); ?>">
                </div>
                <div class="form-actions">
                    <button type="submit" name="update_profile" class="btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ADDED: JavaScript for the new Edit Profile modal
        const editModal = document.getElementById('editProfileModal');

        function openEditModal() {
            editModal.style.display = 'flex';
        }

        function closeEditModal() {
            editModal.style.display = 'none';
        }

        // REMOVED: JavaScript for the old application details modal

        window.onclick = function(event) {
            if (event.target == editModal) { // Close edit modal if overlay is clicked
                closeEditModal();
            }
        }
        
        // Interactive Task Checkboxes
        document.querySelectorAll('.task-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.dataset.taskId;
                const isChecked = this.checked;
                const row = document.getElementById(`task-row-${taskId}`);

                fetch('updateTaskStatus.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `task_id=${taskId}&status=${isChecked ? 1 : 0}`
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        row.classList.toggle('completed', isChecked);
                        // Potentially update the "Impact" card here as well
                    } else {
                        alert('Could not update task status. Please try again.');
                        this.checked = !isChecked; // Revert checkbox on failure
                    }
                });
            });
        });
        
        // Auto-hide success messages
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.message.success');
            if(successMessage) {
                setTimeout(() => {
                    successMessage.style.transition = 'opacity 0.5s ease';
                    successMessage.style.opacity = '0';
                    setTimeout(() => successMessage.remove(), 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>