<?php
session_start();
require_once "../databaseConnection.php";

$redirect_url = '/';

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
                // ✅ Recalculate role based on email rules for safety
                if (stripos($row['Email'], 'rustaff') !== false && stripos($row['Email'], '@spca.co.za') !== false) {
                    $role = "administrator";
                } elseif (stripos($row['Email'], 'ruvet') !== false && stripos($row['Email'], '@spca.co.za') !== false) {
                    $role = "veterinarian";
                } else {
                    $role = "user";
                }

                // Save session data
                $_SESSION['userID']      = $row['userID'];
                $_SESSION['Role']        = ucfirst($role); // keep same format as register
                $_SESSION['Fname']       = $row['Fname'];
                $_SESSION['Sname']       = $row['Sname'];
                $_SESSION['DateOfBirth'] = $row['DateOfBirth'];
                $_SESSION['CellNumber']  = $row['CellNumber'];
                $_SESSION['Address']     = $row['Address'];

                // Handle adopt and cruelty redirect first (highest priority)
                $adopt_redirect = $_GET['adopt_redirect'] ?? $_POST['adopt_redirect'] ?? null;
                $animal_id      = $_GET['animal_id'] ?? $_POST['animal_id'] ?? null;
                $cruelty_redirect = $_GET['cruelty_redirect'] ?? $_POST['cruelty_redirect'] ?? null;
                
                if ($adopt_redirect == '1' && $animal_id) {
                    header("Location: ../dashboard_user/adoptionApplication.php?animal_id=" . urlencode($animal_id));
                    exit;
                } elseif ($cruelty_redirect == '1') {
                    header("Location: ../dashboard_user/CreateCrueltyReport.php");
                    exit;
                }

                // Redirect based on role
                if ($role === 'administrator') {
                    header("Location: ../dashboard_admin/dashboard_admin.php");
                } elseif ($role === 'veterinarian') {
                    header("Location: ../dashboard_vet/dashboard_vet.php");
                } elseif ($role === 'user') {
                    if ($redirect_url !== '/') {
                        header("Location: " . $redirect_url);
                    } else {
                        header("Location: ../landing pages/homepage.php");
                    }
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
        $error_message = "⚠ Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Login</title>
    <link rel="stylesheet" href="userLogin.css" />
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>

    <div class="body-container">
    <div class="img-container">
    <img src="../pictures/logo/Logo.jpg" alt="">
    </div>
<div class="auth-container">
    <h1>Login</h1>

    <?php if (!empty($error_message)) : ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Email</label>
        <input type="email" name="Email" required />

        <label>Password</label>
        <input type="password" name="Password" required />
        
        <a href="javascript:void(0);" id="forgotPasswordLink">Forgot Password</a>

        <button type="submit">Login</button>
    </form>
    </div>

    <p class="switch">
        New user? 
        <a href="userRegisterC.php<?php
            // Cruelty redirect has priority
            if (isset($_GET['cruelty_redirect']) && $_GET['cruelty_redirect'] === '1') {
                echo '?cruelty_redirect=1';
            } 
            // Otherwise, adoption redirect
            elseif (isset($_GET['adopt_redirect']) && isset($_GET['animal_id'])) {
                echo '?adopt_redirect=1&animal_id=' . urlencode($_GET['animal_id']);
            }
        ?>">Register here</a>
    </p>
</div>

<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Forgot Password</h3>
        <form id="forgotPasswordForm">
            <label for="resetEmail">Enter your email:</label>
            <input type="email" name="resetEmail" id="resetEmail" required />
            <button type="submit">Send Reset Link</button>
        </form>
        <div id="modalMessage"></div>
    </div>
</div>

<script>
    var modal = document.getElementById("forgotPasswordModal");
    var btn = document.getElementById("forgotPasswordLink");
    var span = document.getElementsByClassName("close")[0];

    // Open the modal when the link is clicked
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // Close the modal when the close button is clicked
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal if the user clicks anywhere outside the modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Handle Forgot Password form submission (AJAX)
    document.getElementById('forgotPasswordForm').onsubmit = function(event) {
        event.preventDefault();  // Prevent form submission

        var email = document.getElementById('resetEmail').value;

        if (email) {
            // Send AJAX request to forgotPassword.php
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "forgotPassword.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('modalMessage').innerHTML = xhr.responseText;
                }
            };
            xhr.send("resetEmail=" + encodeURIComponent(email));
        } else {
            document.getElementById('modalMessage').innerHTML = "Please enter a valid email address.";
        }
    }
</script>
</body>
</html>
