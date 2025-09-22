<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
include '../databaseConnection.php';

// Handle approve/reject/revert actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // APPROVE
if (isset($_POST['approve']) && isset($_POST['adoptionID'])) {
    $adoptionID = $_POST['adoptionID'];

    // set adoption to approved
    $approve_sql = "UPDATE adoption SET status = 1 WHERE adoptionID = ?";
    $approve_stmt = $conn->prepare($approve_sql);
    $approve_stmt->bind_param("i", $adoptionID);
    $approve_stmt->execute();

    // fetch the animalID and userID
    $animal_sql = "SELECT animalID, userID FROM adoption WHERE adoptionID = ?";
    $animal_stmt = $conn->prepare($animal_sql);
    $animal_stmt->bind_param("i", $adoptionID);
    $animal_stmt->execute();
    $animal_result = $animal_stmt->get_result();

    if ($animal_row = $animal_result->fetch_assoc()) {
        $animalID = $animal_row['animalID'];
        $userID = $animal_row['userID'];

        // mark animal as adopted
        $update_animal_sql = "UPDATE animal SET status = 'Adopted' WHERE animalID = ?";
        $update_animal_stmt = $conn->prepare($update_animal_sql);
        $update_animal_stmt->bind_param("i", $animalID);
        $update_animal_stmt->execute();

        // Set default values for optional fields
        $criteriaMeet = 'Pending';  // Default value for criteriaMeet
        $retrieved = 'Pending';     // Default value for retrieved
        $followUpDate = new DateTime();  
        $followUpDate->modify('+1 month');  
        $followUpDate = $followUpDate->format('Y-m-d');  

        // Insert into postfollowup table
        $postfollowup_sql = "INSERT INTO postfollowup (userID, adoptionID, criteriaMeet, retrieved, followUpDate, outcome) 
                             VALUES (?, ?, ?, ?, ?, ?)";
        $postfollowup_stmt = $conn->prepare($postfollowup_sql);
        $postfollowup_stmt->bind_param("iissss", $userID, $adoptionID, $criteriaMeet, $retrieved, $followUpDate, $outcome);

        if (!$postfollowup_stmt->execute()) {
            echo "Error inserting into postfollowup table: " . $postfollowup_stmt->error;
        } else {
            echo "Successfully inserted into postfollowup table!";
        }
    }
}


  // REJECT
  if (isset($_POST['reject']) && isset($_POST['adoptionID'])) {
    $reject_sql = "UPDATE adoption SET isDeleted = 1 WHERE adoptionID = ?";
    $reject_stmt = $conn->prepare($reject_sql);
    $reject_stmt->bind_param("i", $_POST['adoptionID']);
    $reject_stmt->execute();
  }

  // REVERT (set back to pending)
  if (isset($_POST['revert']) && isset($_POST['adoptionID'])) {
    $adoptionID = $_POST['adoptionID'];

    // Revert adoption status to pending
    $revert_sql = "UPDATE adoption SET status = 0 WHERE adoptionID = ?";
    $revert_stmt = $conn->prepare($revert_sql);
    $revert_stmt->bind_param("i", $adoptionID);
    $revert_stmt->execute();

    // Fetch animalID and userID to identify the postfollowup entry
    $animal_sql = "SELECT animalID, userID FROM adoption WHERE adoptionID = ?";
    $animal_stmt = $conn->prepare($animal_sql);
    $animal_stmt->bind_param("i", $adoptionID);
    $animal_stmt->execute();
    $animal_result = $animal_stmt->get_result();

    if ($animal_row = $animal_result->fetch_assoc()) {
        $animalID = $animal_row['animalID'];
        $userID = $animal_row['userID'];

        // Also reset animal status back to 'Available'
        $update_animal_sql = "UPDATE animal SET status = 'Available' WHERE animalID = ?";
        $update_animal_stmt = $conn->prepare($update_animal_sql);
        $update_animal_stmt->bind_param("i", $animalID);
        $update_animal_stmt->execute();

        // Delete the corresponding postfollowup record
        $delete_postfollowup_sql = "DELETE FROM postfollowup WHERE adoptionID = ?";
        $delete_postfollowup_stmt = $conn->prepare($delete_postfollowup_sql);
        $delete_postfollowup_stmt->bind_param("i", $adoptionID);

        if (!$delete_postfollowup_stmt->execute()) {
            echo "Error deleting from postfollowup table: " . $delete_postfollowup_stmt->error;
        } else {
            echo "Successfully deleted from postfollowup table!";
        }
    }
}

  // Refresh to show updated status
  header("Location: manageAdoptions.php");
  exit;
}


