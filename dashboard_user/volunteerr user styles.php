<?php
include("../databaseConnection.php");

// --- The new user application logic remains the same ---
if (isset($_POST['submit_volunteer_application'])) {
    $fname = $_POST['fname'];
    $sname = $_POST['sname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $cellNumber = $_POST['cellNumber'];
    $address = $_POST['address'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql_insert_user = "INSERT INTO user (fname, sname, Email, Password, DateOfBirth, CellNumber, Address, Role, isDeleted)
                        VALUES ('$fname', '$sname', '$email', '$hashedPassword', '$dateOfBirth', '$cellNumber', '$address', 'Volunteer', 0)";

    if ($conn->query($sql_insert_user) === TRUE) {
        $newUserID = $conn->insert_id;
        $sql_insert_app = "INSERT INTO application (userID, animalID, applicationStatus, applicationType, isDeleted)
                           VALUES ('$newUserID', 0, 'Pending', 'Volunteer', 0)";

        if ($conn->query($sql_insert_app) === TRUE) {
            echo "<script>alert('Thank you! Your volunteer application has been submitted successfully.');</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        if ($conn->errno == 1062) {
            echo "<script>alert('Error: An account with this email address already exists.');</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}


// --- UPDATED: Logic to find user by EMAIL for the dashboard ---

$user_found = false;
$error_message = '';
$user_applications = null;
$user_tasks = null;

// Check if an email was submitted to view the dashboard
if (isset($_GET['email']) && !empty($_GET['email'])) {
    $email_safe = $conn->real_escape_string($_GET['email']);
    
    // 1. Find the userID associated with the provided email
    $user_lookup_sql = "SELECT userID FROM user WHERE Email = '$email_safe' LIMIT 1";
    $result = $conn->query($user_lookup_sql);

    if ($result && $result->num_rows > 0) {
        $user_found = true;
        $user_row = $result->fetch_assoc();
        $current_user_id = $user_row['userID'];

        // 2. Fetch applications using the found userID
        $user_applications_sql = "SELECT app.applicationStatus, app.applicationDate, usr.fname, usr.sname 
                                  FROM application AS app
                                  JOIN user AS usr ON app.userID = usr.userID
                                  WHERE app.userID = '$current_user_id' 
                                  AND app.applicationType = 'Volunteer' 
                                  AND app.isDeleted = 0";
        $user_applications = $conn->query($user_applications_sql);

        // 3. Fetch tasks using the found userID
        $user_tasks = $conn->query("SELECT at.*, a.name as animal_name, a.species FROM AssignedTask at 
                                   LEFT JOIN Animal a ON at.animalID = a.animalID 
                                   WHERE at.userID = '$current_user_id'");
    } else {
        // Handle case where email is not found
        $error_message = "No user found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <title>SPCA - Volunteer With Us</title>
    <link rel="stylesheet" href="../dashboard_admin/styles.css">
    <style>
        .back-button {
            display: inline-block; padding: 8px 15px; margin-bottom: 15px; background-color: #f0ad4e;
            color: white; text-decoration: none; border-radius: 4px; border: 1px solid #eea236;
        }
        .back-button:hover { background-color: #ec971f; }
        .error-message { color: red; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    
    <h1>SPCA Volunteer Program</h1>
    
    <h2>Make a Difference - Volunteer Today!</h2>
    <p>Join our amazing team of volunteers and help us care for animals in need. Whether you can spare a few hours a week or more, every contribution makes a difference!</p>
    
    <h3>Volunteer Opportunities Include:</h3>
    <ul>
        <li>Animal Care - Feeding, cleaning, and socializing animals</li>
        <li>Dog Walking - Help our dogs get exercise and fresh air</li>
        <li>Cat Socialization - Spend time with cats to help them become more adoptable</li>
        <li>Administrative Support - Help with paperwork and organization</li>
        <li>Event Assistance - Help at adoption events and fundraisers</li>
        <li>Transport - Help transport animals to vet appointments</li>
    </ul>
    
    <h2>Volunteer Application Form</h2>
    <p>Please fill out the form below to apply to become a volunteer:</p>
    
    <form method="POST">
        <table border="1" width="60%">
            <tr>
                <td><strong>Personal Information</strong></td>
                <td></td>
            </tr>
            <tr>
                <td>First Name:</td>
                <td><input type="text" name="fname" required></td>
            </tr>
            <tr>
                <td>Last Name:</td>
                <td><input type="text" name="sname" required></td>
            </tr>
            <tr>
                <td>Email Address:</td>
                <td><input type="email" name="email" required></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td>Date of Birth:</td>
                <td><input type="date" name="dateOfBirth" required></td>
            </tr>
            <tr>
                <td>Cell Number:</td>
                <td><input type="text" name="cellNumber" placeholder="e.g., 0821234567" required></td>
            </tr>
            <tr>
                <td>Home Address:</td>
                <td><textarea name="address" rows="3" cols="30" required></textarea></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" required> I confirm that I am over 18 years old and agree to undergo a background check if required.
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" required> I understand that volunteering requires a commitment and I will notify SPCA if I can no longer volunteer.
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="submit_volunteer_application" value="Submit Volunteer Application"></td>
            </tr>
        </table>
    </form>
    
    <hr>
    
    <?php if ($user_found): ?>
    <h2>My Volunteer Dashboard</h2>
    <a href="volunteerr user styles.php" class="back-button">⬅️ Back to Main Page</a>
    <p>Welcome back! Here's your volunteer information:</p>
    <h3>My Applications</h3>
    <table border="1" width="80%">
        <tr>
            <th>Applicant Name</th>
            <th>Status</th>
            <th>Application Date</th>
        </tr>
        <?php
        if ($user_applications && $user_applications->num_rows > 0) {
            while($app = $user_applications->fetch_assoc()) {
                $applicationDate = date("d F Y, g:i a", strtotime($app['applicationDate']));
                echo "<tr>";
                echo "<td>".$app['fname']." ".$app['sname']."</td>";
                echo "<td><strong>".$app['applicationStatus']."</strong></td>";
                echo "<td>".$applicationDate."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No applications found for this User ID.</td></tr>";
        }
        ?>
    </table>
    <br>
    <h3>My Assigned Tasks</h3>
    <table border="1" width="80%">
        <tr>
            <th>Task</th>
            <th>Animal</th>
            <th>Status</th>
            <th>Volunteers Needed</th>
        </tr>
        <?php
        if ($user_tasks && $user_tasks->num_rows > 0) {
            while($task = $user_tasks->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$task['taskName']."</td>";
                echo "<td>".$task['animal_name']." (".$task['species'].")</td>";
                echo "<td>".($task['completionStatus'] ? 'Completed' : 'In Progress')."</td>";
                echo "<td>".$task['totalVolunteers']."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No tasks assigned yet. Check back soon!</td></tr>";
        }
        ?>
    </table>

    <?php else: ?>
    <hr>
    <h3>Existing Volunteer?</h3>
    <p>If you are already a registered volunteer, enter your email address below to view your dashboard.</p>
    
    <form action="" method="GET" style="margin-top: 10px;">
        <label for="email_login"><b>Email Address:</b></label>
        <input type="email" id="email_login" name="email" placeholder="Enter your email" required>
        <input type="submit" value="View My Dashboard">
    </form>
    
    <?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <?php endif; ?>
    
    <hr>
    <h3>Contact Us</h3>
    <p>Questions about volunteering? Contact us at:</p>
    <p>Email: volunteers@spca.co.za | Phone: (021) 700-4140</p>
    
</body>
</html>

<?php
$conn->close();
?>