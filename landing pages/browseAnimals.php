<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Animals | Home Fur Good</title>


    <link rel="stylesheet" href="../navbar functionalities/login-register.css">
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <link rel="stylesheet" href="browseAnimals.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>

<body>

<?php include '../navbar functionalities/navbar.php'; ?>
<?php include '../databaseConnection.php'; ?>

<h1>Animals Available for Adoption</h1>

<div class="container">

    <!-- SIDEBAR FOR FILTERS -->
    <div class="sidebar">
    <h2 class="title">Filters</h2>
    <form method="GET" action="" class="filters-form">
        <label for="search">Name or Breed</label>
        <input 
            type="text" 
            name="search" 
            id="search" 
            placeholder="e.g. Luna, Labrador"
            value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"
            class="filter-input"
        > <!--VALUE: if there is a value set for search in the URL, use it/if not, then use ' '-->

        <label for="species-select">Species</label>
        <select name="species-select" id="species-select" class="species-select">
            <option value="">All</option>
            <option value="Dog" <?php if(isset($_GET['species']) && $_GET['species'] == 'Dog') echo 'selected'; ?>>Dog</option>
            <option value="Cat" <?php if(isset($_GET['species']) && $_GET['species'] == 'Cat') echo 'selected'; ?>>Cat</option>
            <option value="Horse" <?php if(isset($_GET['species']) && $_GET['species'] == 'Horse') echo 'selected'; ?>>Horse</option>
            <option value="Donkey" <?php if(isset($_GET['species']) && $_GET['species'] == 'Donkey') echo 'selected'; ?>>Donkey</option>
            <option value="Goat" <?php if(isset($_GET['species']) && $_GET['species'] == 'Goat') echo 'selected'; ?>>Goat</option>
        </select>

        <div class="slide-container">
    <label>Age Range: <span id="age-range-display"></span></label>
    
    <div class="range-slider">
        <input 
            type="range" 
            name="age-min" 
            id="age-min" 
            min="0" 
            max="30" 
            value="<?php echo isset($_GET['age-min']) ? $_GET['age-min'] : 0; ?>" 
            class="thumb thumb-left"
        >
        <input 
            type="range" 
            name="age-max" 
            id="age-max" 
            min="0" 
            max="30" 
            value="<?php echo isset($_GET['age-max']) ? $_GET['age-max'] : 30; ?>" 
            class="thumb thumb-right"
        >
        <div class="slider-track"></div>
        </div>
        </div>


        <label for="sex-select">Gender</label>
        <select name="sex-select" id="sex-select" class="sex-select">
            <option value="">All</option>
            <option value="Male" <?php if(isset($_GET['sex']) && $_GET['sex'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if(isset($_GET['sex']) && $_GET['sex'] == 'Female') echo 'selected'; ?>>Female</option>
            
        </select>

        <button type="submit" class="apply-filters-btn">Apply Filters</button>
    </form>
</div>



    <!-- ANIMAL BLOCKS -->
    <div class="content">
        <?php

        //SEARCH/FILTER STUFF
        $search = "";
        $species = "";
        $minage = 0;
        $maxage = 30;
        $sex = "";

        if (isset($_GET['search'])) {
            $search = $_GET['search'];
        }
        if (isset($_GET['species-select'])) {
            $species = $_GET['species-select'];
        }
        if (isset($_GET['age-min'])) {
            $minage = (int)$_GET['age-min'];
        }
        if (isset($_GET['age-max'])) {
            $maxage = (int)$_GET['age-max'];
        }
        if (isset($_GET['sex-select'])) {
            $sex = $_GET['sex-select'];
        }

        /*if(isset($_GET['species-select'])){ //checks if there is data within the search form
            $species = $_GET['species-select'];
        }*/

        // Escape strings to prevent SQL injection
    $search_safe = $conn->real_escape_string($search);
    $species_safe = $conn->real_escape_string($species);
    $sex_safe = $conn->real_escape_string($sex);

// Build the SQL query with filters applied only if set
$sql = "SELECT * FROM animal WHERE isDeleted = '0' AND status ='Available'";

        if (!empty($search) || !empty($species) || !empty($sex) || $minage !== 0 || $maxage !== 30) {
            if (!empty($search_safe)) {
                $sql .= " AND (name LIKE '%$search_safe%' OR breed LIKE '%$search_safe%')";
            }
            if (!empty($species_safe)) {
                $sql .= " AND species = '$species_safe'";
            }
            if (!empty($sex_safe)) {
                $sql .= " AND sex = '$sex_safe'";
            }
            // Always apply age range filter from slider values
            $sql .= " AND age BETWEEN $minage AND $maxage";

        }else{
            $sql = "SELECT * FROM animal WHERE isDeleted = '0' and status = 'Available'";
        }

        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card'>";

                //ANIMAL IMAGE
                echo "<div class='card-image-container'>";
                if (!empty($row['picture'])) {
                    echo "<img src='../pictures/animals/" . $row['picture'] . "' alt='" . $row['name'] . "' />";
                } else {
                    echo "<img src='images/default.jpg' alt='Animal' />";
                }

                //AVAILABLE TAG
                echo "<span class='badge available'>" . $row['status'] . "</span>";
                echo "</div>";

                // ANIMAL INFO
                echo "<div class='card-info'>";

                echo "<div class='name-and-type'>";
                    echo "<h3>" . $row['name'] . "    <div class='type-tag'>" . $row['species'] . "</div></h3>";
                echo "</div>";

                echo "<p class='breed'>" . $row['breed'] . "</p>";
                    echo "<div class='animal-info'>";
                        echo "<p><strong>Age:</strong> " . $row['age'] . " years</p>";
                        echo "<p><strong>Gender:</strong> " . $row['sex'] . "</p>";
                        echo "<p><strong>Size:</strong> " . $row['size'] . "</p><br>";
                    echo "</div>";
                    
                    echo "<p class='description'>" . substr($row['description'], 0, 75) . "...</p>";
                        
                    echo "<a href='animal1.php?id=" . $row['animalID'] . "' class='details-btn'>View " . $row['name'] . "</a>";

                echo "</div>"; 

                echo "</div>"; 
            }
        } else {
            echo "<p>No animals available at the moment.</p>";
        }

        $conn->close();
        ?>
    </div>

</div>
<?php include 'footer.php'; ?>
</body>

<script>
    const minSlider = document.getElementById("age-min");
    const maxSlider = document.getElementById("age-max");
    const display = document.getElementById("age-range-display");
    const sliderTrack = document.querySelector(".slider-track");
    const maxAllowed = 30;

    function updateSlider() {
        let minVal = parseInt(minSlider.value);
        let maxVal = parseInt(maxSlider.value);

        // Prevent overlap
        if (minVal > maxVal) {
            [minSlider.value, maxSlider.value] = [maxVal, minVal];
            [minVal, maxVal] = [maxVal, minVal];
        }

        display.textContent = `${minVal} - ${maxVal} years`;
        fillSlider(minVal, maxVal);
    }

    function fillSlider(min, max) {
        const percent1 = (min / maxAllowed) * 100;
        const percent2 = (max / maxAllowed) * 100;
        sliderTrack.style.left = percent1 + "%";
        sliderTrack.style.width = (percent2 - percent1) + "%";
    }

    minSlider.addEventListener('input', updateSlider);
    maxSlider.addEventListener('input', updateSlider);

    // Initial setup
    updateSlider();
</script>
</html>
