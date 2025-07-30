<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="registerAnimal.css">
    <title>Document</title>
</head>

<header>
    <?php include 'navbar.php'; ?>
</header>

<body>

<div class="form_container">
    <h1>Register New Animal</h1>

    <div class="form-wrap">
    <form action="registerAnimal.php" method="POST">

        <div class="form_row">
            <div class="form_group">
                <label for="animalID">Animal ID:</label>
                <input type="text" id='animalID' name='animalID' required>
            </div>

            <div class="form_group">
                <label for="kennelID">Kennel ID:</label>
                <input type="text" id='kennelID' name='kennelID' required>
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
                <input type="text" id='breed' name='breed' required>
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
                <input type="text" id='colour' name='colour' required>
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

        <div class="form_group full_sspan">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Please input any relevant details about the animal..."></textarea>
  </    div>

        <div class="submit-button full_span">
        <input type="submit" value="Register Animal">
        </div>

    </form>
    </div>
</div>


</body>
</html>