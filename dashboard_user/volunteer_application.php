<?php
ob_start(); // START OUTPUT BUFFERING: This is the key fix to prevent "headers already sent" errors.
session_start();
include '../databaseConnection.php';

// Initialize variables to hold messages and form data
$error_message = '';
$form_data = $_POST; // Store POST data to repopulate the form on error

// --- [START] FORM SUBMISSION LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!isset($_SESSION['userID'])) {
        header("Location: ../navbar functionalities/userRegisterC.php?volunteer_redirect=1");
        exit();
    }

    $userID = $_SESSION['userID'];
    
    $stmt_check = $conn->prepare("SELECT applicationStatus FROM application WHERE userID = ? AND applicationType = 'Volunteer' AND (applicationStatus = 'Pending' OR applicationStatus = 'Approved')");
    $stmt_check->bind_param("i", $userID);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        $stmt_check->bind_result($status);
        $stmt_check->fetch();
        $error_message = "You already have a {$status} volunteer application. Please check your dashboard for updates.";
    } else {
        $volunteerPreference = trim($form_data['volunteerPreference'] ?? '');
        $volunteerReason = trim($form_data['volunteerReason'] ?? '');
        $experience = trim($form_data['experience'] ?? '');
        
        $availability_array = $form_data['availability'] ?? [];
        $availability_text = trim($form_data['availability_text'] ?? '');
        $availability = '';
        if (!empty($availability_array)) {
            $availability = implode(', ', $availability_array);
        }
        if (!empty($availability_text)) {
            $availability .= (!empty($availability) ? '. Additional notes: ' : '') . $availability_text;
        }

        $validation_errors = [];
        if (empty($volunteerPreference)) { $validation_errors[] = "Please select your primary volunteer interest."; }
        if (empty($volunteerReason)) { $validation_errors[] = "Please tell us why you want to volunteer."; } 
        elseif (strlen($volunteerReason) < 20) { $validation_errors[] = "Your reason for volunteering needs to be at least 20 characters long."; }
        if (empty($availability_array)) { $validation_errors[] = "Please select at least one availability time slot."; }

        if (!empty($validation_errors)) {
            $error_message = "⚠️ Please fix the following issues:<br>• " . implode("<br>• ", $validation_errors);
        } else {
            try {
                $conn->begin_transaction();
                $sql = "INSERT INTO application (userID, animalID, applicationStatus, applicationType, volunteerPreference, volunteerReason, availability, experience, createDate, applicationDate) VALUES (?, 0, 'Pending', 'Volunteer', ?, ?, ?, ?, NOW(), NOW())";
                $stmt = $conn->prepare($sql);
                if (!$stmt) throw new Exception($conn->error);
                $stmt->bind_param("issss", $userID, $volunteerPreference, $volunteerReason, $availability, $experience);
                $stmt->execute();
                if ($stmt->error) throw new Exception($stmt->error);
                $stmt->close();
                $conn->commit();
                $_SESSION['success_message'] = "Your application has been received!";
                header("Location: volunteerSuccess.php");
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "There was an error submitting your application. Please try again.";
                error_log("Volunteer Application Error: " . $e->getMessage());
            }
        }
    }
    $stmt_check->close();
}
// --- [END] FORM SUBMISSION LOGIC ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPCA - Volunteer Application</title>
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --primary-color: #AE9787;
            --accent-color: #2c3e50;
            --text-dark: #34495e;
            --text-light: #7f8c8d;
            --bg-light: #f8f9fa;
            --white: #ffffff;
            --border-color: #dee2e6;
            --success-color: #28a745;
            --error-color: #dc3545;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            padding-top: 70px;
            line-height: 1.7;
        }

        .page-header { 
            background: linear-gradient(135deg, var(--accent-color), #34495e); 
            color: var(--white);
            text-align: center;
            padding: 50px 20px;
        }
        
        .content-section { max-width: 900px; margin: auto; padding: 40px 20px; }
        
        /* --- Form Container & Steps --- */
        .form-container {
            background: var(--white);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .form-step { display: none; }
        .form-step.active { display: block; }
        .form-step h3 {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 1.8em;
            text-align: center;
        }

        /* --- Progress Bar --- */
        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .progress-step {
            text-align: center;
            width: 33.33%;
            position: relative;
            font-weight: 600;
            color: #ccc;
            transition: color 0.4s;
        }
        .progress-step.active { color: var(--accent-color); }
        .progress-step::before {
            content: '';
            width: 30px;
            height: 30px;
            background: var(--white);
            border: 3px solid #ccc;
            border-radius: 50%;
            display: block;
            margin: 0 auto 10px;
            transition: all 0.4s ease;
        }
        .progress-step.active::before { border-color: var(--accent-color); }
        .progress-line {
            position: absolute;
            top: 15px;
            left: 0;
            height: 3px;
            background: #ccc;
            width: 100%;
            z-index: -1;
            transition: background 0.4s;
        }
        .progress-line-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: var(--accent-color);
            width: 0%;
            transition: width 0.4s ease;
        }

        /* --- Form Elements --- */
        .form-group { margin-bottom: 25px; }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600;
        }
        .required::after { content: " *"; color: var(--error-color); }
        input, select, textarea { 
            width: 100%; 
            padding: 12px; 
            border-radius: 8px; 
            border: 1px solid var(--border-color);
            font-size: 1rem;
            transition: all 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(174, 151, 135, 0.2);
        }

        /* --- Buttons --- */
        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 30px; 
            border: none; 
            cursor: pointer; 
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-next { background: var(--accent-color); color: var(--white); }
        .btn-prev { background: #ccc; color: var(--text-dark); }
        .btn-submit { 
            background: linear-gradient(135deg, #5cb85c, #4cae4c);
            color: var(--white);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn:disabled { background: #ccc; cursor: not-allowed; }
        .loader {
            width: 18px;
            height: 18px;
            border: 2px solid #FFF;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
            margin-left: 10px;
        }
        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }


        /* --- Availability Grid --- */
        .availability-grid { width: 100%; border-collapse: collapse; }
        .availability-grid th, .availability-grid td { text-align: center; padding: 12px 8px; border: 1px solid var(--border-color); }
        .availability-grid th { background-color: var(--primary-color); color: white; }
        .availability-grid td:first-child { text-align: left; font-weight: 600; }
        .availability-grid td { cursor: pointer; transition: background-color 0.3s; }
        .availability-grid td.selected { background-color: rgba(174, 151, 135, 0.2); }
        .availability-grid input[type="checkbox"] { display: none; } /* Hide checkbox, cell is clickable */

        /* --- Messages --- */
        .error-message { 
            color: #721c24; background: #f8d7da; border-left: 4px solid var(--error-color); 
            padding: 15px; margin-bottom: 20px; border-radius: 8px;
        }
        .field-error {
            color: var(--error-color);
            font-size: 0.9rem;
            margin-top: 5px;
            display: none; /* Hidden by default */
        }
        
        /* NEW: Character Counter */
        .char-counter {
            text-align: right;
            font-size: 0.9rem;
            color: var(--text-light);
            margin-top: 5px;
        }
        .char-counter.valid {
            color: var(--success-color);
            font-weight: 600;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include '../navbar functionalities/navbar.php'; ?>

    <div class="page-header">
        <h1>Volunteer Application</h1>
        <p>Join our team and start making a difference in the lives of animals.</p>
    </div>

    <div class="content-section">
        <a href='../landing pages/volunteerLanding.php' class="back-link"><i class="fas fa-arrow-left"></i> Back to Volunteer Page</a>
        
        <div class="form-container">
            <div class="progress-bar">
                <div class="progress-line"><div class="progress-line-fill"></div></div>
                <div class="progress-step active" data-step="1">Interest</div>
                <div class="progress-step" data-step="2">Availability</div>
                <div class="progress-step" data-step="3">Motivation</div>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if (!isset($_SESSION['userID'])): ?>
                <div class="error-message">
                    <strong>Login Required:</strong> You need to be logged in to apply. 
                    <a href="../navbar functionalities/userRegisterC.php?volunteer_redirect=1" style="color: #721c24; font-weight: bold;">Click here to register</a> 
                    or <a href="../navbar functionalities/userLoginC.php?volunteer_redirect=1" style="color: #721c24; font-weight: bold;">login here</a>.
                </div>
            <?php else: ?>
            
            <form method="POST" action="" id="volunteerForm" novalidate>
                <div class="form-step active" data-step="1">
                    <h3>Step 1: Your Interests</h3>
                    <div class="form-group">
                        <label for="volunteerPreference" class="required">Primary Volunteer Interest</label>
                        <select name="volunteerPreference" id="volunteerPreference" required>
                            <option value="">-- Please select an option --</option>
                            <option value="Dog Walking & Care" <?= ($form_data['volunteerPreference'] ?? '') == 'Dog Walking & Care' ? 'selected' : '' ?>>Dog Walking & Care</option>
                            <option value="Cat Cuddling & Socialization" <?= ($form_data['volunteerPreference'] ?? '') == 'Cat Cuddling & Socialization' ? 'selected' : '' ?>>Cat Cuddling & Socialization</option>
                            <option value="General Dog Care" <?= ($form_data['volunteerPreference'] ?? '') == 'General Dog Care' ? 'selected' : '' ?>>General Dog Care</option>
                            <option value="General Cat Care" <?= ($form_data['volunteerPreference'] ?? '') == 'General Cat Care' ? 'selected' : '' ?>>General Cat Care</option>
                            <option value="Administrative Support" <?= ($form_data['volunteerPreference'] ?? '') == 'Administrative Support' ? 'selected' : '' ?>>Administrative Support</option>
                            <option value="Events and Fundraising" <?= ($form_data['volunteerPreference'] ?? '') == 'Events and Fundraising' ? 'selected' : '' ?>>Events and Fundraising</option>
                            <option value="General Animal Care" <?= ($form_data['volunteerPreference'] ?? '') == 'General Animal Care' ? 'selected' : '' ?>>General Animal Care (Cleaning, Feeding)</option>
                        </select>
                        <div class="field-error" id="preferenceError">This field is required.</div>
                    </div>
                </div>

                <div class="form-step" data-step="2">
                    <h3>Step 2: Your Availability</h3>
                    <div class="form-group">
                        <label class="required">Weekly Availability (Mon-Sat, 9am-4pm)</label>
                        <table class="availability-grid">
                            <tr><th></th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>
                            <tr>
                                <td>Morning (9-12)</td>
                                <?php foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day): ?>
                                    <td><input type="checkbox" name="availability[]" value="<?= $day ?> Morning" <?= in_array($day.' Morning', $form_data['availability'] ?? []) ? 'checked' : '' ?>></td>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <td>Afternoon (12-4)</td>
                                <?php foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day): ?>
                                    <td><input type="checkbox" name="availability[]" value="<?= $day ?> Afternoon" <?= in_array($day.' Afternoon', $form_data['availability'] ?? []) ? 'checked' : '' ?>></td>
                                <?php endforeach; ?>
                            </tr>
                        </table>
                        <div class="field-error" id="availabilityError">Please select at least one time slot.</div>
                        <div style="margin-top: 15px;">
                            <label for="availability_text">Additional Notes (Optional)</label>
                            <textarea name="availability_text" id="availability_text" rows="2" placeholder="e.g., 'Available every other Saturday'"><?= htmlspecialchars($form_data['availability_text'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-step" data-step="3">
                    <h3>Step 3: Your Motivation</h3>
                    <div class="form-group">
                        <label for="experience">Previous Experience</label>
                        <textarea name="experience" id="experience" rows="4" placeholder="Tell us about any relevant experience with animals or volunteering. (Optional)"><?= htmlspecialchars($form_data['experience'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="volunteerReason" class="required">Why do you want to volunteer with us?</label>
                        <textarea name="volunteerReason" id="volunteerReason" rows="5" required minlength="20" placeholder="Share your motivation for wanting to help animals..."><?= htmlspecialchars($form_data['volunteerReason'] ?? '') ?></textarea>
                        <div id="charCounter" class="char-counter"></div>
                        <div class="field-error" id="reasonError">Please tell us more (at least 20 characters).</div>
                    </div>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn btn-prev" style="display: none;">Previous</button>
                    <button type="button" class="btn btn-next">Next</button>
                    <button type="submit" class="btn btn-submit" style="display: none;">
                        <span>Submit Application</span>
                        <div class="loader" style="display: none;"></div>
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('volunteerForm');
        if (!form) return; // Stop if form doesn't exist (e.g., for non-logged-in users)

        const steps = Array.from(form.querySelectorAll('.form-step'));
        const nextBtn = form.querySelector('.btn-next');
        const prevBtn = form.querySelector('.btn-prev');
        const submitBtn = form.querySelector('.btn-submit');
        const progressSteps = Array.from(document.querySelectorAll('.progress-step'));
        const progressLineFill = document.querySelector('.progress-line-fill');
        
        // NEW: Character Counter Elements
        const reasonTextarea = document.getElementById('volunteerReason');
        const charCounter = document.getElementById('charCounter');
        const minChars = 20;

        let currentStep = 0;

        // --- MULTI-STEP FORM LOGIC ---
        nextBtn.addEventListener('click', () => {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });

        prevBtn.addEventListener('click', () => {
            currentStep--;
            showStep(currentStep);
        });
        
        form.addEventListener('submit', function(e) {
            if (!validateStep(currentStep)) {
                e.preventDefault();
            } else {
                submitBtn.querySelector('span').style.display = 'none';
                submitBtn.querySelector('.loader').style.display = 'inline-block';
                submitBtn.disabled = true;
            }
        });

        function showStep(stepIndex) {
            steps.forEach((step, index) => {
                step.classList.toggle('active', index === stepIndex);
            });
            updateProgress(stepIndex);
            updateButtons(stepIndex);
        }

        function updateProgress(stepIndex) {
            progressSteps.forEach((step, index) => {
                step.classList.toggle('active', index <= stepIndex);
            });
            const progressPercentage = (stepIndex / (steps.length - 1)) * 100;
            progressLineFill.style.width = progressPercentage + '%';
        }

        function updateButtons(stepIndex) {
            prevBtn.style.display = stepIndex > 0 ? 'inline-block' : 'none';
            nextBtn.style.display = stepIndex < steps.length - 1 ? 'inline-block' : 'none';
            submitBtn.style.display = stepIndex === steps.length - 1 ? 'inline-block' : 'none';
        }
        
        // --- LIVE INLINE VALIDATION ---
        function validateStep(stepIndex) {
            let isValid = true;
            const currentStepFields = steps[stepIndex].querySelectorAll('[required]');
            
            steps[stepIndex].querySelectorAll('.field-error').forEach(err => err.style.display = 'none');
            
            currentStepFields.forEach(field => {
                let fieldValid = true;
                if (field.type === 'select-one' && field.value === '') {
                    fieldValid = false;
                }
                if (field.type === 'textarea' && field.value.trim().length < (field.minLength || 0)) {
                    fieldValid = false;
                }
                
                if (!fieldValid) {
                    isValid = false;
                    const errorElement = document.getElementById(field.id + 'Error');
                    if (errorElement) errorElement.style.display = 'block';
                }
            });
            
            if (stepIndex === 1) {
                const availabilityChecked = form.querySelectorAll('input[name="availability[]"]:checked').length > 0;
                if (!availabilityChecked) {
                    isValid = false;
                    document.getElementById('availabilityError').style.display = 'block';
                }
            }
            
            return isValid;
        }

        form.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('input', () => validateStep(currentStep));
        });

        // --- INTERACTIVE AVAILABILITY GRID ---
        const availabilityCells = document.querySelectorAll('.availability-grid td');
        availabilityCells.forEach(cell => {
            const checkbox = cell.querySelector('input[type="checkbox"]');
            if (checkbox) {
                if (checkbox.checked) cell.classList.add('selected');

                cell.addEventListener('click', () => {
                    checkbox.checked = !checkbox.checked;
                    cell.classList.toggle('selected', checkbox.checked);
                    validateStep(currentStep);
                });
            }
        });
        
        // --- NEW: CHARACTER COUNTER LOGIC ---
        function updateCharCounter() {
            const currentLength = reasonTextarea.value.length;
            if (currentLength < minChars) {
                charCounter.textContent = `${currentLength}/${minChars} characters`;
                charCounter.classList.remove('valid');
            } else {
                charCounter.textContent = `✓ ${currentLength}/${minChars} characters`;
                charCounter.classList.add('valid');
            }
        }
        reasonTextarea.addEventListener('input', updateCharCounter);
        // Run once on page load in case of sticky form data
        updateCharCounter();
    });
    </script>
</body>
</html>
<?php ob_end_flush(); // END OUTPUT BUFFERING: Send the final output to the browser. ?>