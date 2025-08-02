<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="registerAnimal.css">
    <link rel="stylesheet" href="navbar functionalities/navbar.css">
    <link rel="stylesheet" href="navbar functionalities/login-register.css">
    <title>Document</title>
</head>

<header>

</header>

<body>
<?php include 'navbar functionalities/navbar.php'; 
    include 'databaseConnection.php';
?>

<div class="form_container">
    <div class="form_heading">
        <h1>Register New Animal</h1>
        <h3>Add a new animal to the adoption system</h3>
    </div>
    <div class="form-wrap">
    <form action="registerAnimal.php" enctype="multipart/form-data" method="POST">

        <div class="form_row">
            <div class="form_group">
                <label for="animalID">Animal ID:</label>
                <input type="text" id='animalID' name='animalID' required>
            </div>

            <div class="form_group">
                <label for="kennelID">Kennel ID:</label>
                <select name="kennelID" id="kennelID">
                    <option value="" disabled selected>Select animal kennel</option>
                    
                    <?php
                    
                    $sql1 = "SELECT kennelID, occupation, capacity FROM kennel ORDER BY kennelID";
                    $result = $conn->query($sql1);

                    while($row = $result->fetch_assoc()){
                        $kennelID = $row['kennelID'];
                        $occupation = $row['occupation'];
                        $capacity = $row['capacity'];
                        $isFull = $occupation >= $capacity;
                        
                        $kennelNumber = intval(substr($kennelID,1)); //extracts 'K00001' into 1
                        $label = "Kennel $kennelNumber (" . ($isFull ? "Full" : "$occupation/$capacity") . ")";

                        echo "<option value='$kennelID'" . ($isFull ? " disabled" : "") . ">$label</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form_row">
            <div class="form_group">
                <label for="name">Status ID:</label>
                <input type="text" id='statusID' name='statusID' required>
            </div>

            <div class="form_group">
                <label for="name">Name:</label>
                <input type="text" id='name' name='name' required>
            </div>
        </div>

        <div class="form_row"> 
            <div class="form_group">       
                <label for="species">Species:</label>
                    <div class="radio-group">
                        <label for="dog">Dog:<input type="radio" id='dog' name='species' value='Dog'></label>
                        <label for ="cat">Cat:<input type="radio" id='cat' name='species' value='Cat'></label>
                    </div>
            </div>

            <div class="form_group">                
                <label for="breed">Breed:</label>
                <input type="text" id='breed' name='breed' placeholder = 'Golden Retriever, Pug, German Shepard, etc...' required>
            </div>
        </div>

        <div class="form_row">
            <div class="form_group">
                <label for="age">Age:</label>
                <input type="number" id='age' name='age' placeholder="In years" required>
            </div>

            <div class="form_group">
                <label for="sex">Gender:</label>
                <div class="radio-group">
                    <label for="male">Male:<input type="radio" id='male' name='sex' value='Male'></label>
                    <label for="female">Female:<input type="radio" id='female' name='sex' value='Female'></label>
                </div>            
            </div>
        </div>
        
        <div class="form_row">
            <div class="form_group">
                <label for="colour">Colour:</label>
                <input type="text" id='colour' name='colour' placeholder ='Black, Brown, White, etc...' required>
            </div>

            <div class="form_group">
                <label for="size">Size:</label>
                <select name="size" id="size" required >
                    <option value="" disabled selected>Select animal size</option>
                    <option value="Small">Small</option>
                    <option value="Medium">Medium</option>
                    <option value="Large">Large</option>
                </select>
            </div>
        </div>
        
        <div class='form_group full_span'>
            <label for="picture">Picture of Animal:</label>
            <input type="file" name="picture" id="picture" required>
        </div>
        
        <div class="form_group full_span">
            <label for="description">Description:</label>
            <textarea id="description" name="description" placeholder="Please input any relevant details about the animal..."></textarea>
        </div>

        <div class="submit-button full_span">
        <input type="submit" value="Register Animal">
        </div>

    </form>
    </div>
</div>


    <!--Modal for successful registration-->
    <div id="registerSuccessModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p>Animal has successfully been registered.</p>
        <button type="submit" onclick="closeRegisterModal()">Ok</button>
        </form>
    </div>
    </div>

    <!--Modal for failed registration-->
        <div id="registerFailureModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p>Animal already exists</p>
        <button onclick="closeRegisterModal()">Ok</button>
        </form>
    </div>
    </div>


<script>

    //function to open modal
    function openSuccessModal() { 
    document.getElementById('registerSuccessModal').style.display = 'block';
    }

    function openFailureModal() { 
    document.getElementById('registerFailureModal').style.display = 'block';
    }

    //function to close modal
    function closeRegisterModal() { 
    document.getElementById('registerSuccessModal').style.display = 'none';
    document.getElementById('registerFailureModal').style.display = 'none';;
    }


  window.onclick = function(event) {
    const loginModal = document.getElementById('loginModal');
    const successModal = document.getElementById('registerSuccessModal');
    const failureModal = document.getElementById('registerFailureModal');

        if (event.target === loginModal) {
            loginModal.style.display = 'none';
        }
        if (event.target === successModal) {
            successModal.style.display = 'none';
        }
        if (event.target === failureModal) {
            failureModal.style.display = 'none';
  }
}


    //SUCCESS AND FAILURE MODALS
    window.onload = function () {
        const urlParams = new URLSearchParams(window.location.search);//evaluates error/success from URL (coming from registerAnimal.php)
        if (urlParams.get('success') === '1') { //if success is in the url
            openSuccessModal(); //successful registration modal opens
        
            if (window.history.replaceState) { // Clear query parameters so modal doesn't reopen on refresh
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        
        }
        if (urlParams.get('error') === '1') { //if error is in the url
            openFailureModal(); //errornous registration occurs
        }
    };
</script>


</body>
</html>