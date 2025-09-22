<?php
session_start();
include '../databaseConnection.php';

$userID = $_SESSION['userID'] ?? null;
if (!$userID) {
    echo '<div style="padding:2rem;text-align:center;color:red;font-size:1.2rem;">You must be logged in to view your adoption application status.</div>';
    exit;
}

// Get all adoption applications for this user
$sql = "SELECT a.*, an.name AS animal_name, an.species, an.breed, an.picture
        FROM adoption a
        JOIN animal an ON a.animalID = an.animalID
        LEFT JOIN postfollowup pf ON a.adoptionID = pf.adoptionID
        WHERE a.userID = ? AND a.isDeleted = 0
          AND (pf.retrieved IS NULL OR pf.retrieved != 'Retrieved')
        ORDER BY a.adoptionDate DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: no_adoption.php');
    exit;
}

// Handle adoption cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_adoption']) && isset($_POST['adoptionID'])) {
    $adoptionID = $_POST['adoptionID'];
    // Mark adoption as deleted
    $cancel_sql = "UPDATE adoption SET isDeleted = 1 WHERE adoptionID = ?";
    $cancel_stmt = $conn->prepare($cancel_sql);
    $cancel_stmt->bind_param("i", $adoptionID);
    $cancel_stmt->execute();
    // Set animal status back to Available
    $animal_sql = "SELECT animalID FROM adoption WHERE adoptionID = ?";
    $animal_stmt = $conn->prepare($animal_sql);
    $animal_stmt->bind_param("i", $adoptionID);
    $animal_stmt->execute();
    $animal_result = $animal_stmt->get_result();
    if ($animal_row = $animal_result->fetch_assoc()) {
        $animalID = $animal_row['animalID'];
        $update_animal_sql = "UPDATE animal SET status = 'Available' WHERE animalID = ?";
        $update_animal_stmt = $conn->prepare($update_animal_sql);
        $update_animal_stmt->bind_param("i", $animalID);
        $update_animal_stmt->execute();
    }
    // Delete postfollowup record
    $delete_postfollowup_sql = "DELETE FROM postfollowup WHERE adoptionID = ?";
    $delete_postfollowup_stmt = $conn->prepare($delete_postfollowup_sql);
    $delete_postfollowup_stmt->bind_param("i", $adoptionID);
    $delete_postfollowup_stmt->execute();
    header('Location: adoption_cancelled.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Adoption Applications</title>
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <link rel="stylesheet" href="adoptionStatus.css">
    <link rel="stylesheet" href="../landing pages/footer.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>
<?php include '../navbar functionalities/navbar.php'; ?>
<div class="adoption-status-main">
    <h1>My Adoption Applications</h1>
    <?php while ($app = $result->fetch_assoc()): ?>
        <?php
        // Status logic
        $status = 'Pending';
        $statusColor = '#fbbf24';
        if ($app['status'] == 1) {
            $status = 'Approved';
            $statusColor = '#34d399';
        } elseif ($app['status'] == 2) {
            $status = 'Rejected';
            $statusColor = '#f87171';
        } elseif ($app['status'] == 3) {
            $status = 'Approval Reverted';
            $statusColor = '#fbbf24';
        }
        // Fetch application answers for this adoption
        $app_answers = null;
        $app_id = $app['adoptionID'] ?? null;
        if ($app_id) {
            $answers_sql = "SELECT * FROM adoption WHERE adoptionID = ?";
            $answers_stmt = $conn->prepare($answers_sql);
            $answers_stmt->bind_param("i", $app_id);
            $answers_stmt->execute();
            $answers_result = $answers_stmt->get_result();
            $app_answers = $answers_result->fetch_assoc();
        }
        ?>
        <div class="adoption-status-card" style="max-width:900px; width:90%; margin:2rem auto;">
            <div class="animal-info">
                <img src="../pictures/animals/<?= htmlspecialchars($app['picture']) ?>" alt="<?= htmlspecialchars($app['animal_name']) ?>" class="animal-img">
                <div class="animal-details">
                    <h2><?= htmlspecialchars($app['animal_name']) ?></h2>
                    <p><strong>Species:</strong> <?= htmlspecialchars($app['species']) ?></p>
                    <p><strong>Breed:</strong> <?= htmlspecialchars($app['breed']) ?></p>
                </div>
            </div>
            <div class="status-info">
                <span class="status-label" style="background:<?= $statusColor ?>;color:#fff;">Status: <?= $status ?></span>
                <p class="status-date"><strong>Applied on:</strong> <?= date('F j, Y', strtotime($app['adoptionDate'])) ?></p>
                <?php if ($status == 'Pending' || $status == 'Approved'): ?>
                    <p><?= $status == 'Pending' ? 'Your application is being reviewed. You will be notified once a decision is made.' : 'Congratulations! Your adoption application has been approved. Please come collect your new pet!'; ?></p>
                    <form method="POST" style="margin-top:1rem;display:inline-block;">
                        <input type="hidden" name="adoptionID" value="<?= htmlspecialchars($app['adoptionID']) ?>">
                        <button type="submit" name="cancel_adoption" style="padding:8px 18px;font-size:1rem;background:#f87171;color:#fff;border:none;border-radius:6px;cursor:pointer;">Cancel Adoption</button>
                    </form>
                <?php elseif ($status == 'Rejected'): ?>
                    <p>We regret to inform you that your application was not successful. You may contact us for more information.</p>
                <?php elseif ($status == 'Approval Reverted'): ?>
                    <p>Your previous approval has been reverted. Please contact us for clarification or next steps.</p>
                <?php endif; ?>
                <button onclick="document.getElementById('answers-modal-<?= $app['adoptionID'] ?>').style.display='block'" style="margin-top:1rem;padding:8px 18px;font-size:1rem;background: #3a6fa0;color:#fff;border:none;border-radius:6px;cursor:pointer;">View My Application Answers</button>
            </div>
        </div>
        <!-- Modal for application answers -->
        <div id="answers-modal-<?= $app['adoptionID'] ?>" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999;">
          <div style="background:#fff;max-width:700px;width:90%;margin:60px auto;padding:32px;border-radius:12px;position:relative;max-height:80vh;overflow-y:auto;">
            <button onclick="document.getElementById('answers-modal-<?= $app['adoptionID'] ?>').style.display='none'" style="position:absolute;top:12px;right:18px;font-size:1.2rem;background:#eee;border:none;border-radius:50%;width:32px;height:32px;cursor:pointer;">&times;</button>
            <h2 style="margin-top:0;">Your Adoption Application Answers</h2>
            <?php function display($value) { return $value !== '' && isset($value) ? nl2br(htmlspecialchars($value)) : 'Info not available'; } ?>
            <?php if ($app_answers): ?>
              <table style="width:100%;border-collapse:collapse;table-layout:fixed;background:#fff;box-shadow:0 2px 8px #e5e7eb;">
                <?php
                $fields = [
                  'housing' => 'Do you own or rent your home?',
                  'landlord_permission' => 'If renting, do you have landlord’s permission for pets?',
                  'residence_type' => 'Type of residence (house, apartment, farm, etc.)',
                  'yard_size' => 'Size of yard / outdoor space (if relevant)',
                  'household_members' => 'Who lives in the household? (adults, children, ages)',
                  'household_allergies' => 'Are there any allergies in the home?',
                  'past_pets' => 'Have you owned pets before? What kind?',
                  'current_pets' => 'Do you currently have other pets? If yes, species/ages/spayed-neutered?',
                  'pet_training_experience' => 'Experience with training, handling, or caring for pets',
                  'work_hours' => 'Typical work hours / time pet will be alone daily',
                  'travel_plan' => 'Travel frequency (who will look after pet when away)',
                  'activity_level' => 'Level of activity (important for matching active animals like dogs)',
                  'reason' => 'Why do you want to adopt this animal?',
                  'financial_ready' => 'Are you financially able to cover vet visits, food, grooming, emergencies?',
                  'commitment_agreement' => 'Do you agree to spay/neuter, vaccinations, and regular vet care?',
                  'backup_plan' => 'What will you do if you can no longer keep the pet?',
                  'emergency_contact' => 'Emergency contact / reference',
                  'vet_reference' => 'Vet reference (if you had pets before)',
                  'adoptionDate' => 'Application Date'
                ];
                foreach ($fields as $field => $label) {
                  echo '<tr>';
                  echo '<th style="background:#f1f5f9;color:#2c3e50;font-size:16px;font-weight:600;text-align:left;border:1px solid #e0e0e0;padding:12px 15px;">' . $label . '</th>';
                  echo '<td style="font-size:15px;color:#34495e;border:1px solid #e0e0e0;padding:12px 15px;vertical-align:top;word-wrap:break-word;overflow-wrap:break-word;background:#fff;">' . display($app_answers[$field] ?? '') . '</td>';
                  echo '</tr>';
                }
                ?>
              </table>
            <?php else: ?>
              <p>No answers found for your application.</p>
            <?php endif; ?>
          </div>
        </div>
    <?php endwhile; ?>
</div>
<?php include '../landing pages/footer.php'; ?>
</body>
</html>
