<?php
include_once "../databaseConnection.php";
session_start();
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
        <title>Cruelty Report Information | Home Fur Good</title>
        <link rel="stylesheet" href="../navbar functionalities/navbar.css">
        <link rel="stylesheet" href="dashboard_user.css">
        <link rel="stylesheet" href="CrueltyReportInfor.css">
        <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
        
    </head>
    <body>
        <?php
        include '../navbar functionalities/navbar.php';
        ?>
        <div class="info-container">
            <h1>Cruelty Report Information</h1>
            <p>
                An animal cruelty report is a formal complaint submitted by a concerned individual regarding the mistreatment, neglect, or abuse of animals. These reports play a vital role in protecting animals by alerting authorities and animal welfare organizations to situations where intervention may be needed.
            </p>
            <section>
                <h2 style="color:#24527a; font-size:1.3em; margin-top:2rem;">How to Fill Out a Cruelty Report</h2>
                <ol>
                    <li><strong>Provide your contact details:</strong> Enter your first and last name, and a valid cell number so investigators can reach you for further information if needed.</li>
                    <li><strong>Upload evidence:</strong> If possible, attach a photo of the animal or the situation. This helps authorities assess the severity and urgency of the case.</li>
                    <li><strong>Describe the incident:</strong> Give a detailed description of what happened, including the animal's appearance, condition, and any signs of abuse or neglect. Be as specific as possible.</li>
                    <li><strong>Specify the location:</strong> Clearly state where the incident took place. Include street address, landmarks, or any information that will help responders find the location quickly.</li>
                    <li><strong>Submit the report:</strong> Review your information for accuracy and completeness, then submit the report. You will receive confirmation and may be able to view the status of your report online.</li>
                </ol>
            </section>
            <section>
                <h2 style="color:#24527a; font-size:1.15em; margin-top:2rem;">Why Your Report Matters</h2>
                <p>
                    When you submit a cruelty report, you provide important details such as the location of the incident, a description of what happened, and any evidence (such as photos). This information helps investigators assess the situation, respond quickly, and take appropriate action to ensure the safety and well-being of the affected animals.
                </p>
                <p>
                    Cruelty reports can lead to rescue operations, medical care for injured animals, and legal action against perpetrators. By reporting animal cruelty, you contribute to a safer and more compassionate community for all living beings.
                </p>
            </section>
            <section>
                <h2 style="color:#24527a; font-size:1.15em; margin-top:2rem;">Need Immediate Help?</h2>
                <p>If you are not logged in and wish to report animal cruelty, please call our hotline at <strong>1-800-555-ANIMAL (1-800-555-2646)</strong> for immediate assistance.</p>
            </section>
            <section style="margin-top:2.5rem;">
                <?php if ($userID): ?>
                    <form action="CreateCrueltyReport.php" method="GET" style="width:100%; text-align:center; margin-top:2rem;">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userID) ?>">
                        <button type="submit" style="width:100%; max-width:350px;">Create a Cruelty Report</button>
                    </form>
                <?php else: ?>
                    <form action="../navbar functionalities/userRegisterC.php" method="GET" style="text-align:center;">
                        <input type="hidden" name="cruelty_redirect" value="1">
                        <button type="submit" style="width:100%; max-width:350px;">Create Cruelty Report</button>
                    </form>
                <?php endif; ?>
            </section>
        </div>
         
    </body>
</html>

