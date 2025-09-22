<?php
session_start();
include '../databaseConnection.php'; 

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: ../navbar functionalities/userLoginC.php?foster_redirect=1");
    exit();
}

$userID = $_SESSION['userID'];
$message = '';
$messageType = '';

// Handle profile updates (Logic remains the same)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $phoneNumber = trim($_POST['phoneNumber']);
    $address = trim($_POST['address']);
    
    if (empty($firstName) || empty($lastName) || empty($email)) {
        $message = "First name, last name, and email are required fields.";
        $messageType = 'error';
    } else {
        $stmt_check = $conn->prepare("SELECT userID FROM user WHERE Email = ? AND userID != ?");
        $stmt_check->bind_param("si", $email, $userID);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $message = "This email address is already registered to another account.";
            $messageType = 'error';
        } else {
            $stmt_update = $conn->prepare("UPDATE user SET Fname = ?, Sname = ?, Email = ?, CellNumber = ?, Address = ? WHERE userID = ?");
            $stmt_update->bind_param("sssssi", $firstName, $lastName, $email, $phoneNumber, $address, $userID);
            
            if ($stmt_update->execute()) {
                $message = "Your profile has been updated successfully!";
                $messageType = 'success';
            } else {
                $message = "There was an error updating your profile.";
                $messageType = 'error';
            }
            $stmt_update->close();
        }
        $stmt_check->close();
    }
}

// Fetch user's profile information
$stmt_user = $conn->prepare("SELECT Fname, Sname, Email, CellNumber, Address, DateOfBirth FROM user WHERE userID = ?");
$stmt_user->bind_param("i", $userID);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
$stmt_user->close();

if (!$user) {
    header("Location: ../navbar functionalities/userLoginC.php");
    exit();
}

