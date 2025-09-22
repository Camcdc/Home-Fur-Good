<?php
require_once('../databaseConnection.php');

if (!isset($_GET['token'])) {
    die("Invalid password reset link.");
}

$token = $_GET['token'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if ($newPassword !== $confirmPassword) {
        $message = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT userID FROM user WHERE reset_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE user SET Password = ?, reset_token = NULL WHERE userID = ?");
            $update->bind_param("si", $hashedPassword, $user['userID']);
            $update->execute();

            $message = "Password reset successfully! You can now login.";
        } else {
            $message = "Invalid or expired token.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="resetPassword.css"> 
</head>
<body>
 

<div class="reset-container">
    <h2>Reset Your Password</h2>
    <div class="message"><?php echo $message; ?></div>
    <form method="post">
        <input type="password" name="newPassword" placeholder="New Password" required>
        <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>
</div>
</body>
</html>
