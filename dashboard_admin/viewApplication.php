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

// Helper function to safely display values
function display($value) {
    return $value !== '' && isset($value) ? nl2br(htmlspecialchars($value)) : 'Info not available';
}

// Get adoption ID
$adoptionID = isset($_GET['adoptionID']) ? intval($_GET['adoptionID']) : 0;
if (!$adoptionID) die("Missing adoptionID.");

// Fetch adoption record
$adopt_stmt = $conn->prepare("SELECT * FROM adoption WHERE adoptionID = ?");
$adopt_stmt->bind_param("i", $adoptionID);
$adopt_stmt->execute();
$adoption = $adopt_stmt->get_result()->fetch_assoc();
if (!$adoption) die("No adoption found for this ID.");

// Fetch user details
$userID = $adoption['userID'];
$user_stmt = $conn->prepare("SELECT userID, Fname, Sname, Email, DateOfBirth, CellNumber, Address, Role FROM user WHERE userID = ?");
$user_stmt->bind_param("i", $userID);
$user_stmt->execute();
$user_details = $user_stmt->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Adoption Application Details</title>
  <link rel="stylesheet" href="viewApplication.css">
  <link rel="stylesheet" href="sidebar_admin.css">
</head>
<body>
  <?php include '../dashboard_admin/sidebar_admin.php'; ?>

    <div class="page-heading" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
    <a href='manageAdoptions.php' class='back-btn'>← Back to Adoption Management</a>
    <h2 style="margin-right: 200px; text-align: center; flex: 1;">View Application</h2>
    </div>

  <div class="container">
    <div class="app-block">

      <!-- User Info Table -->
      <div class="table-container">
        <table>
          <caption>User Info</caption>
          <tr><th>UserID</th><td><?= display($user_details['userID']) ?></td></tr>
          <tr><th>First Name</th><td><?= display($user_details['Fname']) ?></td></tr>
          <tr><th>Surname</th><td><?= display($user_details['Sname']) ?></td></tr>
          <tr><th>Email</th><td><?= display($user_details['Email']) ?></td></tr>
          <tr><th>Date of Birth</th><td><?= display($user_details['DateOfBirth']) ?></td></tr>
          <tr><th>Cell Number</th><td><?= display($user_details['CellNumber']) ?></td></tr>
          <tr><th>Address</th><td><?= display($user_details['Address']) ?></td></tr>
          <tr><th>Role</th><td><?= display($user_details['Role']) ?></td></tr>
        </table>
      </div>

      <!-- Application Answers Table -->
      <div class="table-container">
        <table>
          <caption>Application Answers</caption>
          <?php
          // Define adoption fields and labels
          $fields = [
            "housing" => "Do you own or rent your home?",
            "landlord_permission" => "If renting, do you have landlord’s permission for pets?",
            "residence_type" => "Type of residence (house, apartment, farm, etc.)",
            "yard_size" => "Size of yard / outdoor space (if relevant)",
            "household_members" => "Who lives in the household? (adults, children, ages)",
            "household_allergies" => "Are there any allergies in the home?",
            "past_pets" => "Have you owned pets before? What kind?",
            "current_pets" => "Do you currently have other pets? If yes, species/ages/spayed-neutered?",
            "pet_training_experience" => "Experience with training, handling, or caring for pets",
            "work_hours" => "Typical work hours / time pet will be alone daily",
            "travel_plan" => "Travel frequency (who will look after pet when away)",
            "activity_level" => "Level of activity (important for matching active animals like dogs)",
            "reason" => "Why do you want to adopt this animal?",
            "financial_ready" => "Are you financially able to cover vet visits, food, grooming, emergencies?",
            "commitment_agreement" => "Do you agree to spay/neuter, vaccinations, and regular vet care?",
            "backup_plan" => "What will you do if you can no longer keep the pet?",
            "emergency_contact" => "Emergency contact / reference",
            "vet_reference" => "Vet reference (if you had pets before)",
            "adoptionDate" => "Application Date"
          ];

          foreach ($fields as $field => $label) {
              $value = isset($adoption[$field]) ? $adoption[$field] : '';
              if ($field === "household_members" || $field === "past_pets" || $field === "current_pets" || $field === "pet_training_experience" || $field === "reason" || $field === "backup_plan") {
                  echo "<tr><th>$label</th><td><div class='answer-container'>" . display($value) . "</div></td></tr>";
              } else {
                  echo "<tr><th>$label</th><td>" . display($value) . "</td></tr>";
              }
          }
          ?>
        </table>
      </div>

    </div>
  </div>
</body>
</html>
