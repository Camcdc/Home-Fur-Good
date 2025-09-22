<?php

ob_start(); // Start output buffering
 session_start();
    if (!isset($_SESSION['userID']) || $_SESSION['Role'] !== 'Administrator') {
        // Redirect to homepage if not logged in or not an admin
        header("Location: ../landing pages/homepage.php");
        exit;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="animalUpdate.css"> 
    <link rel="stylesheet" href="sidebar_admin.css">
</head>
<body>


<?php
include 'sidebar_admin.php';
require '../databaseConnection.php';

$animalID = $_REQUEST['animalID'];

$selectAnimals = "SELECT * FROM animal WHERE animalID = ?";
$stmt = $conn->prepare($selectAnimals);
$stmt->bind_param("i", $animalID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Animal not found.");
}

if (isset($_POST['update'])) {
    include_once 'occupancyUpdate.php';

    $name = $_POST['name'];
    $newkennelID = $_POST['kennelID'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $colour = $_POST['colour'];
    $size = $_POST['size'];
    $rescueCircumstance = $_POST['rescueCircumstance'];
    $healthStatus = $_POST['healthStatus'];
    $picture = $row['picture'];

    if (!empty($_FILES['picture']['name'])) {
        $fileName = basename($_FILES['picture']['name']);
        $tempPath = $_FILES['picture']['tmp_name'];
        $uploadPath = '../pictures/animals/' . $fileName;

        if (move_uploaded_file($tempPath, $uploadPath)) {
            $picture = $fileName;
        }
    }

    $description = $_POST['description'];

    $oldKennelID = $row['kennelID'];

    if ($oldKennelID != $newkennelID) {
        decreaseKennelOccupancy($conn, $oldKennelID);

        $check = $conn->prepare("SELECT occupation, capacity FROM kennel WHERE kennelID = ?");
        $check->bind_param("i", $newkennelID);
        $check->execute();
        $result = $check->get_result();
        $rowCheck = $result->fetch_assoc();

        if ($rowCheck['occupation'] < $rowCheck['capacity']) {
            updateKennelOccupancy($conn, $newkennelID);
        } else {
            exit();
        }
    }

    $sql_update = $conn->prepare("UPDATE animal SET 
        name = ?, kennelID = ?, species = ?, breed = ?, age = ?, sex = ?, colour = ?, size = ?, picture = ?, description = ?, rescueCircumstance = ?, healthStatus = ? 
        WHERE animalID = ?");

    $sql_update->bind_param(
    "sississsssssi",
    $name, $newkennelID, $species, $breed, $age, $sex, $colour, $size, $picture, $description, $rescueCircumstance, $healthStatus, $animalID
    );



    if ($sql_update->execute()) {
        header("Location: viewAllAnimals.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<div class="form_container">
    <div class="form_heading">
        <a href="viewAllAnimals.php" class="back-link-top">← Back to all Animals</a>
        <h1>Update Animal Record</h1>
        <p>Edit details and picture</p>
    </div>

    <form action="" enctype="multipart/form-data" method="POST">

        <div class="form_row">
            <div class="form_group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
            </div>

                <div class="form_group">
                <label for="kennelID">Kennel:</label>
                <select name="kennelID" id="kennelID" required>
    <option value="" disabled>Select animal kennel</option>

    <?php
    $currentType = ""; // Initialize for optgroup grouping
    
    $sql1 = "SELECT kennelID, occupation, capacity, kennelType FROM kennel ORDER BY kennelID";
    $result = $conn->query($sql1);

    while($rowkennel = $result->fetch_assoc()){
        $kennelID = $rowkennel['kennelID'];
        $occupation = $rowkennel['occupation'];
        $capacity = $rowkennel['capacity'];
        $kennelType = $rowkennel['kennelType'];
        $isFull = $occupation >= $capacity;
        
        $kennelNumber = intval($kennelID);
        $label = "Kennel $kennelNumber (" . ($isFull ? "Full" : "$occupation/$capacity") . ")";

        // Group kennels by type using <optgroup>
        if ($kennelType !== $currentType) {
            if ($currentType !== "") {
                echo "</optgroup>";
            }
            echo "<optgroup label='{$kennelType} Kennels'>";
            $currentType = $kennelType;
        }

        // Build option tag
        echo "<option value='$kennelID'";
        
        if ($kennelID == $row['kennelID']) {
            echo " selected";
        }

        if ($isFull && $kennelID != $row['kennelID']) {
            // Disable option if kennel is full and not currently assigned kennel
            echo " disabled";
        }

        echo ">$label</option>";
    }

    // Close last optgroup if any
    if ($currentType !== "") {
        echo "</optgroup>";
    }
    ?>
</select>
            </div>
        </div>

        <div class="form_row">
            <div class="form_group">
                <label>Species:</label>
                <div class="radio-group">
                    <label for="dog">Dog:<input type="radio" id='dog' name='species' value='Dog' <?php if($row['species'] === 'Dog') echo 'checked'; ?>></label>
                        <label for ="cat">Cat:<input type="radio" id='cat' name='species' value='Cat' <?php if($row['species'] === 'Cat') echo 'checked'; ?>></label>
                        <label for ="horse">Horse:<input type="radio" id='horse' name='species' value='Horse' <?php if($row['species'] === 'Horse') echo 'checked'; ?>></label>
                        <label for ="donkey">Donkey:<input type="radio" id='donkey' name='species' value='Donkey'<?php if($row['species'] === 'Donkey') echo 'checked'; ?>></label>
                        <label for ="goat">Goat:<input type="radio" id='goat' name='species' value='Goat'<?php if($row['species'] === 'Goat') echo 'checked'; ?>></label>
                </div>
            </div>

            <div class="form_group">
                <label for="breed">Breed:</label>
                <input type="text" id="breed" name="breed" value="<?php echo htmlspecialchars($row['breed']); ?>" required>
            </div>
        </div>

        <div class="form_row">
            <div class="form_group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo $row['age']; ?>" required>
            </div>

            <div class="form_group">
                <label>Gender:</label>
                <div class="radio-group">
                    <label><input type="radio" name="sex" value="Male" <?php if ($row['sex'] == 'Male') echo 'checked'; ?>> Male</label>
                    <label><input type="radio" name="sex" value="Female" <?php if ($row['sex'] == 'Female') echo 'checked'; ?>> Female</label>
                </div>
            </div>
        </div>

        <div class="form_row">
            <div class="form_group">
                <label for="colour">Colour:</label>
                <input type="text" id="colour" name="colour" value="<?php echo htmlspecialchars($row['colour']); ?>" required>
            </div>

            <div class="form_group">
                <label for="size">Size:</label>
                <select name="size" id="size" required>
                    <option value="">Select size</option>
                    <option value="Small" <?php if ($row['size'] == 'Small') echo 'selected'; ?>>Small</option>
                    <option value="Medium" <?php if ($row['size'] == 'Medium') echo 'selected'; ?>>Medium</option>
                    <option value="Large" <?php if ($row['size'] == 'Large') echo 'selected'; ?>>Large</option>
                </select>
            </div>
        </div>

        <div class="form_row">
        <div class="form_group">
            <label for="rescueCircumstance">Rescue Circumstance:</label>
            <select id="rescueCircumstance" name="rescueCircumstance" onchange="toggleOtherCircumstance()">
                <option value="" disabled selected>Select circumstance</option>
                <option value="Abandoned">Abandoned</option>
                <option value="Stray">Stray</option>
                <option value="Surrendered">Surrendered</option>
                <option value="Rescued">Rescued</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" id="otherCircumstance" name="otherCircumstance" 
                placeholder="Please specify..." style="display:none; margin-top:8px;">
        </div>

        
        <div class="form_group">
            <label for="healthStatus">Health Status:</label>
            <select id="healthStatus" name="healthStatus">
                <option value="" disabled selected>Select health status</option>
                <option value="Healthy">Healthy</option>
                <option value="Injured">Injured</option>
                <option value="Sick">Sick</option>
            </select>
        </div>
        </div>

        <div class="form_group full_span">
            <label for="picture">Current Picture:</label>
            <?php if (!empty($row['picture'])): ?>
                <div class="animal-image-preview">
                    <img src="../pictures/animals/<?php echo htmlspecialchars($row['picture']); ?>" alt="Animal Image">
                </div>
            <?php else: ?>
                <p>No image available.</p>
            <?php endif; ?>

            <label for="picture">Upload New Picture:</label>
            <input type="file" name="picture" id="picture" accept="image/*">
        </div>

        <div class="form_group full_span">
            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($row['description']); ?></textarea>
        </div>

        <div class="submit-button full_span">
            <input type="submit" value="Update Animal" name="update">
        </div>

    </form>
</div>

</body>
</html>
<?php
ob_end_flush(); // Send output to the browser after all PHP processing is done
?>
