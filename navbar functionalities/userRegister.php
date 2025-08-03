<DOCTYPE html>
<html lang= "en">
<head>
        <title>Register</title>
    
</head>
<body>
    <?php 
    require_once "../databaseConnection.php";

    $firstname = $_POST['Fname'];
    $surname = $_POST['Sname'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $dateofbirth = $_POST['DateOfBirth'];
    $cellnumber = $_POST['CellNumber'];
    $address = $_POST['Address'];
    $role = $_POST['Role'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    //check if the email exists already
  /*  $stmt = $conn->prepare("SELECT userID FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result(); //checks how many rows were found

    if($stmt->num_rows>0){
        echo ""

    }*/

    $sql ="INSERT INTO user(Fname, Sname, Email, Password, DateOfBirth, CellNumber, Address, Role)
           VALUES ('$firstname', '$surname', '$email', '$password', '$dateofbirth', '$cellnumber', '$address', '$role')";
    

  /*if ($conn->query($sql)===TRUE){
        echo "Successfully registered! <a href='navbar.html'>Login here</a>";

 }*/

if ($conn->query($sql) === TRUE) {
       
        header("Location: navbar.php?register_success=1");
        exit;
    } else {
        echo "Unsuccessful: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
   
    
    ?>