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

    $selectAnimals = "SELECT * FROM animal WHERE animalID = $animalID";
    $result = $conn->query($selectAnimals);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        die("Animal not found.");
    }
    


    if(isset($_POST['update'])){

        include_once 'occupancyUpdate.php';

        $name = $_POST['name'];
        $newkennelID = $_POST['kennelID'];
        $species = $_POST['species'];
        $breed = $_POST['breed'];
        $age = $_POST['age'];
        $sex = $_POST['sex'];
        $colour = $_POST['colour'];
        $size = $_POST['size'];
        $picture = $row['picture'];

            if (!empty($_FILES['picture']['name'])) {
            $fileName = $_FILES['picture']['name'];
            $tempPath = $_FILES['picture']['tmp_name'];
            $uploadPath = '../pictures/animals/' . $fileName;

            move_uploaded_file($tempPath, $uploadPath);

            $picture = $fileName;
            }

        $description = $_POST['description'];


        $oldKennelID = $row['kennelID']; //kennel before update
        $newKennelID = $_POST['kennelID']; //from db

        if ($oldKennelID != $newKennelID) {
            

            //Decrease old kennel
            decreaseKennelOccupancy($conn, $oldKennelID);

            //Check new kennel capacity before incrementing
            $check = $conn->prepare("SELECT occupation, capacity FROM kennel WHERE kennelID = ?");
            $check->bind_param("i", $newKennelID);
            $check->execute();
            $result = $check->get_result();
            $rowCheck = $result->fetch_assoc();

            if ($rowCheck['occupation'] < $rowCheck['capacity']) {
                //Increase new kennel
                updateKennelOccupancy($conn, $newKennelID);
            } else{
                exit();
            }
        }


        $sql_update = "UPDATE animal SET 
        name = '$name',
        kennelID = '$newkennelID',
        species = '$species',
        age = '$age',
        sex = '$sex',
        colour = '$colour',
        size = '$size',
        picture = '$picture',
        description = '$description' WHERE animalID = '$animalID'";

        $query_execute = $conn->query($sql_update);

        if($query_execute){
            header("Location:viewAllAnimals.php");
            $conn->close();
        } else {
            echo "<script>alert('Record could update failed. Retry')</script>";
            header("Location:viewAllAnimals.php");
        }
    }   
?>

    <div class="form_container">
    <div class="form_heading">
        <h1>Update Record</h1>
        <h3>Update animal's record</h3>
    </div>
    <div class="form-wrap">
    <form action="" enctype="multipart/form-data" method="POST">

        <div class="form_row">
            <div class="form_group">
                <label for="name">Name:</label>
                <input type="text" id='name' name='name' value="<?php echo htmlspecialchars($row['name']); ?>">
            </div>

            <div class="form_group">
                <label for="kennelID">Kennel:</label>
                <select name="kennelID" id="kennelID">
                    <option value="" disabled selected>Select animal kennel</option>
                    <?php
                        $sql1 = "SELECT kennelID, occupation, capacity FROM kennel ORDER BY kennelID";
                        $kennelResult = $conn->query($sql1);

                        while($kennelRow = $kennelResult->fetch_assoc()){
                            $kennelID = $kennelRow['kennelID'];
                            $occupation = $kennelRow['occupation'];
                            $capacity = $kennelRow['capacity'];
                            $isFull = $occupation >= $capacity;

                            $label = "Kennel $kennelID (" . ($isFull ? "Full" : "$occupation/$capacity") . ")";
                            $selected = ($kennelID == $row['kennelID']) ? "selected" : "";

                            echo "<option value='$kennelID' $selected" . ($isFull ? " disabled" : "") . ">$label</option>";
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="form_row"> 
            <div class="form_group">       
                <label for="species">Species:</label>
                <div class="radio-group">
                    <label for="dog">Dog:<input type="radio" id='dog' name='species' value='Dog' <?php if ($row['species'] == 'Dog') echo 'checked'; ?>></label>
                    <label for="cat">Cat:<input type="radio" id='cat' name='species' value='Cat' <?php if ($row['species'] == 'Cat') echo 'checked'; ?>></label>
                </div>
            </div>

            <div class="form_group">                
                <label for="breed">Breed:</label>
                <input type="text" id='breed' name='breed' value="<?php echo htmlspecialchars($row['breed']); ?>">
            </div>
        </div>

        <div class="form_row">
            <div class="form_group">
                <label for="age">Age:</label>
                <input type="number" id='age' name='age' value="<?php echo $row['age']; ?>">
            </div>

            <div class="form_group">
                <label for="sex">Gender:</label>
                <div class="radio-group">
                    <label for="male">Male:<input type="radio" id='male' name='sex' value='Male' <?php if ($row['sex'] == 'Male') echo 'checked'; ?>></label>
                    <label for="female">Female:<input type="radio" id='female' name='sex' value='Female' <?php if ($row['sex'] == 'Female') echo 'checked'; ?>></label>
                </div>            
            </div>
        </div>

        <div class="form_row">
            <div class="form_group">
                <label for="colour">Colour:</label>
                <input type="text" id='colour' name='colour' value="<?php echo htmlspecialchars($row['colour']); ?>">
            </div>

            <div class="form_group">
                <label for="size">Size:</label>
                <select name="size" id="size" required >
                    <option value="" disabled>Select animal size</option>
                    <option value="Small" <?php if ($row['size'] == 'Small') echo 'selected'; ?>>Small</option>
                    <option value="Medium" <?php if ($row['size'] == 'Medium') echo 'selected'; ?>>Medium</option>
                    <option value="Large" <?php if ($row['size'] == 'Large') echo 'selected'; ?>>Large</option>
                </select>
            </div>
        </div>

        <div class='form_group full_span'>
            <label for="picture">Picture of Animal:</label>
            <input type="file" name="picture" id="picture" value="<?php echo $row['picture'];?>">
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
</div>

</body>
</html>