<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Only allow access if session is valid for protected actions
require_once "../databaseConnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname   = trim($_POST['Fname'] ?? '');
    $surname     = trim($_POST['Sname'] ?? '');
    $email       = trim($_POST['Email'] ?? '');
    $password    = trim($_POST['Password'] ?? '');
    $dateofbirth = trim($_POST['DateOfBirth'] ?? '');
    $cellnumber  = trim($_POST['CellNumber'] ?? '');
    $address     = trim($_POST['Address'] ?? '');

    // 1. Check required fields
    if (!$firstname || !$surname || !$email || !$password || !$dateofbirth || !$cellnumber || !$address) {
        $error_message = "⚠ Please fill in all required fields.";
    } else {
        // 2. Check if email already exists
        $checkEmail = $conn->prepare("SELECT userID FROM user WHERE Email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        $checkNumber = $conn->prepare("SELECT userID FROM user WHERE CellNumber = ?");
        $checkNumber->bind_param("s", $cellnumber);
        $checkNumber->execute();
        $checkNumber->store_result();

        if ($checkEmail->num_rows > 0) {
            $error_message = "⚠ Email already registered. Please log in.";
        } elseif ($checkNumber->num_rows > 0) {
            $error_message = "⚠ Cell Number already registered. Please log in.";
        } else {
            // 3. Check age
            $dob = new DateTime($dateofbirth);
            $today = new DateTime();
            $age = $today->diff($dob)->y;

            if ($age < 18) {
                $error_message = "⚠ You must be at least 18 years old to register.";
            } else {
                // 4. Determine role automatically
                if (stripos($email, 'rustaff') !== false && stripos($email, '@spca.co.za') !== false) {
                    $role = "Administrator";
                } elseif (stripos($email, 'ruvet') !== false && stripos($email, '@spca.co.za') !== false) {
                    $role = "Veterinarian";
                } else {
                    $role = "User";
                }

                // 5. Hash password and insert
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO user (Fname, Sname, Email, Password, DateOfBirth, CellNumber, Address, Role)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssss", $firstname, $surname, $email, $hashedPassword, $dateofbirth, $cellnumber, $address, $role);

            if ($stmt->execute()) {
    $animal_id = $_GET['animal_id'] ?? $_POST['animal_id'] ?? null;
    $adopt_redirect = $_GET['adopt_redirect'] ?? $_POST['adopt_redirect'] ?? null;
    $cruelty_redirect = $_POST['cruelty_redirect'] ?? '0';
    $redirect_url = $_POST['redirect_url'] ?? '../dashboard_user/CreateCrueltyReport.php';
    echo "<script>alert('Registration Successful' )</script>";

    // Store new user's ID in session
    $user_id = $conn->insert_id;
    $_SESSION['userID'] = $user_id;
    $_SESSION['Role'] = $role;
    $_SESSION['Fname'] = $firstname;
    $_SESSION['Sname'] = $surname;
    $_SESSION['DateOfBirth'] = $dateofbirth;
    $_SESSION['CellNumber'] = $cellnumber;
    $_SESSION['Address'] = $address;

    // Redirect based on role
    if ($role === "Administrator") {
        header("Location: ../dashboard_admin/dashboard_admin.php");
        exit;
    } elseif ($role === "Veterinarian") {
        header("Location: ../dashboard_vet/dashboard_vet.php");
        exit;
    }

    // Redirect based on context for User
    if ($adopt_redirect === '1' && $animal_id) {
        header("Location: ../dashboard_user/adoptionApplication.php?animal_id=" . urlencode($animal_id));
        exit;
    }
    if ($cruelty_redirect === '1') {
        header("Location: ../dashboard_user/CreateCrueltyReport.php");
        exit;
    }
    // Default redirect (if none of the above)
    $redirect_url = $_POST['redirect_url'] ?? '../dashboard_user/CreateCrueltyReport.php';
    header("Location: " . $redirect_url);
    exit;
} else {
    $error_message = "Error: " . $stmt->error;
}
            }
        }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Registration</title>
    <link rel="stylesheet" href="userRegister.css" />
</head>
<body>


<div class="auth-wrapper">
  <h2>Welcome!</h2>
  <p class="welcome">Join our community by creating an account. We're excited to have you!</p>

  <h1>Register</h1>

  <!-- your error message if any -->
  <?php if (!empty($error_message)) : ?>
      <p class="error"><?= htmlspecialchars($error_message) ?></p>
  <?php endif; ?>

  <form method="POST" action="">
  <div class="form-group">
    <label for="Fname">First Name</label>
    <input type="text" id="Fname" name="Fname" required>
  </div>

  <div class="form-group">
    <label for="Sname">Surname</label>
    <input type="text" id="Sname" name="Sname" required>
  </div>

  <div class="form-group">
    <label for="Email">Email</label>
    <input type="email" id="Email" name="Email" required>
  </div>

  <div class="form-group">
    <label for="Password">Password</label>
    <input type="password" id="Password" name="Password" required>
  </div>

  <div class="form-group">
    <label for="DateOfBirth">Date of Birth</label>
    <input type="date" id="DateOfBirth" name="DateOfBirth" required>
  </div>

  <div class="form-group">
    <label for="CellNumber">Cell Number</label>
    <input type="text" id="CellNumber" name="CellNumber" required>
  </div>

  <div class="form-group full-width">
    <label for="Address">Address</label>
    <textarea id="Address" name="Address" required></textarea>
  </div>

  <input type="hidden" name="redirect_url" value="../landing pages/homepage.php">
  <?php if (isset($_GET['cruelty_redirect'])): ?>
    <input type="hidden" name="cruelty_redirect" value="<?= htmlspecialchars($_GET['cruelty_redirect']) ?>">
  <?php endif; ?>
  <button type="submit" class="full-width">Register</button>
</form>

 <p class="switch">
    Already registered? 
    <a href="userLoginC.php<?php 
        // Check for cruelty redirect first
        if (isset($_GET['cruelty_redirect']) && $_GET['cruelty_redirect'] === '1') {
            echo '?cruelty_redirect=1';
        } 
        // Otherwise, check for adoption redirect
        elseif (isset($_GET['adopt_redirect']) && isset($_GET['animal_id'])) {
            echo '?adopt_redirect=1&animal_id=' . urlencode($_GET['animal_id']);
        } 
    ?>">Login here</a>
</p>

</div>

</body>
</html>

