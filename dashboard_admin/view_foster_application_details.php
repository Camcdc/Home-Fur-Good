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
    // ... (POST handling code remains the same) ...
    if (isset($_POST['assign_animal_to_foster'])) {
        $animalID = $_POST['animalID'];
        $userID = $_POST['userID'];
        
        $conn->begin_transaction();
        try {
            $stmt1 = $conn->prepare("INSERT INTO adoption (userID, animalID, adoptionDate, status) VALUES (?, ?, NOW(), 'Fostered')");
            $stmt1->bind_param("ii", $userID, $animalID);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $conn->prepare("UPDATE animal SET status = 'Fostered' WHERE animalID = ?");
            $stmt2->bind_param("i", $animalID);
            $stmt2->execute();
            $stmt2->close();
            
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
        }
    }
    
    if (isset($_POST['return_animal'])) {
        $animalID = $_POST['animalID'];
        $userID = $_POST['userID'];

        $conn->begin_transaction();
        try {
            $stmt1 = $conn->prepare("UPDATE animal SET status = 'Available' WHERE animalID = ?");
            $stmt1->bind_param("i", $animalID);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $conn->prepare("UPDATE adoption SET status = 'Returned' WHERE animalID = ? AND userID = ? AND status = 'Fostered'");
            $stmt2->bind_param("ii", $animalID, $userID);
            $stmt2->execute();
            $stmt2->close();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
        }
    }
    
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?id=$applicationID#foster-history");
    exit();
}

//--- DATA FETCHING ---//
// Fetch main application and user details
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

// NEW: Fetch details for the specific animal requested in the application, if any
$requested_animal_details = null;
if (isset($application_details['animalID']) && $application_details['animalID'] > 0) {
    $animal_stmt = $conn->prepare("SELECT * FROM animal WHERE animalID = ?");
    $animal_stmt->bind_param("i", $application_details['animalID']);
    $animal_stmt->execute();
    $animal_result = $animal_stmt->get_result();
    if ($animal_result->num_rows > 0) {
        $requested_animal_details = $animal_result->fetch_assoc();
    }
    $animal_stmt->close();
}

// Fetch animals available for fostering (for the assignment dropdown)
$all_animals = $conn->query("SELECT animalID, name, species FROM animal WHERE status = 'Available' AND isDeleted = 0");

