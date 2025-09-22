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

//--- DATA FETCHING FOR DISPLAY ---//

// Key statistics (no changes here)
$volunteer_stats = $conn->query("SELECT 
    COUNT(*) as total_applications,
    SUM(CASE WHEN applicationStatus = 'Pending' THEN 1 ELSE 0 END) as pending_applications,
    SUM(CASE WHEN applicationStatus = 'Approved' THEN 1 ELSE 0 END) as approved_applications
    FROM application WHERE applicationType = 'Volunteer' AND isDeleted = 0")->fetch_assoc();

$task_stats = $conn->query("SELECT 
    COUNT(*) as total_tasks,
    SUM(CASE WHEN completionStatus = 0 THEN 1 ELSE 0 END) as active_tasks
    FROM assignedtask")->fetch_assoc();

// MODIFIED: Logic for applications query
$search_term = $_GET['search'] ?? '';
$search_params = [];
$where_clause = "WHERE a.applicationType = 'Volunteer' AND a.isDeleted = 0";

if (!empty($search_term)) {
    // If searching, look through all statuses for matching names
    $where_clause .= " AND (u.fname LIKE ? OR u.sname LIKE ?)";
    $like_term = "%" . $search_term . "%";
    $search_params = [$like_term, $like_term];
} else {
    // If not searching, default to showing only pending applications
    $where_clause .= " AND a.applicationStatus='Pending'";
}

$applications_query = "SELECT a.applicationID, u.fname, u.sname, a.applicationDate, a.applicationStatus 
    FROM application a 
    LEFT JOIN user u ON a.userID = u.userID 
    $where_clause
    ORDER BY a.applicationDate DESC LIMIT 5";

$stmt = $conn->prepare($applications_query);
if (!empty($search_term)) {
    $stmt->bind_param("ss", ...$search_params);
}
$stmt->execute();
$recent_applications = $stmt->get_result();


// Active tasks list (no changes here)
$active_tasks = $conn->query("SELECT at.taskName, u.fname, u.sname, a.name as animal_name
    FROM assignedtask at
    LEFT JOIN user u ON at.userID = u.userID
    LEFT JOIN animal a ON at.animalID = a.animalID
    WHERE at.completionStatus = 0
    ORDER BY at.assignTaskID DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard - SPCA Admin</title>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="dashboard_donation.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <style>
        .item-details {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .quick-actions button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
        }
        .quick-actions button .material-symbols-outlined {
            vertical-align: middle;
        }
        .quick-actions .btn-view .material-symbols-outlined { color: #3498db; }
        .quick-actions .btn-approve .material-symbols-outlined { color: #2ecc71; }
        .quick-actions .btn-reject .material-symbols-outlined { color: #e74c3c; }
        .quick-actions button:hover, .quick-actions a:hover { background-color: #f0f0f0; border-radius: 50%; }
        .search-bar { margin-bottom: 15px; }
        .search-bar form { display: flex; }
        .search-bar input[type="search"] { flex-grow: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px 0 0 5px; outline: none; }
        .search-bar button { padding: 8px 12px; border: none; background: #333; color: white; border-radius: 0 5px 5px 0; cursor: pointer; }
        .search-bar button .material-symbols-outlined { vertical-align: middle; font-size: 20px; }
        
        /* ADDED: Styling for status badges in the list */
        .status-badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            margin-left: 10px;
            vertical-align: middle;
        }
        .status-badge.status-pending { background: #f1c40f; color: #fff; }
        .status-badge.status-approved { background: #2ecc71; color: #fff; }
        .status-badge.status-rejected { background: #e74c3c; color: #fff; }
    </style>
</head>
<body>
    <?php include 'sidebar_admin.php'; ?>
    <div class="container">
        <div class="dashboard-heading">
            <h3>Volunteer Management Dashboard</h3>
        </div>
        <div class="dashboard-container">
            <div class="stat-cards">
                <div class="card">
                    <h3>Total Applications</h3>
                    <p id="total-apps"><?php echo $volunteer_stats['total_applications'] ?? 0; ?></p>
                </div>
                <div class="card">
                    <h3>Pending Applications</h3>
                    <p id="pending-apps"><?php echo $volunteer_stats['pending_applications'] ?? 0; ?></p>
                </div>
                <div class="card">
                    <h3>Approved Volunteers</h3>
                    <p id="approved-apps"><?php echo $volunteer_stats['approved_applications'] ?? 0; ?></p>
                </div>
                <div class="card">
                    <h3>Active Tasks</h3>
                    <p><?php echo $task_stats['active_tasks'] ?? 0; ?></p>
                </div>
            </div>
            
            <div class="dashboard-section">
                <div class="section">
                    <div class="section-header">
                        <h4><?php echo !empty($search_term) ? 'Application Search Results' : 'Recent Pending Applications'; ?></h4>
                        <div class="buttons">
                            <a href="manageVolunteers.php" class="add-btn">
                                <span class="material-symbols-outlined">person_search</span>
                                <span class="btn-text">Manage All</span>
                            </a>
                        </div>
                    </div>
                    <div class="section-list">
                        <div class="search-bar">
                            <form action="" method="GET">
                                <input type="search" name="search" placeholder="Search all applicants by name..." value="<?php echo htmlspecialchars($search_term); ?>">
                                <button type="submit"><span class="material-symbols-outlined">search</span></button>
                            </form>
                        </div>
                        <div id="application-list">
                            <?php if ($recent_applications && $recent_applications->num_rows > 0): ?>
                                <?php while ($app = $recent_applications->fetch_assoc()): ?>
                                <div class="list-item" id="app-<?php echo $app['applicationID']; ?>">
                                    <div class="item-details">
                                        <span class="item-title">
                                            <?php echo htmlspecialchars($app['fname'] . ' ' . $app['sname']); ?>
                                            <?php if (!empty($search_term)): ?>
                                                <span class="status-badge status-<?php echo strtolower($app['applicationStatus']); ?>"><?php echo htmlspecialchars($app['applicationStatus']); ?></span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="item-date">Applied on: <?php echo date("d M Y", strtotime($app['applicationDate'])); ?></span>
                                    </div>
                                    <div class="quick-actions">
                                        <a href="manageVolunteers.php?view_id=<?php echo $app['applicationID']; ?>" title="View Details" class="btn-view"><button><span class="material-symbols-outlined">visibility</span></button></a>
                                        <?php if ($app['applicationStatus'] === 'Pending'): ?>
                                            <button class="btn-approve" title="Approve" data-id="<?php echo $app['applicationID']; ?>"><span class="material-symbols-outlined">check_circle</span></button>
                                            <button class="btn-reject" title="Reject" data-id="<?php echo $app['applicationID']; ?>"><span class="material-symbols-outlined">cancel</span></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="list-item"><span class="item-subtitle">No applications found.</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <h4>Active Tasks</h4>
                        <div class="buttons">
                            <a href="manageTask.php" class="add-btn">
                                <span class="material-symbols-outlined">task</span>
                                <span class="btn-text">Manage All</span>
                            </a>
                        </div>
                    </div>
                    <div class="section-list">
                        <?php if ($active_tasks && $active_tasks->num_rows > 0): ?>
                            <?php while ($task = $active_tasks->fetch_assoc()): ?>
                            <div class="list-item">
                                <span class="item-title"><?php echo htmlspecialchars($task['taskName']); ?></span>
                                <span class="item-subtitle">
                                    Assigned to: <?php echo htmlspecialchars($task['fname'] . ' ' . $task['sname']); ?>
                                </span>
                                <?php if($task['animal_name']): ?>
                                <span class="item-date">Animal: <?php echo htmlspecialchars($task['animal_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="list-item"><span class="item-subtitle">No active tasks.</span></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const appList = document.getElementById('application-list');
    
    if (appList) {
        appList.addEventListener('click', function(e) {
            const button = e.target.closest('button[data-id]');
            if (!button) return;

            const appId = button.dataset.id;
            let action = '';

            if (button.classList.contains('btn-approve')) {
                action = 'Approved';
            } else if (button.classList.contains('btn-reject')) {
                action = 'Rejected';
            }

            if (action && confirm(`Are you sure you want to mark this application as ${action}?`)) {
                updateApplicationStatus(appId, action, button);
            }
        });
    }

    function updateApplicationStatus(id, status, button) {
        const row = document.getElementById(`app-${id}`);
        button.disabled = true;
        
        fetch('update_application_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                row.style.transition = 'opacity 0.5s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 500);

                const pendingCount = document.getElementById('pending-apps');
                pendingCount.textContent = parseInt(pendingCount.textContent) - 1;

                if(status === 'Approved') {
                    const approvedCount = document.getElementById('approved-apps');
                    approvedCount.textContent = parseInt(approvedCount.textContent) + 1;
                }
            } else {
                alert('Error: ' + (data.error || 'Could not update status.'));
                button.disabled = false;
            }
        })
        .catch(error => {
            alert('An unexpected error occurred. Please try again.');
            console.error('Fetch Error:', error);
            button.disabled = false;
        });
    }
});
</script>
</body>
</html>