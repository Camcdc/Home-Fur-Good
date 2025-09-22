<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    // Not logged in, redirect to registration/login page
    // You can change 'userRegisterC.php' to your login page if needed
    $animal_id = isset($_POST['animal_id']) ? $_POST['animal_id'] : '';
    // Pass animal_id so user can continue adoption after registering
    header("Location: userRegisterC.php?adopt_redirect=1&animal_id=" . urlencode($animal_id));
    exit;
}

// User is logged in, proceed with adoption processing
$animal_id = isset($_POST['animal_id']) ? $_POST['animal_id'] : '';
$user_id = $_SESSION['userID'];

// Example: Insert adoption request into database
require_once "../databaseConnection.php";

$sql = "INSERT INTO adoption (userID, animalID, adoptionDate) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ii", $user_id, $animal_id); // Both userID and animalID are integers
    if ($stmt->execute()) {
        echo "Adoption request submitted successfully!";
        // Optionally redirect to a confirmation page
         header("Location: adoption_success.php");
        // exit;
    } else {
        echo "Error submitting adoption request.";
    }
    $stmt->close();
} else {
    echo "Database error: " . $conn->error;
}
$conn->close();
?>
