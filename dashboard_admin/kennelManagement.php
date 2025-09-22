<?php
 session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kennel Management</title>
    <link rel="stylesheet" href="../global.css">  
    <link rel="stylesheet" href="kennelManagement.css">
    <link rel="stylesheet" href="sidebar_admin.css">
</head>
<body>

<?php
include 'sidebar_admin.php';
require_once '../databaseConnection.php';

// Order kennels first by type, then by ID
$sql = "SELECT * FROM kennel ORDER BY kennelType, kennelID ASC";
$result = $conn->query($sql);
?>

<div class="container">
  <h1>Kennel Management</h1>

  <?php 
  $currentType = null;

  while($row = $result->fetch_assoc()) { 
      $kennelID = $row['kennelID'];
      $occupation = $row['occupation'];
      $capacity = $row['capacity'];
      $kennelType = $row['kennelType'];

      // If the kennelType changes, print a header + new section
      if ($currentType !== $kennelType) {
          if ($currentType !== null) {
              // close previous kennel type section
              echo "</div>";
          }
          echo "<h2 class='kennel-type-header'>" . htmlspecialchars($kennelType) . " Kennels</h2>";
          echo "<div class='kennel-type-section'>";
          $currentType = $kennelType;
      }

      // Fetch animals in this kennel
      $animals_result = $conn->query("
          SELECT animalID, name, breed, age, intakeDate 
          FROM animal 
          WHERE kennelID = $kennelID AND isDeleted = 0 
          ORDER BY intakeDate DESC
      ");
  ?>
      <div class="section">
        <div class = "section2">
        <div class="section-header">
          <h4>Kennel <?= htmlspecialchars($kennelID) ?></h4>
          <h5>Capacity <?= "($occupation/$capacity)" ?></h5>
        </div>

        <div class="section-list">
            <?php
            if ($animals_result && $animals_result->num_rows > 0) {
                while ($animal = $animals_result->fetch_assoc()) {
                    echo '<div class="list-item">';
                    echo '<span class="item-title">' . htmlspecialchars($animal['name']) . '</span>';
                    echo '<span class="item-subtitle">' . htmlspecialchars($animal['breed']) . ' | ' . htmlspecialchars($animal['age']) . ' yrs</span>';
                    echo '<span class="item-date">Added: ' . htmlspecialchars($animal['intakeDate']) . '</span>';
                    echo '<a href="animalUpdate.php?animalID=' . $animal['animalID'] . '" class="view-btn">View Animal</a>';
                    echo '</div>';
                }
            } else {
                echo '<div class="list-item"><span class="item-subtitle">No animals in this kennel.</span></div>';
            }
            ?>
        </div>
        </div>
      </div>
  <?php } ?>

  </div>
</div>

</body>
</html>
