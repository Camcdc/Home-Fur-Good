<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
// Do not force login to view animal details; only require login for adoption
include '../databaseConnection.php';


// Get animal_id from URL (?id=a00001), fallback to demo if missing
$animal_id = isset($_GET['id']) ? $_GET['id'] : 'a00001';

// Fetch animal info
$sql = "SELECT * FROM animal WHERE animalID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  die("SQL error: " . $conn->error);
}
$stmt->bind_param("s", $animal_id);
$stmt->execute();
$result = $stmt->get_result();
$animal = $result->fetch_assoc();

// Fallback demo data if not found
if (!$animal) {
  $animal = [
    'animalID' => 'a00001',
    'name' => 'Max',
    'age' => '2 years',
    'species' => 'Dog',
    'sex' => 'Male',
    'breed' => 'Labrador',
    'status' => 'Available',
    'description' => "Max is a friendly Labrador who loves to play and is great with children. He's fully trained and eager to find his forever home.",
    'image_url' => 'https://place-puppy.com/600x400'
  ];
}

// Fetch latest medical report
$medical_sql = "SELECT * FROM medical_report WHERE animalID = ? AND isDeleted = 0 ORDER BY reportDate DESC";
$medical_stmt = $conn->prepare($medical_sql);
$medical_reports = [];
if ($medical_stmt) {
    $medical_stmt->bind_param("s", $animal['animalID']);
    $medical_stmt->execute();
    $medical_result = $medical_stmt->get_result();
    
    while ($report = $medical_result->fetch_assoc()) {
    // Fetch procedures for this report
    $proc_sql = "SELECT mp.procedureName, mr.notes, mp.frequency, mp.cost, mp.procedureRequired
      FROM medical_report mr
      JOIN medicalprocedure mp ON mr.procedureID = mp.procedureID
      WHERE mr.reportID = ?
      ";
$proc_stmt = $conn->prepare($proc_sql);

if (!$proc_stmt) {
    die("Procedure SQL prepare failed: " . $conn->error . " | SQL: " . $proc_sql);
}


    $proc_stmt->bind_param("i", $report['reportID']);

    if (!$proc_stmt) {
        die("Procedure SQL prepare failed: " . $conn->error);
    }

    $proc_stmt->execute();
    $proc_result = $proc_stmt->get_result();
    $procedures = [];
    while ($proc = $proc_result->fetch_assoc()) {
        $procedures[] = $proc;
    }
    $report['procedures'] = $procedures;
    $medical_reports[] = $report;
}

}


