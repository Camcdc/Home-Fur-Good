<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseConnection.php';

// Handle approve/reject/revert actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationID = $_POST['applicationID'];
    $action = '';

    if (isset($_POST['approve'])) $action = 'Approved';
    if (isset($_POST['reject']))  $action = 'Rejected';
    if (isset($_POST['revert']))  $action = 'Pending';

    if (!empty($action) && !empty($applicationID)) {
        $stmt = $conn->prepare("UPDATE application SET applicationStatus = ? WHERE applicationID = ?");
        $stmt->bind_param("si", $action, $applicationID);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manageVolunteers.php");
    exit;
}

// --- Data Fetching with Search ---
$search_term = $_GET['search'] ?? '';
$where_clause = '';
if (!empty($search_term)) {
    $search_param = "%{$search_term}%";
    $where_clause = "AND (CONCAT(u.fname, ' ', u.sname) LIKE ? OR a.applicationStatus LIKE ? OR a.applicationID LIKE ?)";
}

$sql = "SELECT a.applicationID, a.applicationStatus, u.fname, u.sname, a.applicationDate
        FROM application a
        JOIN user u ON a.userID = u.userID
        WHERE a.applicationType = 'Volunteer' $where_clause
        ORDER BY a.applicationDate DESC";

$stmt = $conn->prepare($sql);
if (!empty($search_term)) {
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
}
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Query error: " . $conn->error);
}

include 'sidebar_admin.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Volunteer Applications</title>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="manageAdoptions.css"> 
    <style>
        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }
        .page-heading h1 {
            margin: 0;
            text-align: center;
            flex-grow: 1; /* Allows the h1 to take up space and center properly */
        }
        .filter-container {
            display: flex;
            flex-direction: column; /* Stacks children vertically */
            gap: 15px; /* Adds space between stacked items */
        }
        .filter-item {
            display: flex;
            flex-direction: column; /* Stacks label and input vertically */
        }
        .filter-item label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .filter-item input,
        .filter-item select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            width: 100%; /* Make inputs take full width of their container */
        }
    </style>
</head>
<body>
    <div class="page-heading">
        <a href='volunteer_admin.php' class='updateBtn'>← Back to Dashboard</a>
        <h1>Manage Volunteer Applications</h1>
    </div>

    <div class="table-container" style="padding: 20px; margin-bottom: 20px;">
        <div class="filter-container">
            <div class="filter-item">
                <label for="searchFilter">Search:</label>
                <input type="text" id="searchFilter" onkeyup="filterTable()" placeholder="Filter by name, date, status...">
            </div>
            <div style="display: flex; gap: 20px;">
                <div class="filter-item" style="flex: 1;">
                    <label for="sortColumn">Sort By:</label>
                    <select id="sortColumn" onchange="sortTable()">
                        <option value="2">Application Date</option>
                        <option value="1">Applicant Name</option>
                        <option value="3">Status</option>
                    </select>
                </div>
                <div class="filter-item" style="flex: 1;">
                    <label for="sortOrder">Order:</label>
                    <select id="sortOrder" onchange="sortTable()">
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
    <div class="table-container">
        <table id="applicationsTable">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Applicant Name</th>
                    <th>Application Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['applicationID']); ?></td>
                    <td><?php echo htmlspecialchars($row['fname'] . ' ' . $row['sname']); ?></td>
                    <td><?php echo date("Y-m-d", strtotime($row['applicationDate'])); ?></td>
                    <td>
                        <?php
                            $status = htmlspecialchars($row['applicationStatus']);
                            $status_class = strtolower($status);
                            echo "<span class='status-badge status-{$status_class}'>{$status}</span>";
                        ?>
                    </td>
                    <td>
                        <?php if ($row['applicationStatus'] == 'Pending'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="applicationID" value="<?php echo $row['applicationID']; ?>">
                                <button type="submit" name="approve">Approve</button>
                                <button type="submit" name="reject">Reject</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="applicationID" value="<?php echo $row['applicationID']; ?>">
                                <button type="submit" name="revert" style="background-color: #6c757d; color: white;">Revert to Pending</button>
                            </form>
                        <?php endif; ?>
                        <a href="view_application_details.php?id=<?php echo $row['applicationID']; ?>" class="view-details-link">View Details</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p style="padding-left: 300px;">No volunteer applications found.</p>
    <?php endif; ?>
    
    <style>
        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: white; }
        .status-pending { background: #ffc107; color: #333; }
        .status-approved { background: #28a745; }
        .status-rejected { background: #dc3545; }
        .view-details-link { display: inline-block; margin-left: 10px; color: #007bff; text-decoration: none; font-weight: bold; }
        .view-details-link:hover { text-decoration: underline; }
    </style>

    <script>
        function filterTable() {
            const filter = document.getElementById('searchFilter').value.toUpperCase();
            const table = document.getElementById('applicationsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) { 
                let display = 'none';
                const tds = tr[i].getElementsByTagName('td');
                for (let j = 0; j < tds.length; j++) {
                    if (tds[j] && tds[j].textContent.toUpperCase().indexOf(filter) > -1) {
                        display = '';
                        break;
                    }
                }
                tr[i].style.display = display;
            }
        }

        function sortTable() {
            const table = document.getElementById('applicationsTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const column = parseInt(document.getElementById('sortColumn').value, 10);
            const order = document.getElementById('sortOrder').value;
            const isAsc = order === 'asc';

            const sortedRows = rows.sort((a, b) => {
                const aText = a.querySelector(`td:nth-child(${column + 1})`).textContent.trim();
                const bText = b.querySelector(`td:nth-child(${column + 1})`).textContent.trim();
                
                if (column === 2) { 
                    const dateA = new Date(aText);
                    const dateB = new Date(bText);
                    return (dateA - dateB) * (isAsc ? 1 : -1);
                }
                
                return aText.localeCompare(bText, undefined, {numeric: true}) * (isAsc ? 1 : -1);
            });

            tbody.innerHTML = '';
            sortedRows.forEach(row => tbody.appendChild(row));
        }

        document.addEventListener('DOMContentLoaded', function() {
            sortTable();
        });
    </script>
</body>
</html>