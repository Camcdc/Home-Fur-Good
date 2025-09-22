<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include '../databaseConnection.php';

// Helper function for safe output
function display($value) {
    return $value !== '' && isset($value) ? nl2br(htmlspecialchars($value)) : 'Info not available';
}

// Handle outcome/criteria/retrieved update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['followUpID'])) {
    $followUpID = intval($_POST['followUpID']);
    $outcome = $_POST['outcome'] ?? '';
    $criteriaMeet = isset($_POST['criteriaMeet']) ? 1 : 0;
    $retrieved = isset($_POST['retrieved']) ? 1 : 0;
    $update_sql = "UPDATE postfollowup SET outcome = ?, criteriaMeet = ?, retrieved = ? WHERE followUpID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("siii", $outcome, $criteriaMeet, $retrieved, $followUpID);
    $update_stmt->execute();
    header("Location: post-followup.php");
    exit;
}

// Get all post-followup records
$sql = "SELECT pf.*, CONCAT(u.Fname, ' ', u.Sname) AS userName, an.name AS animalName, an.picture
   FROM postfollowup pf
   JOIN user u ON pf.userID = u.userID
   LEFT JOIN application app ON pf.adoptionID = app.adoptionID
   LEFT JOIN animal an ON app.animalID = an.animalID
   ORDER BY pf.followUpDate DESC";
$result = $conn->query($sql);

if (!$result) {
  die("Query error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Post-Adoption Follow-Up</title>
  <link rel="stylesheet" href="postFollowup.css">
</head>
<body>
<?php include 'sidebar_admin.php'; ?>
<div class="page-heading" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
    <h2 style="margin-right: 200px; text-align: center; flex: 1;">Post-Adoption Follow-Up</h2>
</div>
<div class="table-container">
<table>
  <tr>
    <th>FollowUp ID</th>
    <th>User Name</th>
    <th>Animal Name</th>
    <th>Application ID</th>
    <th>Follow-Up Date</th>
    <th>Outcome</th>
    <th>Criteria Met</th>
    <th>Retrieved</th>
    <th>Actions</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['followUpID']) ?></td>
      <td><?= htmlspecialchars($row['userName']) ?></td>
      <td><?= htmlspecialchars($row['animalName']) ?></td>
      <td><?= htmlspecialchars($row['adoptionID']) ?></td>
      <td><?= htmlspecialchars($row['followUpDate']) ?></td>
      <td><?= display($row['outcome']) ?></td>
      <td><?= $row['criteriaMeet'] ? '<span style="color:green;font-weight:bold;">Yes</span>' : '<span style="color:orange;font-weight:bold;">No</span>' ?></td>
      <td><?= $row['retrieved'] ? '<span style="color:red;font-weight:bold;">Yes</span>' : '<span style="color:gray;font-weight:bold;">No</span>' ?></td>
      <td>
        <form method="POST" style="display:inline;">
          <input type="hidden" name="followUpID" value="<?= $row['followUpID'] ?>">
          <input type="text" name="outcome" value="<?= htmlspecialchars($row['outcome']) ?>" placeholder="Outcome" style="width:120px;">
          <label><input type="checkbox" name="criteriaMeet" <?= $row['criteriaMeet'] ? 'checked' : '' ?>> Criteria Met</label>
          <label><input type="checkbox" name="retrieved" <?= $row['retrieved'] ? 'checked' : '' ?>> Retrieved</label>
          <button type="submit">Update</button>
        </form>
      </td>
    </tr>
  <?php endwhile; ?>
</table>
</div>
</body>
</html>
