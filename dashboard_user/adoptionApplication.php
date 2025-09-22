<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
if (!isset($_SESSION['userID'])) {
   header("Location: ../landing pages/homepage.php");
  echo '<!DOCTYPE html><html><head><title>Registration Required</title></head><body>';
  echo '<h2 style="color:red;">You must be registered and signed in to apply for adoption.</h2>';
  echo '<p><a href="../navbar functionalities/userRegisterC.php?adopt_redirect=1&animal_id=' . urlencode($_GET['animal_id'] ?? '') . '">Register here</a></p>';
  echo '</body></html>';
  exit;
}




include '../databaseConnection.php';

// Get animal_id
// Get animal_id from GET or POST
$animal_id = $_GET['animal_id'] ?? $_POST['animal_id'] ?? null;
if (!$animal_id) {
  die("No animal selected.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userID = $_SESSION['userID'];
  // Check for existing active application for this animal
  $check_sql = "SELECT adoptionID FROM adoption WHERE userID = ? AND animalID = ? AND isDeleted = 0";
  $check_stmt = $conn->prepare($check_sql);
  $check_stmt->bind_param("ii", $userID, $animal_id);
  $check_stmt->execute();
  $check_result = $check_stmt->get_result();
  if ($check_result->num_rows > 0) {
    echo '<div style="color:red;font-weight:bold;max-width:600px;margin:2rem auto;text-align:center;">You already have an active adoption application for this animal. Please wait for a decision or cancel your existing application before applying again.</div>';
    exit;
  }

  $housing = $_POST['housing'] ?? '';
  $landlord_permission = $_POST['landlord_permission'] ?? '';
  $residence_type = $_POST['residence_type'] ?? '';
  $yard_size = $_POST['yard_size'] ?? '';
  $household_members = $_POST['household_members'] ?? '';
  $household_allergies = $_POST['household_allergies'] ?? '';
  $past_pets = $_POST['past_pets'] ?? '';
  $current_pets = $_POST['current_pets'] ?? '';
  $pet_training_experience = $_POST['pet_training_experience'] ?? '';
  $work_hours = $_POST['work_hours'] ?? '';
  $travel_plan = $_POST['travel_plan'] ?? '';
  $activity_level = $_POST['activity_level'] ?? '';
  $reason = $_POST['reason'] ?? '';
  $financial_ready = $_POST['financial_ready'] ?? '';
  $commitment_agreement = $_POST['commitment_agreement'] ?? '';
  $backup_plan = $_POST['backup_plan'] ?? '';
  $emergency_contact = $_POST['emergency_contact'] ?? '';
  $vet_reference = $_POST['vet_reference'] ?? '';

  $adoption_sql = "INSERT INTO adoption (
    userID, animalID, adoptionDate, status,
    housing, landlord_permission, residence_type, yard_size, household_members, household_allergies,
    past_pets, current_pets, pet_training_experience,
    work_hours, travel_plan, activity_level,
    reason, financial_ready, commitment_agreement, backup_plan,
    emergency_contact, vet_reference, isDeleted
  ) VALUES (?, ?, NOW(), 0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
  $adoption_stmt = $conn->prepare($adoption_sql);
  if (!$adoption_stmt) {
  echo '<div style="color:red;font-weight:bold;">SQL Error: ' . htmlspecialchars($conn->error) . '</div>';
  echo '<pre>POST DATA: ' . print_r($_POST, true) . '</pre>';
  echo '<pre>SQL: ' . htmlspecialchars($adoption_sql) . '</pre>';
  echo '<pre>Session: ' . print_r($_SESSION, true) . '</pre>';
  exit;
  }
  $adoption_stmt->bind_param(
  "iissssssssssssssssss",
    $userID, $animal_id,
    $housing, $landlord_permission, $residence_type, $yard_size, $household_members, $household_allergies,
    $past_pets, $current_pets, $pet_training_experience,
    $work_hours, $travel_plan, $activity_level,
    $reason, $financial_ready, $commitment_agreement, $backup_plan,
    $emergency_contact, $vet_reference
  ); // isDeleted is hardcoded to 0 in SQL
  if (!$adoption_stmt->execute()) {
    echo '<div style="color:red;font-weight:bold;">Execution Error: ' . htmlspecialchars($adoption_stmt->error) . '</div>';
    echo '<pre>POST DATA: ' . print_r($_POST, true) . '</pre>';
    echo '<pre>SQL: ' . htmlspecialchars($adoption_sql) . '</pre>';
    echo '<pre>Session: ' . print_r($_SESSION, true) . '</pre>';
    echo '<pre>Bind Param Types: iissssssssssssssssss</pre>';
    echo '<pre>Bind Param Values: ' . print_r([
      $userID, $animal_id,
      $housing, $landlord_permission, $residence_type, $yard_size, $household_members, $household_allergies,
      $past_pets, $current_pets, $pet_training_experience,
      $work_hours, $travel_plan, $activity_level,
      $reason, $financial_ready, $commitment_agreement, $backup_plan,
      $emergency_contact, $vet_reference
    ], true) . '</pre>';
    echo '<pre>MySQL errno: ' . $adoption_stmt->errno . '</pre>';
    echo '<pre>MySQL error: ' . htmlspecialchars($adoption_stmt->error) . '</pre>';
    echo '<pre>Connection errno: ' . $conn->errno . '</pre>';
    echo '<pre>Connection error: ' . htmlspecialchars($conn->error) . '</pre>';
    exit;
  }
  header("Location: adoption_success.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
  <meta charset="UTF-8">
  <title>Adoption Application</title>
  <link rel="stylesheet" type="text/css" href="../dashboard_user/adoptAppform.css">
  <script>
    function countWords(str) {
      return str.trim().split(/\s+/).filter(Boolean).length;
    }

    function validateForm(e) {
      var notification = document.getElementById('notification');
      var valid = true;
      var messages = [];
      // Validate required fields
      var requiredFields = document.querySelectorAll('[required]');
      requiredFields.forEach(function(field) {
        var label = field.name.replace(/_/g, ' ');
        if (!field.value.trim()) {
          valid = false;
          messages.push(label + ' is required.');
        } else {
          // Type-specific validation
          if (field.type === 'email') {
            var emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            if (!emailPattern.test(field.value)) {
              valid = false;
              messages.push('Please enter a valid email address.');
            }
          }
          if (field.name === 'emergency_contact') {
            var phonePattern = /^[0-9]{10,15}$/;
            if (!phonePattern.test(field.value)) {
              valid = false;
              messages.push('Emergency contact number must be 10-15 digits.');
            }
          }
          if (field.type === 'number') {
            var min = field.min ? parseFloat(field.min) : null;
            var max = field.max ? parseFloat(field.max) : null;
            var val = parseFloat(field.value);
            if ((min !== null && val < min) || (max !== null && val > max)) {
              valid = false;
              messages.push(label + ' must be between ' + min + ' and ' + max + '.');
            }
          }
        }
      });
      // Word limit validation for long text fields
      var wordLimits = {
        household_members: 100,
        past_pets: 200,
        current_pets: 200,
        pet_training_experience: 200,
        reason: 200,
        backup_plan: 200
      };
      Object.keys(wordLimits).forEach(function(fieldName) {
        var fields = document.getElementsByName(fieldName);
        if (fields.length > 0) {
          var field = fields[0];
          if (field && field.value) {
            var words = countWords(field.value);
            if (words > wordLimits[fieldName]) {
              valid = false;
              messages.push(fieldName.replace(/_/g, ' ') + ' must be ' + wordLimits[fieldName] + ' words or fewer. You entered ' + words + ' words.');
            }
          }
        }
      });
      // Custom: check for radio/checkbox groups
      var radioGroups = {};
      var radios = document.querySelectorAll('input[type="radio"][required], input[type="checkbox"][required]');
      radios.forEach(function(radio) {
        if (!radioGroups[radio.name]) radioGroups[radio.name] = [];
        radioGroups[radio.name].push(radio);
      });
      Object.keys(radioGroups).forEach(function(name) {
        var checked = radioGroups[name].some(function(r) { return r.checked; });
        if (!checked) {
          valid = false;
          messages.push(name.replace(/_/g, ' ') + ' is required.');
        }
      });
      if (!valid) {
        notification.innerHTML = messages.join('<br>');
        notification.style.display = 'block';
        e.preventDefault();
        return false;
      } else {
        notification.style.display = 'none';
      }
      return true;
    }
    window.onload = function() {
      var form = document.querySelector('form');
      if (form) form.onsubmit = validateForm;
    };
  </script>
</head>
<body>
  <form class="form-container" action="adoptionApplication.php" method="POST">
    <div id="notification" class="notification"></div>
    <input type="hidden" name="animal_id" value="<?= htmlspecialchars($animal_id) ?>">
    <h1>Adoption Application</h1>
    <h2>Household & Living Situation</h2>
    <div class="row">
      <div class="col">
        <label>Do you own or rent your home?</label>
        <select name="housing" maxlength="250" required>
          <option value="Own">Own</option>
          <option value="Rent">Rent</option>
        </select>
      </div>
      <div class="col">
        <label>If renting, do you have landlord’s permission for pets?</label>
        <select name="landlord_permission" maxlength="10">
          <option value="">N/A</option>
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>
      </div>
      <div class="col">
  <label>Type of residence (house, apartment, farm, etc.)</label>
  <input type="text" name="residence_type" required maxlength="50" placeholder="House, Apartment, Farm">
      </div>
      <div class="col">
  <label>Size of yard / outdoor space (if relevant)</label>
  <input type="text" name="yard_size" maxlength="50" placeholder="Large, Small, None">
      </div>
    </div>
    <div class="row">
      <div class="col">
  <label>Who lives in the household? (adults, children, ages)</label>
  <textarea name="household_members" required maxlength="250" placeholder="e.g. 2 adults (ages 35, 37), 2 children (ages 8, 12)"></textarea>
      </div>
      <div class="col">
  <label>Are there any allergies in the home?</label>
  <input type="text" name="household_allergies" maxlength="100" placeholder="e.g. None, Pollen, Pet Dander">
      </div>
    </div>
    <h2>Pet Ownership Experience</h2>
    <div class="row">
      <div class="col">
  <label>Have you owned pets before? What kind?</label>
  <textarea name="past_pets" maxlength="250" placeholder="e.g. Dogs, Cats, Birds"></textarea>
      </div>
      <div class="col">
  <label>Do you currently have other pets? If yes, species/ages/spayed-neutered?</label>
  <textarea name="current_pets" maxlength="250" placeholder="e.g. 1 dog (age 5, neutered), 2 cats (ages 2, 4)"></textarea>
      </div>
      <div class="col">
  <label>Experience with training, handling, or caring for pets</label>
  <textarea name="pet_training_experience" maxlength="250" placeholder="e.g. Puppy training, obedience classes"></textarea>
      </div>
    </div>
    <h2>Lifestyle & Availability</h2>
    <div class="row">
      <div class="col">
  <label>Typical work hours / time pet will be alone daily</label>
  <input type="text" name="work_hours" maxlength="50" placeholder="e.g. 9am-5pm, pet alone 6 hours">
      </div>
      <div class="col">
  <label>Travel frequency (who will look after pet when away)</label>
  <input type="text" name="travel_plan" maxlength="100" placeholder="e.g. Rarely travel, family cares for pet">
      </div>
      <div class="col">
  <label>Level of activity (important for matching active animals like dogs)</label>
  <input type="text" name="activity_level" maxlength="50" placeholder="e.g. Active, Moderate, Sedentary">
      </div>
    </div>
    <h2>Care & Commitment</h2>
    <div class="row">
      <div class="col">
  <label>Why do you want to adopt this animal?</label>
  <textarea name="reason" required maxlength="250" placeholder="e.g. Lifelong love of animals, companionship"></textarea>
      </div>
      <div class="col">
        <label>Are you financially able to cover vet visits, food, grooming, emergencies?</label>
        <select name="financial_ready" required maxlength="10">
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>
      </div>
      <div class="col">
        <label>Do you agree to spay/neuter, vaccinations, and regular vet care?</label>
        <select name="commitment_agreement" required maxlength="10">
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>
      </div>
      <div class="col">
  <label>What will you do if you can no longer keep the pet?</label>
  <textarea name="backup_plan" maxlength="250" placeholder="e.g. Find a loving home, contact shelter"></textarea>
      </div>
    </div>
    <h2>References & Verification</h2>
    <div class="row">
      <div class="col">
  <label>Emergency contact / reference</label>
  <input type="text" name="emergency_contact" maxlength="100" pattern="[0-9]{10,15}" placeholder="e.g. 0821234567">
      </div>
      <div class="col">
  <label>Vet reference (if you had pets before)</label>
  <input type="text" name="vet_reference" maxlength="100" placeholder="e.g. Dr. Smith, 0123456789">
      </div>
    </div>
    <input type="submit" value="Submit Application">
  </form>
</body>
</html>