// Fetch 3 random suggested animals (excluding current)
$suggested_sql = "SELECT animalID, name, picture FROM animal WHERE isDeleted = 0 AND status = 'Available' AND animalID != ? ORDER BY RAND() LIMIT 3";
$suggested_stmt = $conn->prepare($suggested_sql);
if (!$suggested_stmt) {
  die("SQL error (suggested): " . $conn->error);
}
$suggested_stmt->bind_param("s", $animal_id);
$suggested_stmt->execute();
$suggested_result = $suggested_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($animal['name']) ?> - Home Fur Good</title>
  <link rel="stylesheet" href="../navbar functionalities/login-register.css">
  <link rel="stylesheet" href="../navbar functionalities/navbar.css">
  <link rel="stylesheet" href="browseAnimals.css">
  <link rel="stylesheet" href="animal.css">
  <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>
  <?php include '../navbar functionalities/navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content" style=" flex-direction: column; gap: 2rem; align-items: stretch; margin-top: 4rem;">
    <div style="display: flex; gap: 3rem; align-items: flex-start; width: 100%;">
      <!-- Animal Image -->
      <div style="flex: 0 0 350px; display: flex; justify-content: center; align-items: flex-start;">
        <img src="../pictures/animals/<?= htmlspecialchars($animal['picture']) ?>" 
         alt="<?= htmlspecialchars($animal['name']) ?>" 
         class="animal-img" 
         style="max-width: 100%; border-radius: 16px; box-shadow: 0 2px 12px #e0f7fa;">
      </div>

      <!-- Animal Info -->
      <div class="animal-info" style="flex: 1; min-width: 350px; display: flex; flex-direction: column; justify-content: flex-start; height: 100%;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
          <span style="color: ##3a6fa0; font-weight: bold; font-size: 2rem;">
              <?= htmlspecialchars($animal['name']) ?>
          </span>
          <span class="animal-type-tag" style="background:#eee; color:#333; border-radius:12px; padding:2px 10px; font-size:1.1em; font-weight:600;"> 
              <?= htmlspecialchars($animal['species']) ?>
          </span>
          <span style="font-size:1.1em; color:#555; margin-left:1rem;"><strong>Age:</strong> <?= htmlspecialchars($animal['age']) ?></span>
          <span style="font-size:1.1em; color:#555; margin-left:1rem;"><strong>Gender:</strong> <?= htmlspecialchars($animal['sex']) ?></span>
          <span style="font-size:1.1em; color:#555; margin-left:1rem;"><strong>Breed:</strong> <?= htmlspecialchars($animal['breed']) ?></span>
          <div style="margin-left:2rem; min-width:220px;">
            <h3 style="margin-bottom: 0.5rem;">Medical Information</h3>
            <?php if (!empty($medical_reports)): ?>
              <div style="max-height: 250px;overflow-y: auto;padding: 0.5rem;border: 1px solid #ddd;border-radius: 8px;background:#fafafa;width:100%;width=600px;width: 600px;">
                  <?php foreach($medical_reports as $medical): ?>
                    <li style="margin-bottom: 1rem; padding-bottom:0.5rem; border-bottom:1px solid #eee;">
                      <strong>Report Date:</strong> <?= htmlspecialchars($medical['reportDate'] ?? 'N/A') ?><br>
                      <strong>Diagnosis:</strong> <?= htmlspecialchars($medical['diagnosis'] ?? 'N/A') ?><br>


                               
                      <?php if (!empty($medical['procedures'])): ?>
                        <strong>Procedures:</strong>
                        <ul style="margin-left: 1rem;">
                          <?php foreach($medical['procedures'] as $proc): ?>
                            <li>
                              <?= htmlspecialchars($proc['procedureName']) ?>
                              <?php if(!empty($proc['notes'])): ?>
                                - <?= htmlspecialchars($proc['notes']) ?>
                              <?php endif; ?>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </li>
                  <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p>No medical information available.</p>
            <?php endif; ?>

            <!-- Adopt Button -->
          <div class="adopt-btn" style="width: 100%; display: flex; justify-content: center; margin-top: 2rem;">
        <div style="max-width: 350px; width: 100%;">
          <?php 
            if (isset($_SESSION['userID']) && in_array($_SESSION['Role'], ['User', 'Volunteer', 'Fosterer'])): 
          ?>
            <form action="../dashboard_user/adoptionApplication.php" method="GET" style="width:100%;">
              <input type="hidden" name="animal_id" value="<?= htmlspecialchars($animal['animalID']) ?>">
              <button type="submit" style="width:100%;">Adopt</button>
            </form>
          <?php else: ?>
            <form action="../navbar functionalities/userLoginC.php" method="GET" style="width:100%;">
              <input type="hidden" name="adopt_redirect" value="1">
              <input type="hidden" name="animal_id" value="<?= htmlspecialchars($animal['animalID']) ?>">
              <button type="submit" style="width:100%;">Adopt</button>
            </form>
          <?php endif; ?>


            </div>
      </div>

          </div>

          </div>
        </div>
      </div>

    </div>    
  </main>

<!-- About-->
<div class="about-title" style="margin-top: 2rem; padding: 1.5rem 2rem;  max-width: 900px; margin-left: auto; margin-right: auto;">
    <h3 style="font-size: 2rem; color: #2b3e55; font-weight: 700; text-align: center;">About <?= htmlspecialchars($animal['name']) ?></h3>
</div>

<!-- Description-->
<div class="about-description" style="    padding: 0rem 2rem;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;">
    <p style="font-size: 1.2rem; line-height: 1.6; color: #4a4a4a; text-align: justify; margin-top: 0; margin-bottom: 1rem;    white-space: pre-wrap;
    word-wrap: break-word;
    overflow-wrap: break-word;">
        <?= nl2br(htmlspecialchars(trim($animal['description']))) ?>
    </p>
</div>




<!-- You May Also Like -->
<div class="suggested-container">
<section class="suggested">
  <div class="suggested-heading">
    <h3>You May Also Like</h3>
  </div>
  <div class="suggested-grid">
    <?php while ($suggested = $suggested_result->fetch_assoc()): ?>
      <div class="suggested-item">
        <a href="../landing pages/animal1.php?id=<?= urlencode($suggested['animalID']) ?>">
          <?php if (!empty($suggested['picture'])): ?>
            <img src="../pictures/animals/<?= htmlspecialchars($suggested['picture']) ?>" alt="<?= htmlspecialchars($suggested['name']) ?>" class="suggested-img">
          <?php else: ?>
            <img src="images/default.jpg" alt="Animal" class="suggested-img">
          <?php endif; ?>
          <span class="suggested-name"><?= htmlspecialchars($suggested['name']) ?></span>
        </a>
      </div>
    <?php endwhile; ?>
  </div>
</section>
</div>


<?php
include 'footer.php';
?>
</body>
</html>

