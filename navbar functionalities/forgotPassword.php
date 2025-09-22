<?php
require_once "../databaseConnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resetEmail = trim($_POST['resetEmail'] ?? '');

    if ($resetEmail) {
        // Check if the email exists in the database
        $sql = "SELECT * FROM user WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $resetEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Generate a unique reset token
            $resetToken = bin2hex(random_bytes(32)); // 64-character token
            
            // Set the token expiry (1 hour from now)
            $resetExpires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Update the database with the reset token and expiry
            $updateSql = "UPDATE user SET reset_token = ?, reset_expires = ? WHERE Email = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("sss", $resetToken, $resetExpires, $resetEmail);
            $updateStmt->execute();

            // Now redirect the user to the reset link page
            header("Location: resetLink.php?token=" . urlencode($resetToken));  // Redirect to the page that contains the link
            exit;  // Ensure no further processing
        } else {
            echo "No account found with that email.";
        }
    } else {
        echo "Please enter a valid email address.";
    }
}
?>
