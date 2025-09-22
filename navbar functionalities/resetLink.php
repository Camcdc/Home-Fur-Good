<?php
// Check if the token is passed in the URL
if (isset($_GET['token'])) {
    $resetToken = $_GET['token'];

    // For security, you can also validate if the token exists in the database
    require_once "../databaseConnection.php";
    $sql = "SELECT * FROM user WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $resetToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Check if the token has expired
        if (strtotime($row['reset_expires']) > time()) {
            $message = "Click the link below to reset your password:";
            $resetLink = "resetPassword.php?token=" . urlencode($resetToken);
        } else {
            $message = "The reset link has expired. Please request a new one.";
        }
    } else {
        $message = "Invalid reset token.";
    }
} else {
    $message = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Password Reset</title>
    <link rel="stylesheet" href="userLogin.css" />
</head>
<body>
<div class="auth-container">
    <h1>Password Reset</h1>

    <p><?= htmlspecialchars($message) ?></p>

    <?php if (isset($resetLink)) : ?>
        <a href="<?= htmlspecialchars($resetLink) ?>" class="reset-link">Reset Password</a>
    <?php endif; ?>
</div>
</body>
</html>
