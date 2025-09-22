<?php
 session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
include 'sidebar_admin.php';
require '../databaseConnection.php';

// Get the post-follow-up ID from URL
$followUpID = isset($_GET['followUpID']) ? intval($_GET['followUpID']) : 0;
if ($followUpID <= 0) die("Invalid follow-up ID.");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $outcome = isset($_POST['outcome']) ? $conn->real_escape_string($_POST['outcome']) : '';

    // Collect criteria answers
    $criteriaAnswers = [];
    for ($i = 1; $i <= 7; $i++) {
        $criteriaAnswers[$i] = isset($_POST['criteria'][$i]) ? $conn->real_escape_string($_POST['criteria'][$i]) : 'Pending';
    }

        $uniqueAnswers = array_values(array_unique($criteriaAnswers));
    if (count($uniqueAnswers) === 1) {
        if ($uniqueAnswers[0] === 'Yes') {
            $criteriaMeet = 'Yes';
        } elseif ($uniqueAnswers[0] === 'No') {
            $criteriaMeet = 'No';
        } else {
            $criteriaMeet = 'Pending';
        }
    } else {
        $criteriaMeet = 'Pending';
    }


    // Build the update SQL
    $updates = [];
    for ($i = 1; $i <= 7; $i++) {
        $updates[] = "q$i='{$criteriaAnswers[$i]}'";
    }
    $updates[] = "criteriaMeet='$criteriaMeet'";
    $updates[] = "outcome='$outcome'";

    $updateSql = "UPDATE postfollowup SET " . implode(", ", $updates) . " WHERE followUpID=$followUpID";

    if ($conn->query($updateSql)) {
        echo "<p style='color:green;'>Follow-up updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error updating follow-up: " . $conn->error . "</p>";
    }
}

// Fetch follow-up info
$sql = "SELECT pf.*, a.name AS animalName, u.Fname, u.Sname
        FROM postfollowup pf
        INNER JOIN adoption ad ON pf.adoptionID = ad.adoptionID
        INNER JOIN animal a ON ad.animalID = a.animalID
        INNER JOIN user u ON ad.userID = u.userID
        WHERE pf.followUpID = $followUpID";

$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) die("Follow-up not found.");

$row = $result->fetch_assoc();

// Previous criteria values from columns
$prevCriteria = [];
for ($i = 1; $i <= 7; $i++) {
    $prevCriteria[$i] = isset($row["q$i"]) ? $row["q$i"] : 'Pending';
}

// Criteria questions
$criteriaQuestions = [
    1 => "Is the animal receiving proper nutrition?",
    2 => "Is the animal living in a safe environment?",
    3 => "Is the animal receiving regular veterinary care?",
    4 => "Is the animal comfortable with the family?",
    5 => "Does the animal have sufficient exercise and stimulation?",
    6 => "Are there no signs of neglect or abuse?",
    7 => "Is the adopter complying with any adoption agreements?"
];
?>

<html>
<head>
    <link rel="stylesheet" href="sidebar_admin.css">
    <link rel="stylesheet" href="followUpUpdate.css">
</head>
<body>

<div class="container">
    <div class="page-heading">
    <h2> Adoption Criteria Check</h2>
</div>
<div class="content-container">

    <form method="POST" action="">
    <p><strong>User:</strong> <?php echo htmlspecialchars($row['Fname'] . ' ' . $row['Sname']); ?></p>
    <p><strong>Animal Adopted:</strong> <?php echo htmlspecialchars($row['animalName']); ?></p>
    <p><strong>Follow-Up Date:</strong> <?php echo htmlspecialchars($row['followUpDate']); ?></p>

    <h3>Adoption Criteria</h3>
    
    <div class="criteria-container">
        <?php
        foreach ($criteriaQuestions as $key => $question) {
            $prevValue = $prevCriteria[$key];
            echo "<div class='criteria-item'>";
            echo "<label for='criteria_$key'>$question</label>";
            echo "<select name='criteria[$key]' id='criteria_$key' required>
                    <option value='Pending' " . ($prevValue == 'Pending' ? 'selected' : '') . ">Pending</option>
                    <option value='Yes' " . ($prevValue == 'Yes' ? 'selected' : '') . ">Yes</option>
                    <option value='No' " . ($prevValue == 'No' ? 'selected' : '') . ">No</option>
                  </select>";
            echo "</div>";
        }
        ?>

    </div>

    <label for="outcome">Outcome (e.g., Animal taken back, Adoption successful)</label>
    <textarea name="outcome" id="outcome" rows="4"><?php echo htmlspecialchars($row['outcome']); ?></textarea>

    <div class="form-buttons">
        <button type="submit" class="updateBtn">Update Follow-Up</button>
        <a href="postFollowUp.php" class="cancelBtn">Cancel</a>
    </div>

</form>

</div>
</div>
</body>
</html>