$sql = $sql = "SELECT a.adoptionID, a.userID, a.animalID, 
               CONCAT(u.Fname, ' ', u.Sname) AS userName, 
               an.name AS animalName, 
               a.adoptionDate, a.status, a.isDeleted
        FROM adoption a
        JOIN user u ON a.userID = u.userID
        JOIN animal an ON a.animalID = an.animalID
        WHERE (a.isDeleted IS NULL OR a.isDeleted = 0)
          AND NOT EXISTS (
              SELECT 1 
              FROM postfollowup pf 
              WHERE pf.adoptionID = a.adoptionID 
                AND pf.retrieved = 'Retrieved'
          )";


$result = $conn->query($sql);

if (!$result) {
  die("Query error: " . $conn->error);
}

include 'sidebar_admin.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Adoptions</title>
  <link rel="stylesheet" href="sidebar_admin.css">
  <link rel="stylesheet" href="manageAdoptions.css">
</head>
<body>
<div class="page-heading">
    <h2>Manage Adoptions</h2>
    <div class="heading-underline"></div>
</div>
  <?php if ($result->num_rows > 0): ?>
    <div class="table-container">
    <table>
      <tr>
        <th>Adoption ID</th>
        <th>User Name</th>
        <th>Animal Name</th>
        <th>Adoption Date</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
          $userID = $row['userID'];
          $animalID = $row['animalID'];
          $adoptionID = $row['adoptionID'];

          // User adoption history (only count non-deleted, approved adoptions)
          $checkUser = $conn->prepare("SELECT COUNT(*) AS total FROM adoption WHERE userID = ? AND status = 1 AND (isDeleted IS NULL OR isDeleted = 0)");
          $checkUser->bind_param("i", $userID);
          $checkUser->execute();
          $userAdoptions = $checkUser->get_result()->fetch_assoc()['total'] ?? 0;

          // Animal adoption check (only count non-deleted, approved adoptions)
          $checkAnimal = $conn->prepare("SELECT COUNT(*) AS total FROM adoption WHERE animalID = ? AND status = 1 AND (isDeleted IS NULL OR isDeleted = 0)");
          $checkAnimal->bind_param("i", $animalID);
          $checkAnimal->execute();
          $animalAdopted = $checkAnimal->get_result()->fetch_assoc()['total'] ?? 0;

          // Only check if animal is not already adopted
          $eligible = ($animalAdopted == 0);

          // Fetch application details from 'application' table
          $app_sql = "SELECT * FROM application WHERE userID = ? AND animalID = ?";
          $app_stmt = $conn->prepare($app_sql);
          if (!$app_stmt) {
            echo "<td colspan='6' style='color:red;'>SQL error: " . htmlspecialchars($conn->error) . "</td></tr>";
            continue;
          }
          $app_stmt->bind_param("ii", $userID, $animalID);
          $app_stmt->execute();
          $app_result = $app_stmt->get_result();
          $application = $app_result->fetch_assoc();

          // Fetch user details
          $user_sql = "SELECT Fname, Sname, Email, CellNumber, Address, Role FROM user WHERE userID = ?";
          $user_stmt = $conn->prepare($user_sql);
          $user_stmt->bind_param("i", $userID);
          $user_stmt->execute();
          $user_result = $user_stmt->get_result();
          $user_details = $user_result->fetch_assoc();

          // Fetch animal image filename from DB
          $img_stmt = $conn->prepare("SELECT picture FROM animal WHERE animalID = ?");
          $img_stmt->bind_param("s", $row['animalID']);
          $img_stmt->execute();
          $img_result = $img_stmt->get_result();
          $img_row = $img_result->fetch_assoc();
          $animalImg = !empty($img_row['picture']) ? $img_row['picture'] : 'default.jpg';
        ?>
        <tr>
          <td><?= htmlspecialchars($row['adoptionID']) ?></td>
          <td><?= htmlspecialchars($row['userName']) ?></td>
          <td>
            <?= htmlspecialchars($row['animalName']) ?>
            <button class="view-icon" title="View Animal Image" onclick="showAnimalImage('<?= htmlspecialchars($animalImg) ?>')"><i class="fa-regular fa-eye"></i></button>
          </td>
          <td><?= htmlspecialchars($row['adoptionDate']) ?></td>
          <td>
            <?php
              if (isset($row['isDeleted']) && $row['isDeleted'] == 1) {
                echo '<span style="color:red;font-weight:bold;">Rejected</span>';
              } elseif (isset($row['status']) && $row['status'] == 1) {
                echo '<span style="color:green;font-weight:bold;">Approved</span>';
              } else {
                echo '<span style="color:orange;font-weight:bold;">Pending</span>';
              }
            ?>
          </td>
          <td>
            <?php if ($row['status'] == 1 && (!isset($row['isDeleted']) || $row['isDeleted'] == 0)): ?>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="adoptionID" value="<?= $row['adoptionID'] ?>">
                <button type="submit" name="revert">Revert to Pending</button>
              </form>
            <?php elseif ((!isset($row['isDeleted']) || $row['isDeleted'] == 0)): ?>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="adoptionID" value="<?= $row['adoptionID'] ?>">
                <?php if ($eligible): ?>
                  <button type="submit" name="approve">Approve</button>
                <?php endif; ?>
                <button type="submit" name="reject">Reject</button>
              </form>
            <?php else: ?>
              —
            <?php endif; ?>
            <button><a href="viewApplication.php?adoptionID=<?= urlencode($adoptionID) ?>" class="view-application">View Application</a></button>
            <?php if ($application): ?>
              <details style="margin-top:8px;">
                <summary>View Application</summary>
                <div style="background:#f9f9f9; padding:8px; border-radius:6px;">
                  <table style="width:100%; margin-bottom:12px; border-collapse:collapse;">
                    <caption style="caption-side:top; font-weight:bold; color:#2980b9;">User Info</caption>
                    <tr><th>UserID</th><td><?= htmlspecialchars($userID) ?></td></tr>
                    <tr><th>First Name</th><td><?= htmlspecialchars($user_details['Fname']) ?></td></tr>
                    <tr><th>Surname</th><td><?= htmlspecialchars($user_details['Sname']) ?></td></tr>
                    <tr><th>Email</th><td><?= htmlspecialchars($user_details['Email']) ?></td></tr>
                    <tr><th>Date of Birth</th><td><?= htmlspecialchars($user_details['DateOfBirth'] ?? '') ?></td></tr>
                    <tr><th>Cell Number</th><td><?= htmlspecialchars($user_details['CellNumber']) ?></td></tr>
                    <tr><th>Address</th><td><?= htmlspecialchars($user_details['Address']) ?></td></tr>
                    <tr><th>Role</th><td><?= htmlspecialchars($user_details['Role']) ?></td></tr>
                  </table>
                  <table style="width:100%; border-collapse:collapse; table-layout:fixed; word-break:break-all; overflow-wrap:anywhere;">
                    <caption style="caption-side:top; font-weight:bold; color:#2980b9;">Application Answers</caption>
                    <tr><th>Status</th><td><?= htmlspecialchars($application['applicationStatus']) ?></td></tr>
                    <tr><th>Type</th><td><?= htmlspecialchars($application['applicationType']) ?></td></tr>
                    <tr><th>Preference</th><td><?= htmlspecialchars($application['volunteerPreference']) ?></td></tr>
                    <tr><th>Reason</th><td><div style="white-space:pre-line; word-break:break-all; overflow-wrap:anywhere; max-width:350px;"><?= htmlspecialchars($application['volunteerReason']) ?></div></td></tr>
                    <tr><th>Availability</th><td><div style="white-space:pre-line; word-break:break-all; overflow-wrap:anywhere; max-width:350px;"><?= htmlspecialchars($application['availability']) ?></div></td></tr>
                    <tr><th>Experience</th><td><div style="white-space:pre-line; word-break:break-all; overflow-wrap:anywhere; max-width:350px;"><?= htmlspecialchars($application['experience']) ?></div></td></tr>
                    <tr><th>Created</th><td><?= htmlspecialchars($application['createDate']) ?></td></tr>
                    <tr><th>Application Date</th><td><?= htmlspecialchars($application['applicationDate']) ?></td></tr>
                  </table>
                </div>
              </details>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
    </div>
  <?php else: ?>
    <p>No pending adoptions.</p>
  <?php endif; ?>

  <!-- Modal for animal image -->
  <div id="animalModal" class="animal-modal">
    <div class="animal-modal-content">
      <img id="animalModalImg" src="" alt="Animal Image">
      <button class="animal-modal-close" onclick="closeAnimalModal()">Close</button>
    </div>
  </div>

  <script>
  function showAnimalImage(imgFile) {
    var imgUrl = '../pictures/animals/' + imgFile;
    document.getElementById('animalModalImg').src = imgUrl;
    document.getElementById('animalModal').classList.add('active');
  }
  function closeAnimalModal() {
    document.getElementById('animalModal').classList.remove('active');
  }
  </script>
</body>
</html>