// Fetch foster history
$foster_history_query = $conn->prepare("SELECT a.animalID, a.name as animal_name, a.species, a.breed, a.picture, ad.status, ad.adoptionDate 
                                        FROM adoption ad 
                                        JOIN animal a ON ad.animalID = a.animalID 
                                        WHERE ad.userID = ? AND ad.status IN ('Fostered', 'Returned', 'Adopted')
                                        ORDER BY ad.adoptionDate DESC");
if ($foster_history_query === false) die("SQL Prepare Error: " . htmlspecialchars($conn->error));
$foster_history_query->bind_param("i", $current_userID);
$foster_history_query->execute();
$foster_history_result = $foster_history_query->get_result();

// Separate active placements from past history for display
$active_placements = [];
$past_placements = [];
while ($row = $foster_history_result->fetch_assoc()) {
    if ($row['status'] === 'Fostered') {
        $active_placements[] = $row;
    } else {
        $past_placements[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Foster Application Details</title>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="viewApplication.css">
    <style>
        .foster-card-container { display: flex; flex-wrap: wrap; gap: 20px; }
        .foster-card { display: flex; gap: 20px; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #ddd; width: 100%; max-width: 500px; }
        .foster-card img { width: 120px; height: 120px; object-fit: cover; border-radius: 8px; }
        .foster-card-info { flex: 1; }
        .foster-card-info h4 { margin: 0 0 5px 0; font-size: 1.2em; color: #333; }
        .foster-card-info p { margin: 4px 0; color: #555; }
        .foster-card-info strong { color: #000; }
    </style>
</head>
<body>
    <?php include 'sidebar_admin.php'; ?>

    <div class="page-heading">
        <a href='manageFosters.php' class='back-btn'>← Back to All Applications</a>
        <h2>Foster Profile</h2>
    </div>

    <div class="container">
        <?php if ($requested_animal_details): ?>
        <div id="requested-animal" class="app-block">
            <h3>Requested Foster Animal</h3>
            <div class="foster-card-container">
                <div class="foster-card">
                    <img src="<?php echo display($requested_animal_details['picture']); ?>" alt="<?php echo display($requested_animal_details['name']); ?>">
                    <div class="foster-card-info">
                        <h4><?php echo display($requested_animal_details['name']); ?></h4>
                        <p><?php echo display($requested_animal_details['species']); ?> - <?php echo display($requested_animal_details['breed']); ?></p>
                        <p><strong>Age:</strong> <?php echo display($requested_animal_details['age']); ?></p>
                        <p><strong>Status:</strong> <?php echo display($requested_animal_details['status']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
                    <caption>Foster Application Details</caption>
                    <tr><th>Application Date</th><td><?php echo date("F j, Y", strtotime($application_details['applicationDate'])); ?></td></tr>
                    <tr><th>Current Status</th><td><?php echo display($application_details['applicationStatus']); ?></td></tr>
                    <tr><th>Fostering Preference</th><td><?php echo display($application_details['fosterPreference']); ?></td></tr>
                    <tr><th>Preferred Duration</th><td><?php echo display($application_details['fosterDurationDays']); ?> days</td></tr>
                    <tr><th>Household Info</th><td><div class="answer-container"><?php echo display($application_details['householdInfo']); ?></div></td></tr>
                    <tr><th>Home Type</th><td><?php echo display($application_details['homeType']); ?></td></tr>
                    <tr><th>Has Fenced Yard?</th><td><?php echo display($application_details['hasYard']); ?></td></tr>
                    <tr><th>Current Pets</th><td><div class="answer-container"><?php echo display($application_details['currentPets']); ?></div></td></tr>
                    <tr><th>Experience</th><td><div class="answer-container"><?php echo display($application_details['experience']); ?></div></td></tr>
                </table>
            </div>
        </div>

        <?php if ($application_details['applicationStatus'] == 'Approved'): ?>
        <div class="app-block">
            <div class="table-container">
                <form method="POST">
                    <input type="hidden" name="userID" value="<?php echo $current_userID; ?>">
                    <table>
                        <caption>Assign Animal to Foster</caption>
                        <tr>
                            <th>Select Available Animal</th>
                            <td>
                                <select name="animalID" required>
                                    <option value="">-- Select an animal --</option>
                                    <?php while($animal = $all_animals->fetch_assoc()): ?>
                                    <option value="<?php echo $animal['animalID']; ?>"><?php echo htmlspecialchars($animal['name'] . " (" . $animal['species'] . ")"); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: right;">
                                <button type="submit" name="assign_animal_to_foster" class="back-btn" style="background-color: #28a745; margin-bottom: 0;">Assign to Foster</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div id="current-placements" class="app-block">
            <h3>Current Foster Placement(s)</h3>
            <div class="foster-card-container">
                <?php if (!empty($active_placements)): ?>
                    <?php foreach($active_placements as $foster): ?>
                        <div class="foster-card">
                            <img src="<?php echo display($foster['picture']); ?>" alt="<?php echo display($foster['animal_name']); ?>">
                            <div class="foster-card-info">
                                <h4><?php echo display($foster['animal_name']); ?></h4>
                                <p><?php echo display($foster['species']); ?> - <?php echo display($foster['breed']); ?></p>
                                <p><strong>Placed On:</strong> <?php echo date("F j, Y", strtotime($foster['adoptionDate'])); ?></p>
                                <p><strong>Return By:</strong>
                                    <?php
                                    $placementDate = new DateTime($foster['adoptionDate']);
                                    $duration = $application_details['fosterDurationDays'];
                                    $placementDate->add(new DateInterval("P{$duration}D"));
                                    echo $placementDate->format('F j, Y');
                                    ?>
                                </p>
                                <form method="POST" style="margin-top: 10px;">
                                    <input type="hidden" name="animalID" value="<?php echo $foster['animalID']; ?>">
                                    <input type="hidden" name="userID" value="<?php echo $current_userID; ?>_userID; ?>">
                                    <button type="submit" name="return_animal" class="back-btn" style="background-color: #6c757d; margin:0; padding: 5px 10px;">Return to Shelter</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>This person has no animals currently in their care.</p>
                <?php endif; ?>
            </div>
        </div>

        <div id="foster-history" class="app-block">
            <h3>Foster History Log</h3>
            <div class="table-container">
                <table>
                    <caption>A log of all past foster and adoption records.</caption>
                    <thead><tr><th>Animal Name</th><th>Placement Date</th><th>Final Status</th></tr></thead>
                    <tbody>
                        <?php if (!empty($past_placements)): ?>
                            <?php foreach($past_placements as $foster): ?>
                            <tr>
                                <td><?php echo display($foster['animal_name']); ?></td>
                                <td><?php echo date("F j, Y", strtotime($foster['adoptionDate'])); ?></td>
                                <td><?php echo display($foster['status']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No past foster history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