// Fetch user's foster applications
$stmt_apps = $conn->prepare("
    SELECT applicationID, fosterPreference, applicationStatus, applicationDate 
    FROM application 
    WHERE userID = ? AND applicationType = 'Foster' 
    ORDER BY applicationDate DESC
");
$stmt_apps->bind_param("i", $userID);
$stmt_apps->execute();
$applications = $stmt_apps->get_result();
$stmt_apps->close();

// --- CORRECTED QUERY ---
// Fetch animals currently assigned to the foster parent ONLY IF they have an approved foster application.
$fostered_animals = [];
$stmt_fosters = $conn->prepare("
    SELECT 
        a.Name AS animalName, a.species, a.breed, a.age
    FROM 
        adoption AS ad
    JOIN 
        animal AS a ON ad.animalID = a.animalID
    WHERE 
        ad.userID = ? AND ad.status = 'Fostered'
        AND EXISTS (
            SELECT 1 
            FROM application 
            WHERE userID = ad.userID 
            AND applicationType = 'Foster' 
            AND applicationStatus = 'Approved'
        )
    ORDER BY 
        a.Name ASC
");
$stmt_fosters->bind_param("i", $userID);
$stmt_fosters->execute();
$fosters_result = $stmt_fosters->get_result();
while ($row = $fosters_result->fetch_assoc()) {
    $fostered_animals[] = $row;
}
$stmt_fosters->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Foster Dashboard - SPCA</title>
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <style>
        /* CSS remains the same */
        @import url('https://fonts.googleapis.com/css2?family=Germania+One&family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Hind Siliguri', sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; padding-top: 70px; }
        .dashboard-header { background: linear-gradient(135deg, #2c5aa0, #1a3d73); color: white; padding: 40px 20px; text-align: center; }
        .dashboard-header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }
        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .dashboard-card { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header { background: #2c5aa0; color: white; padding: 20px; font-weight: 600; font-size: 1.2em; }
        .card-content { padding: 30px; }
        .full-width { grid-column: 1 / -1; }
        .profile-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group.full-width { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input, textarea { width: 100%; padding: 12px; border: 2px solid #dee2e6; border-radius: 8px; font-size: 16px; }
        .btn { display: inline-block; padding: 12px 24px; background: #2c5aa0; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; }
        .btn-secondary { background: transparent; color: #2c5aa0; border: 2px solid #2c5aa0; }
        .message { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .message.success { background: #d4edda; color: #155724; }
        .message.error { background: #f8d7da; color: #721c24; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .data-table th, .data-table td { padding: 15px; text-align: left; border-bottom: 1px solid #dee2e6; }
        .data-table th { background-color: #f8f9fa; }
        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .info-item { padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #2c5aa0; }
        .info-item strong { display: block; margin-bottom: 5px; }
        .empty-state { text-align: center; padding: 40px; color: #6c757d; }
        .edit-toggle { float: right; font-size: 14px; cursor: pointer; }
        .profile-view.hidden, .profile-edit { display: none; }
        .profile-edit.visible { display: block; }
        @media (max-width: 768px) { .dashboard-grid, .profile-form, .info-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include '../navbar functionalities/navbar.php'; ?>

    <div class="dashboard-header">
        <h1>My Foster Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($user['Fname']); ?>! Manage your foster journey here.</p>
    </div>

    <div class="dashboard-container">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    My Personal Information
                    <span class="edit-toggle" onclick="toggleEdit()">✏️ Edit</span>
                </div>
                <div class="card-content">
                    <div class="profile-view" id="profileView">
                        <div class="info-grid">
                            <div class="info-item"><strong>Name</strong><?php echo htmlspecialchars($user['Fname'] . ' ' . $user['Sname']); ?></div>
                            <div class="info-item"><strong>Email</strong><?php echo htmlspecialchars($user['Email']); ?></div>
                            <div class="info-item"><strong>Phone</strong><?php echo htmlspecialchars($user['CellNumber'] ?: 'Not provided'); ?></div>
                            <div class="info-item"><strong>Address</strong><?php echo htmlspecialchars($user['Address'] ?: 'Not provided'); ?></div>
                        </div>
                    </div>
                    <div class="profile-edit" id="profileEdit">
                        <form method="POST" action="">
                            <div class="profile-form">
                                <div class="form-group"><label for="firstName">First Name *</label><input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['Fname']); ?>" required></div>
                                <div class="form-group"><label for="lastName">Last Name *</label><input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['Sname']); ?>" required></div>
                                <div class="form-group"><label for="email">Email Address *</label><input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required></div>
                                <div class="form-group"><label for="phoneNumber">Phone Number</label><input type="tel" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($user['CellNumber']); ?>"></div>
                                <div class="form-group full-width"><label for="address">Address</label><textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['Address']); ?></textarea></div>
                            </div>
                            <div><button type="submit" name="update_profile" class="btn">Update Profile</button> <button type="button" class="btn btn-secondary" onclick="toggleEdit()">Cancel</button></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="card-header">Quick Actions</div>
                <div class="card-content" style="display: flex; flex-direction: column; gap: 15px;">
                    <a href="foster_application.php" class="btn">Submit New Foster Application</a>
                    <a href="../navbar functionalities/userLoginC.php?logout=true" class="btn btn-secondary">Logout</a>
                </div>
            </div>
        </div>
        <div class="dashboard-card full-width">
            <div class="card-header">My Foster Applications</div>
            <div class="card-content">
                <?php if ($applications->num_rows > 0): ?>
                    <table class="data-table">
                        <thead><tr><th>Application Date</th><th>Foster Preference</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php while ($app = $applications->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d F Y', strtotime($app['applicationDate'])); ?></td>
                                    <td><?php echo htmlspecialchars($app['fosterPreference']); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($app['applicationStatus']); ?>"><?php echo htmlspecialchars($app['applicationStatus']); ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state"><p>You haven't submitted any foster applications yet.</p><a href="foster_application.php" class="btn">Apply Now</a></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="dashboard-card full-width">
            <div class="card-header">Animals Currently In My Care</div>
            <div class="card-content">
                <?php if (!empty($fostered_animals)): ?>
                    <table class="data-table">
                        <thead><tr><th>Name</th><th>Species</th><th>Breed</th><th>Age</th></tr></thead>
                        <tbody>
                            <?php foreach ($fostered_animals as $animal): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($animal['animalName']); ?></td>
                                    <td><?php echo htmlspecialchars($animal['species']); ?></td>
                                    <td><?php echo htmlspecialchars($animal['breed']); ?></td>
                                    <td><?php echo htmlspecialchars($animal['age']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state"><p>You do not currently have any animals in your care.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        function toggleEdit() {
            document.getElementById('profileView').classList.toggle('hidden');
            document.getElementById('profileEdit').classList.toggle('visible');
            const editButton = document.querySelector('.edit-toggle');
            editButton.textContent = document.getElementById('profileEdit').classList.contains('visible') ? 'Cancel' : '✏️ Edit';
        }
    </script>
</body>
</html>