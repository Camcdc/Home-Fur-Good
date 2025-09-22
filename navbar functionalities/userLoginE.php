<?php
session_start();
require_once "../databaseConnection.php";

$redirect_url = '../dashboard_user/CreateCrueltyReport.php';


// Validate redirect param if present
if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
    $redirect_url = $_GET['redirect'];
    // Security: allow only internal redirects (starting with '/')
    if (strpos($redirect_url, '/') !== 0) {
        $redirect_url = '/';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['Email'] ?? '');
    $password = trim($_POST['Password'] ?? '');

    if ($email && $password) {
        $sql = "SELECT * FROM user WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['Password'])) {
                $_SESSION['userID'] = $row['userID'];
                $_SESSION['Role'] = $row['Role'];
                $_SESSION['Fname'] = $row['Fname'];
                $_SESSION['Sname'] = $row['Sname'];
                $_SESSION['DateOfBirth'] = $row['DateOfBirth'];
                $_SESSION['CellNumber'] = $row['CellNumber'];
                $_SESSION['Address'] = $row['Address'];

                // Handle adopt redirect first (highest priority)
                $adopt_redirect = $_GET['adopt_redirect'] ?? $_POST['adopt_redirect'] ?? null;
                $animal_id = $_GET['animal_id'] ?? $_POST['animal_id'] ?? null;
                if ($adopt_redirect == '1' && $animal_id) {
                    header("Location: ../dashboard_user/adoptionApplication.php?animal_id=" . urlencode($animal_id));
                    exit;
                }

                // NEW: Handle volunteer redirect
                $volunteer_redirect = $_GET['volunteer_redirect'] ?? $_POST['volunteer_redirect'] ?? null;
                if ($volunteer_redirect == '1') {
                    header("Location: ../dashboard_user/volunteer_application.php");
                    exit;
                }

                $role = strtolower($row['Role']);

                // Redirect regular users back to previous page if redirect_url set
              

                // Admin and Vet dashboards
                if ($role === 'administrator') {
                    header("Location: ../dashboard_admin/dashboard_admin.php");
                } elseif ($role === 'veterinarian') {
                    header("Location: ../dashboard_vet/dashboard_vet.php");
                } elseif (in_array($role, ['volunteer', 'fosterer', 'user'])) {
                    // Regular user dashboard fallback if no redirect_url
                    header("Location: ../dashboard_user/CreateCrueltyReport.php");
                } else {
                    header("Location: ../error.php?msg=unknown_role");
                }
                exit;

            } else {
                $error_message = "❌ Invalid password.";
            }
        } else {
            $error_message = "❌ No account found with that email.";
        }
    } else {
        $error_message = "⚠️ Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Login</title>
    <link rel="stylesheet" href="userLogin.css" />
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
</head>
<body>
<div class="auth-container">
    <h1>Login</h1>

    <?php if (!empty($error_message)) : ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <!-- FIXED: Make sure form submits to the correct file -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']); ?>">
        <label>Email</label>
        <input type="email" name="Email" required />

        <label>Password</label>
        <input type="password" name="Password" required />

        <!-- FIXED: Preserve redirect parameters as hidden inputs -->
        <?php if (isset($_GET['volunteer_redirect'])): ?>
            <input type="hidden" name="volunteer_redirect" value="<?= htmlspecialchars($_GET['volunteer_redirect']) ?>" />
        <?php endif; ?>
        
        <?php if (isset($_GET['adopt_redirect'])): ?>
            <input type="hidden" name="adopt_redirect" value="<?= htmlspecialchars($_GET['adopt_redirect']) ?>" />
        <?php endif; ?>
        
        <?php if (isset($_GET['animal_id'])): ?>
            <input type="hidden" name="animal_id" value="<?= htmlspecialchars($_GET['animal_id']) ?>" />
        <?php endif; ?>

        <button type="submit">Login</button>
    </form>

    <p class="switch">
        New user? <a href="userRegisterC.php<?php
            // FIXED: Preserve all redirect parameters
            $params = [];
            if (isset($_GET['adopt_redirect']) && isset($_GET['animal_id'])) {
                $params[] = 'adopt_redirect=1&animal_id=' . urlencode($_GET['animal_id']);
            }
            if (isset($_GET['volunteer_redirect'])) {
                $params[] = 'volunteer_redirect=1';
            }
            echo !empty($params) ? '?' . implode('&', $params) : '';
        ?>">Register here</a>
    </p>
</div>
</body>
</html>