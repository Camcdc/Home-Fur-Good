<?php 
include 'databaseconnection.php';

// Safely get the userID
$userID = $_GET['userID'] ?? null;

if (!$userID) {
    die("❌ No userID provided in the URL");
}

$sql_select = "SELECT * FROM user WHERE userID = ?";
$stmt = $conn->prepare($sql_select);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$editData = $result->fetch_assoc();

echo "UserID Number = ". htmlspecialchars($userID) ." extracted from the URL using GET<br>";
echo "UserID Number = ". htmlspecialchars($editData['userID']) ." extracted from database using query<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crueltyReportID = $_GET['crueltyReportID'] ?? null;
    $capacity = $_POST['capacity'] ?? '';
    $status = $_POST['status'] ?? '';
    $picture = $_POST['picture'] ?? '';

    if (!$crueltyReportID) {
        die("❌ No crueltyReportID provided in the URL");
    }

    // Use empty() to check for missing/empty crueltyReportID
    if (empty($crueltyReportID)) {
        $sql = 'INSERT INTO assigninspector (userID, crueltyReportID, capacity, status, picture) VALUES (?, ?, ?, ?, ?)';
        $query_execute = $conn->prepare($sql);

        if ($query_execute === false) {
            die("❌ Inspector could not be assigned: " . htmlspecialchars($conn->error));
        }

        $query_execute->bind_param("iisss", $userID, $crueltyReportID, $capacity, $status, $picture);

    } else {
        $sql = 'UPDATE assigninspector SET userID=?, capacity=?, status=?, picture=? WHERE crueltyReportID=?';
        $query_execute = $conn->prepare($sql);

        if ($query_execute === false) {
            die("❌ Failed to prepare UPDATE statement: " . htmlspecialchars($conn->error));
        }

        // Correct parameter order for UPDATE
        $query_execute->bind_param("isssi", $userID, $capacity, $status, $picture, $crueltyReportID);
    }

    $execute_success = $query_execute->execute();

    if ($execute_success) {
        echo "<script>alert('✅ Inspector assigned successfully')</script>";
        echo "<script>window.location.href='displayAllrecords.php'</script>";
    } else {
        echo "❌ Execution failed: " . htmlspecialchars($query_execute->error);
    }
    exit();
}
?>


