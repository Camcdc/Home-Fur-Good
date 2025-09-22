<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}
include '../databaseConnection.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_SESSION['userID'];

    // Check for existing foster applications
    $stmt_check_app = $conn->prepare("
        SELECT applicationStatus 
        FROM application 
        WHERE userID = ? AND applicationType = 'Foster' 
        AND (applicationStatus = 'Pending' OR applicationStatus = 'Approved')");
    $stmt_check_app->bind_param("i", $userID);
    $stmt_check_app->execute();
    $stmt_check_app->store_result();

    if ($stmt_check_app->num_rows > 0) {
        $stmt_check_app->bind_result($status);
        $stmt_check_app->fetch();
        $error_message = "You already have a {$status} foster application. Please check your dashboard for updates.";
        $stmt_check_app->close();
    } else {
        $stmt_check_app->close();

        // Sanitize and retrieve form data
        $fosterPreference = trim($_POST['fosterPreference'] ?? '');
        $householdInfo = trim($_POST['householdInfo'] ?? '');
        $homeType = trim($_POST['homeType'] ?? '');
        $hasYard = trim($_POST['hasYard'] ?? '');
        $currentPets = trim($_POST['currentPets'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $fosterDurationDays = filter_input(INPUT_POST, 'fosterDurationDays', FILTER_VALIDATE_INT);
        $animalID = filter_input(INPUT_POST, 'animalID', FILTER_VALIDATE_INT);

        // Validation
        $validation_errors = [];
        if (empty($fosterPreference)) $validation_errors[] = "Please select your foster preference.";
        if (empty($householdInfo)) $validation_errors[] = "Please provide information about your household.";
        if (empty($homeType)) $validation_errors[] = "Please select your home type.";
        if (empty($hasYard)) $validation_errors[] = "Please specify if you have a yard.";
        if (empty($currentPets)) $validation_errors[] = "Please list your current pets or state 'None'.";
        if (!$fosterDurationDays || $fosterDurationDays < 1 || $fosterDurationDays > 7) {
            $validation_errors[] = "Please select a valid foster duration (1-7 days).";
        }
        if (!$animalID || $animalID <= 0) $validation_errors[] = "Please select an animal to foster.";

        if (!empty($validation_errors)) {
            $error_message = "Please fix the following issues:<br>• " . implode("<br>• ", $validation_errors);
        } else {
            // Check if selected animal is still available
            $stmt_animal_check = $conn->prepare("SELECT status FROM animal WHERE animalID = ? AND isDeleted = 0");
            $stmt_animal_check->bind_param("i", $animalID);
            $stmt_animal_check->execute();
            $animal_result = $stmt_animal_check->get_result()->fetch_assoc();
            $stmt_animal_check->close();
            
            if (!$animal_result) {
                $error_message = "Selected animal not found.";
            } elseif ($animal_result['status'] !== 'Available') {
                $error_message = "Selected animal is no longer available for foster.";
            } else {
                // Database Insertion
                try {
                    $conn->begin_transaction();

                    $stmt_app = $conn->prepare("
                        INSERT INTO application (
                            userID, animalID, applicationStatus, applicationType, applicationDate,
                            fosterPreference, householdInfo, homeType, hasYard, currentPets, 
                            experience, fosterDurationDays, createDate
                        ) VALUES (?, ?, 'Pending', 'Foster', NOW(), ?, ?, ?, ?, ?, ?, ?, NOW())");

                    $stmt_app->bind_param("iissssssi", 
                        $userID, $animalID, $fosterPreference, $householdInfo, 
                        $homeType, $hasYard, $currentPets, $experience, $fosterDurationDays);
                    
                    $stmt_app->execute();
                    
                    if ($stmt_app->error) {
                        throw new Exception($stmt_app->error);
                    }
                    
                    $stmt_app->close();
                    $conn->commit();

                    $_SESSION['success_message'] = "Your foster application has been submitted successfully! You will be contacted within 5-7 business days.";
                    header("Location: fosterSuccess.php");
                    exit();

                } catch (Exception $e) {
                    $conn->rollback();
                    $error_message = "An error occurred while submitting your application. Please try again.";
                    error_log("Foster Application Error: " . $e->getMessage());
                }
            }
        }
    }
}

// Get available animals for the dropdown
$available_animals_query = $conn->query("
    SELECT animalID, name, species, breed 
    FROM animal 
    WHERE status = 'Available' AND isDeleted = 0 
    ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPCA - Foster Application</title>
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Germania+One&family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Hind Siliguri', sans-serif; 
            line-height: 1.6; 
            color: #333; 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 70px; 
            min-height: 100vh;
        }
        .page-header { 
            background: linear-gradient(135deg, #2c5aa0, #1a3d73); 
            color: white; 
            text-align: center; 
            padding: 50px 20px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .page-header h1 { font-size: 2.5em; margin-bottom: 10px; font-weight: 700; }
        .page-header p { font-size: 1.1em; opacity: 0.9; }
        
        .content-section { 
            max-width: 1200px; 
            margin: -20px auto 0; 
            padding: 20px; 
            position: relative; 
            z-index: 10;
        }
        .application-layout { display: flex; gap: 40px; flex-wrap: wrap; }
        .form-container { 
            flex: 2; 
            min-width: 320px; 
            background: white; 
            border-radius: 16px; 
            padding: 40px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .image-container { 
            flex: 1; 
            min-width: 280px; 
        }
        
        /* Alert Messages */
        .error-message { 
            color: #721c24; 
            background: linear-gradient(135deg, #f8d7da, #f5c6cb); 
            border: none; 
            padding: 20px; 
            margin-bottom: 25px; 
            border-radius: 12px; 
            border-left: 5px solid #dc3545;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.1);
        }
        .success-message { 
            color: #155724; 
            background: linear-gradient(135deg, #d4edda, #c3e6cb); 
            border: none; 
            padding: 20px; 
            margin-bottom: 25px; 
            border-radius: 12px; 
            border-left: 5px solid #28a745;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.1);
        }
        .info-box { 
            background: linear-gradient(135deg, #e3f2fd, #bbdefb); 
            border-left: 5px solid #2196f3; 
            padding: 20px; 
            margin-bottom: 25px; 
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.1);
        }
        
        /* Form Styling */
        .form-group { margin-bottom: 25px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        label { 
            display: block; 
            margin-bottom: 10px; 
            font-weight: 600; 
            color: #495057; 
            font-size: 1em;
        }
        .required::after { content: " *"; color: #dc3545; font-weight: bold; }
        
        input, select, textarea { 
            width: 100%; 
            padding: 15px; 
            border-radius: 10px; 
            border: 2px solid #e1e5e9; 
            font-size: 16px; 
            transition: all 0.3s ease;
            font-family: inherit;
        }
        input:focus, select:focus, textarea:focus { 
            outline: none; 
            border-color: #2c5aa0; 
            box-shadow: 0 0 0 4px rgba(44, 90, 160, 0.1);
            transform: translateY(-1px);
        }
        
        textarea { 
            resize: vertical; 
            min-height: 100px;
        }
        
        /* Button Styling */
        .submit-btn { 
            width: 100%;
            padding: 18px 30px; 
            background: linear-gradient(135deg, #2c5aa0, #1a3d73); 
            color: white; 
            border: none; 
            cursor: pointer; 
            border-radius: 50px; 
            font-size: 18px; 
            font-weight: 600; 
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(44, 90, 160, 0.3);
        }
        .submit-btn:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 8px 25px rgba(44, 90, 160, 0.4);
        }
        .submit-btn:active {
            transform: translateY(-1px);
        }
        
        .back-link { 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px; 
            color: #2c5aa0; 
            text-decoration: none; 
            font-weight: 600;
            padding: 10px 0;
            transition: all 0.3s ease;
        }
        .back-link:hover { 
            color: #1a3d73;
            transform: translateX(-5px);
        }
        
        /* Side Information */
        .side-info { 
            background: white; 
            padding: 30px; 
            border-radius: 16px; 
            margin-bottom: 25px; 
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border-top: 4px solid #2c5aa0;
        }
        .side-info h4 { 
            color: #2c5aa0; 
            margin-bottom: 20px; 
            font-size: 1.3em;
            font-weight: 700;
        }
        .side-info ul { 
            list-style: none; 
            padding: 0; 
        }
        .side-info li { 
            padding: 12px 0; 
            border-bottom: 1px solid #f0f2f5;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .side-info li:last-child { border-bottom: none; }
        .side-info li::before {
            content: "✓";
            background: #28a745;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .hero-icon {
            font-size: 4em;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) { 
            .application-layout { flex-direction: column; }
            .form-row { grid-template-columns: 1fr; gap: 15px; }
            .form-container { padding: 25px; }
            .page-header h1 { font-size: 2em; }
        }

        /* Loading Animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        .loading .submit-btn {
            background: #6c757d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <?php include '../navbar functionalities/navbar.php'; ?>

    <div class="page-header">
        <h1>🏠 Foster Care Application</h1>
        <p>Open your heart and home to an animal in need</p>
    </div>

    <div class="content-section">
        <a href='../landing pages/foster_landing.php' class="back-link">
            ← Back to Foster Page
        </a>
        
        <div class="application-layout">
            <div class="form-container">
                <h2 style="color: #2c5aa0; margin-bottom: 20px; font-size: 1.8em;">Your Foster Application</h2>
                
                <div class="info-box">
                    <strong>📋 Important:</strong> You must be logged in to apply. All fields marked with * are required. Your application will be reviewed within 5-7 business days.
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <strong>⚠️ Please fix the following:</strong><br>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <form method="POST" action="" id="fosterForm">
                    <div class="form-group">
                        <label for="animalID" class="required">Choose the animal you want to foster:</label>
                        <select name="animalID" id="animalID" required>
                            <option value="">-- Select an animal --</option>
                            <?php if ($available_animals_query && $available_animals_query->num_rows > 0): ?>
                                <?php while ($animal = $available_animals_query->fetch_assoc()): ?>
                                    <option value="<?php echo $animal['animalID']; ?>" 
                                            <?php echo (isset($_POST['animalID']) && $_POST['animalID'] == $animal['animalID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($animal['name'] . " (" . $animal['species'] . " - " . $animal['breed'] . ")"); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="">No animals currently available for foster</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fosterPreference" class="required">I am most interested in fostering:</label>
                        <select name="fosterPreference" id="fosterPreference" required>
                            <option value="">-- Please select --</option>
                            <option value="Kittens/Puppies (under 8 weeks)" <?php echo (isset($_POST['fosterPreference']) && $_POST['fosterPreference'] == 'Kittens/Puppies (under 8 weeks)') ? 'selected' : ''; ?>>Kittens/Puppies (under 8 weeks)</option>
                            <option value="Adult Dogs" <?php echo (isset($_POST['fosterPreference']) && $_POST['fosterPreference'] == 'Adult Dogs') ? 'selected' : ''; ?>>Adult Dogs</option>
                            <option value="Adult Cats" <?php echo (isset($_POST['fosterPreference']) && $_POST['fosterPreference'] == 'Adult Cats') ? 'selected' : ''; ?>>Adult Cats</option>
                            <option value="Animals with medical needs" <?php echo (isset($_POST['fosterPreference']) && $_POST['fosterPreference'] == 'Animals with medical needs') ? 'selected' : ''; ?>>Animals with medical needs</option>
                            <option value="No preference" <?php echo (isset($_POST['fosterPreference']) && $_POST['fosterPreference'] == 'No preference') ? 'selected' : ''; ?>>No preference / As needed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="householdInfo" class="required">Who lives in your home (adults, children, ages)?</label>
                        <textarea name="householdInfo" id="householdInfo" rows="3" required 
                                  placeholder="e.g., 2 adults (30s), 1 child (age 8)"><?php echo htmlspecialchars($_POST['householdInfo'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="homeType" class="required">Type of home:</label>
                            <select name="homeType" id="homeType" required>
                                <option value="">-- Select --</option>
                                <option value="House" <?php echo (isset($_POST['homeType']) && $_POST['homeType'] == 'House') ? 'selected' : ''; ?>>House</option>
                                <option value="Apartment/Condo" <?php echo (isset($_POST['homeType']) && $_POST['homeType'] == 'Apartment/Condo') ? 'selected' : ''; ?>>Apartment/Condo</option>
                                <option value="Townhouse" <?php echo (isset($_POST['homeType']) && $_POST['homeType'] == 'Townhouse') ? 'selected' : ''; ?>>Townhouse</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hasYard" class="required">Do you have a securely fenced yard?</label>
                            <select name="hasYard" id="hasYard" required>
                                <option value="">-- Select --</option>
                                <option value="Yes" <?php echo (isset($_POST['hasYard']) && $_POST['hasYard'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                <option value="No" <?php echo (isset($_POST['hasYard']) && $_POST['hasYard'] == 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="currentPets" class="required">Please list any current pets (name, species, age):</label>
                        <textarea name="currentPets" id="currentPets" rows="3" required 
                                  placeholder="If none, please write 'None'"><?php echo htmlspecialchars($_POST['currentPets'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="experience">Previous foster or animal care experience (if any):</label>
                        <textarea name="experience" id="experience" rows="3" 
                                  placeholder="If none, please write 'None'"><?php echo htmlspecialchars($_POST['experience'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="fosterDurationDays" class="required">Preferred Foster Duration:</label>
                        <select name="fosterDurationDays" id="fosterDurationDays" required>
                            <option value="">-- Select duration --</option>
                            <option value="1" <?php echo (isset($_POST['fosterDurationDays']) && $_POST['fosterDurationDays'] == '1') ? 'selected' : ''; ?>>1 Day</option>
                            <option value="2" <?php echo (isset($_POST['fosterDurationDays']) && $_POST['fosterDurationDays'] == '2') ? 'selected' : ''; ?>>2 Days</option>
                            <option value="3" <?php echo (isset($_POST['fosterDurationDays']) && $_POST['fosterDurationDays'] == '3') ? 'selected' : ''; ?>>3 Days</option>
                            <option value="4" <?php echo (isset($_POST['fosterDurationDays']) && $_POST['fosterDurationDays'] == '4') ? 'selected' : ''; ?>>4 Days</option>
                            <option value="5" <?php echo (isset($_POST['fosterDurationDays']) && $_POST['fosterDurationDays'] == '5') ? 'selected' : ''; ?>>5 Days</option>
                            <option value="6" <?php echo (isset($_POST['fosterDurationDays']) && $_POST['fosterDurationDays'] == '6') ? 'selected' : ''; ?>>6 Days</option>
                            <option value="7" <?php echo (isset($_POST['fosterDurationDays']) && $_POST['fosterDurationDays'] == '7') ? 'selected' : ''; ?>>7 Days (Max)</option>
                        </select>
                    </div>

                    <div style="text-align: center; margin-top: 35px;">
                        <button type="submit" class="submit-btn" id="submitBtn">
                            Submit Foster Application
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="image-container">
                <div style="text-align: center;" class="hero-icon">🏠</div>
                <h3 style="text-align: center; color: #2c5aa0; margin-bottom: 20px; font-size: 1.5em;">Your Foster Journey</h3>
                <p style="text-align: center; margin-bottom: 30px; color: #666; font-size: 1.1em;">
                    By filling out this form, you're taking the first step to become a foster hero. 
                    Provide temporary, loving care for animals who need it most.
                </p>

                <div class="side-info">
                    <h4>🗓️ What Happens Next?</h4>
                    <ul>
                        <li>Application review (5-7 days)</li>
                        <li>Phone or Home Check</li>
                        <li>Foster Orientation</li>
                        <li>Welcome your first foster animal!</li>
                    </ul>
                </div>

                <div class="side-info">
                    <h4>📋 Key Requirements</h4>
                    <ul>
                        <li>Minimum age: 18 years</li>
                        <li>Safe, secure environment</li>
                        <li>Time for animal's needs</li>
                        <li>Follow foster protocols</li>
                        <li>Heart big enough to say goodbye</li>
                    </ul>
                </div>

                <div class="side-info">
                    <h4>💝 Foster Benefits</h4>
                    <ul>
                        <li>Save a life directly</li>
                        <li>Experience joy and companionship</li>
                        <li>Help socialize animals</li>
                        <li>Support SPCA mission</li>
                        <li>Make room for more rescues</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('fosterForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const form = this;
            
            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            form.classList.add('loading');
            
            let errors = [];
            
            // Validate all required fields
            if (!document.getElementById('animalID').value) {
                errors.push('Please select an animal to foster.');
            }
            if (!document.getElementById('fosterPreference').value) {
                errors.push('Foster preference is required.');
            }
            if (!document.getElementById('householdInfo').value.trim()) {
                errors.push('Household information is required.');
            }
            if (!document.getElementById('homeType').value) {
                errors.push('Home type is required.');
            }
            if (!document.getElementById('hasYard').value) {
                errors.push('Yard information is required.');
            }
            if (!document.getElementById('currentPets').value.trim()) {
                errors.push('Current pets information is required.');
            }
            if (!document.getElementById('fosterDurationDays').value) {
                errors.push('Preferred foster duration is required.');
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Foster Application';
                form.classList.remove('loading');
                
                // Show errors
                alert('Please fix the following issues:\n\n• ' + errors.join('\n• '));
                
                // Focus on first error field
                const firstErrorField = document.querySelector('input:invalid, select:invalid, textarea:invalid');
                if (firstErrorField) {
                    firstErrorField.focus();
                }
                
                return false;
            }
            
            // If validation passes, show loading state
            setTimeout(() => {
                if (form.classList.contains('loading')) {
                    submitBtn.textContent = 'Processing Application...';
                }
            }, 1000);
        });

        // Auto-save form data to prevent loss
        const formElements = document.querySelectorAll('#fosterForm input, #fosterForm select, #fosterForm textarea');
        formElements.forEach(element => {
            element.addEventListener('change', function() {
                localStorage.setItem('foster_form_' + this.name, this.value);
            });
        });

        // Restore form data on page load
        window.addEventListener('load', function() {
            formElements.forEach(element => {
                const savedValue = localStorage.getItem('foster_form_' + element.name);
                if (savedValue && !element.value) {
                    element.value = savedValue;
                }
            });
        });

        // Clear saved form data on successful submission
        window.addEventListener('beforeunload', function() {
            if (document.querySelector('.success-message')) {
                formElements.forEach(element => {
                    localStorage.removeItem('foster_form_' + element.name);
                });
            }
        });

        // Add visual feedback for form completion
        const requiredFields = document.querySelectorAll('[required]');
        
        function updateProgress() {
            let completed = 0;
            requiredFields.forEach(field => {
                if (field.value.trim()) completed++;
            });
            
            const progress = (completed / requiredFields.length) * 100;
            const submitBtn = document.getElementById('submitBtn');
            
            if (progress === 100) {
                submitBtn.style.background = 'linear-gradient(135deg, #28a745, #20a043)';
                submitBtn.innerHTML = '✓ Submit Foster Application';
            } else {
                submitBtn.style.background = 'linear-gradient(135deg, #2c5aa0, #1a3d73)';
                submitBtn.innerHTML = 'Submit Foster Application';
            }
        }

        requiredFields.forEach(field => {
            field.addEventListener('input', updateProgress);
            field.addEventListener('change', updateProgress);
        });

        // Initial progress check
        updateProgress();
    </script>
</body>
</html>