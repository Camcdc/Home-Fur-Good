<?php
session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }
header('Content-Type: application/json');
include '../databaseConnection.php';

// Security check
if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

// Get action from POST data
$action = $_POST['action'] ?? '';

try {
    $conn->begin_transaction();

    switch ($action) {
        case 'approve_application':
            handleApproveApplication($conn);
            break;
            
        case 'reject_application':
            handleRejectApplication($conn);
            break;
            
        case 'reset_application':
            handleResetApplication($conn);
            break;
            
        case 'assign_animal':
            handleAssignAnimal($conn);
            break;
            
        case 'return_animal':
            handleReturnAnimal($conn);
            break;
            
        default:
            throw new Exception('Invalid action specified');
    }
    
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}

// Function to approve application and foster the animal
function handleApproveApplication($conn) {
    $applicationID = filter_input(INPUT_POST, 'applicationID', FILTER_VALIDATE_INT);
    
    if (!$applicationID) {
        throw new Exception('Invalid application ID');
    }
    
    // Get application details
    $stmt = $conn->prepare("
        SELECT a.userID, a.animalID, a.applicationStatus, an.status as animal_status 
        FROM application a 
        JOIN animal an ON a.animalID = an.animalID 
        WHERE a.applicationID = ?");
    $stmt->bind_param("i", $applicationID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$result) {
        throw new Exception('Application not found');
    }
    
    if ($result['applicationStatus'] !== 'Pending') {
        throw new Exception('Application is not pending');
    }
    
    if ($result['animal_status'] !== 'Available') {
        throw new Exception('Animal is no longer available');
    }
    
    $userID = $result['userID'];
    $animalID = $result['animalID'];
    
    // Update application status to Approved
    $stmt = $conn->prepare("UPDATE application SET applicationStatus = 'Approved' WHERE applicationID = ?");
    $stmt->bind_param("i", $applicationID);
    $stmt->execute();
    $stmt->close();
    
    // Update animal status to Fostered
    $stmt = $conn->prepare("UPDATE animal SET status = 'Fostered' WHERE animalID = ?");
    $stmt->bind_param("i", $animalID);
    $stmt->execute();
    $stmt->close();
    
    // Create adoption record for foster placement
    $stmt = $conn->prepare("INSERT INTO adoption (userID, animalID, adoptionDate, status) VALUES (?, ?, NOW(), 'Fostered')");
    $stmt->bind_param("ii", $userID, $animalID);
    $stmt->execute();
    $stmt->close();
}

// Function to reject application
function handleRejectApplication($conn) {
    $applicationID = filter_input(INPUT_POST, 'applicationID', FILTER_VALIDATE_INT);
    
    if (!$applicationID) {
        throw new Exception('Invalid application ID');
    }
    
    // Check if application exists and is pending
    $stmt = $conn->prepare("SELECT applicationStatus FROM application WHERE applicationID = ?");
    $stmt->bind_param("i", $applicationID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$result) {
        throw new Exception('Application not found');
    }
    
    if ($result['applicationStatus'] !== 'Pending') {
        throw new Exception('Application is not pending');
    }
    
    // Update application status to Rejected
    $stmt = $conn->prepare("UPDATE application SET applicationStatus = 'Rejected' WHERE applicationID = ?");
    $stmt->bind_param("i", $applicationID);
    $stmt->execute();
    $stmt->close();
}

// Function to reset application (undo approval/rejection)
function handleResetApplication($conn) {
    $applicationID = filter_input(INPUT_POST, 'applicationID', FILTER_VALIDATE_INT);
    
    if (!$applicationID) {
        throw new Exception('Invalid application ID');
    }
    
    // Get application details
    $stmt = $conn->prepare("
        SELECT a.userID, a.animalID, a.applicationStatus 
        FROM application a 
        WHERE a.applicationID = ?");
    $stmt->bind_param("i", $applicationID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$result) {
        throw new Exception('Application not found');
    }
    
    $userID = $result['userID'];
    $animalID = $result['animalID'];
    
    // If application was approved, we need to undo the foster placement
    if ($result['applicationStatus'] === 'Approved') {
        // Return animal to Available status
        $stmt = $conn->prepare("UPDATE animal SET status = 'Available' WHERE animalID = ?");
        $stmt->bind_param("i", $animalID);
        $stmt->execute();
        $stmt->close();
        
        // Update any active foster records to Returned
        $stmt = $conn->prepare("
            UPDATE adoption 
            SET status = 'Returned' 
            WHERE userID = ? AND animalID = ? AND status = 'Fostered'");
        $stmt->bind_param("ii", $userID, $animalID);
        $stmt->execute();
        $stmt->close();
    }
    
    // Reset application status to Pending
    $stmt = $conn->prepare("UPDATE application SET applicationStatus = 'Pending' WHERE applicationID = ?");
    $stmt->bind_param("i", $applicationID);
    $stmt->execute();
    $stmt->close();
}

// Function to assign additional animal to approved foster
function handleAssignAnimal($conn) {
    $userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);
    $animalID = filter_input(INPUT_POST, 'animalID', FILTER_VALIDATE_INT);
    
    if (!$userID || !$animalID) {
        throw new Exception('Invalid User or Animal ID');
    }
    
    // Check if user has approved foster application
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM application 
        WHERE userID = ? AND applicationType = 'Foster' AND applicationStatus = 'Approved'");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($result['count'] == 0) {
        throw new Exception('User does not have an approved foster application');
    }
    
    // Check if animal is available
    $stmt = $conn->prepare("SELECT status FROM animal WHERE animalID = ?");
    $stmt->bind_param("i", $animalID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$result || $result['status'] !== 'Available') {
        throw new Exception('Animal is not available for foster');
    }
    
    // Update animal status to Fostered
    $stmt = $conn->prepare("UPDATE animal SET status = 'Fostered' WHERE animalID = ?");
    $stmt->bind_param("i", $animalID);
    $stmt->execute();
    $stmt->close();
    
    // Create adoption record for foster placement
    $stmt = $conn->prepare("INSERT INTO adoption (userID, animalID, adoptionDate, status) VALUES (?, ?, NOW(), 'Fostered')");
    $stmt->bind_param("ii", $userID, $animalID);
    $stmt->execute();
    $stmt->close();
}

// Function to return animal from foster
function handleReturnAnimal($conn) {
    $userID = filter_input(INPUT_POST, 'userID', FILTER_VALIDATE_INT);
    $animalID = filter_input(INPUT_POST, 'animalID', FILTER_VALIDATE_INT);
    
    if (!$userID || !$animalID) {
        throw new Exception('Invalid User or Animal ID');
    }
    
    // Check if there's an active foster record
    $stmt = $conn->prepare("
        SELECT adoptionID 
        FROM adoption 
        WHERE userID = ? AND animalID = ? AND status = 'Fostered'");
    $stmt->bind_param("ii", $userID, $animalID);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$result) {
        throw new Exception('No active foster record found');
    }
    
    // Update animal status back to Available
    $stmt = $conn->prepare("UPDATE animal SET status = 'Available' WHERE animalID = ?");
    $stmt->bind_param("i", $animalID);
    $stmt->execute();
    $stmt->close();
    
    // Update adoption record to Returned
    $stmt = $conn->prepare("
        UPDATE adoption 
        SET status = 'Returned' 
        WHERE userID = ? AND animalID = ? AND status = 'Fostered'");
    $stmt->bind_param("ii", $userID, $animalID);
    $stmt->execute();
    $stmt->close();
}