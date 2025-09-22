<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../landing pages/homepage.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted - SPCA Volunteer</title>
      <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Germania+One&family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Hind Siliguri', sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding-top: 70px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 60px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .success-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #AE9787, #977f6f);
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #AE9787, #977f6f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 3em;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .success-container h1 {
            color: #AE9787;
            margin-bottom: 20px;
            font-size: 2.5em;
            font-weight: 700;
        }

        .success-container h2 {
            color: #AE9787;
            margin-bottom: 25px;
            font-size: 2em;
            font-weight: 600;
        }

        .success-message {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .next-steps {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin: 30px 0;
            text-align: left;
        }

        .next-steps h3 {
            color: #AE9787;
            margin-bottom: 20px;
            text-align: center;
        }

        .steps-list {
            list-style: none;
            padding: 0;
        }

        .steps-list li {
            padding: 10px 0;
            position: relative;
            padding-left: 30px;
            border-bottom: 1px solid #eee;
        }

        .steps-list li:last-child {
            border-bottom: none;
        }

        .steps-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #AE9787;
            font-weight: bold;
            font-size: 1.2em;
        }

        .contact-info {
            background: #AE9787;
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin: 30px 0;
        }

        .contact-info h4 {
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .btn-primary {
            background: #AE9787;
            color: white;
        }

        .btn-primary:hover {
            background: #977f6f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background: transparent;
            color: #AE9787;
            border: 2px solid #AE9787;
        }

        .btn-secondary:hover {
            background: #AE9787;
            color: white;
            transform: translateY(-2px);
        }

        .application-id {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 1.1em;
            border-left: 4px solid #AE9787;
        }

        @media (max-width: 600px) {
            .success-container {
                margin: 1rem;
                padding: 40px 20px;
            }
            
            .success-container h1 {
                font-size: 2em;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
      <?php include '../navbar functionalities/navbar.php'; ?>
    
    <div class="success-container">
        <div class="success-icon">✓</div>
        
        <h1>Thank You!</h1>
        <h2>Application Submitted Successfully</h2>
        
        <p class="success-message">
            Your volunteer application has been received and is now being reviewed by our volunteer coordinators. 
            We're excited about your interest in helping animals in need!
        </p>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="application-id">
                <strong>Status:</strong> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <div class="next-steps">
            <h3>What Happens Next?</h3>
            <ul class="steps-list">
                <li><strong>Review Process (5-7 days):</strong> Our volunteer coordinators will carefully review your application</li>
                <li><strong>Phone/Video Interview:</strong> We'll contact you to schedule a brief interview</li>
                <li><strong>Background Check:</strong> For the safety of our animals and volunteers</li>
                <li><strong>Orientation Session:</strong> Learn about SPCA policies and procedures</li>
                <li><strong>Training:</strong> Specific training for your chosen volunteer role</li>
                <li><strong>Start Volunteering:</strong> Begin making a difference in animals' lives!</li>
            </ul>
        </div>

        <div class="contact-info">
            <h4>Questions or Need to Update Your Application?</h4>
            <p>Contact our Volunteer Coordinator:<br>
            <strong>Email:</strong> volunteers@spca.org<br>
            <strong>Phone:</strong> (555) 123-4567<br>
            <strong>Hours:</strong> Monday-Friday, 9 AM - 5 PM</p>
        </div>

        <div style="margin-top: 40px;">
            <a href='../landing pages/volunteerLanding.php' class="btn btn-primary">Back to Volunteer Page</a>
            <a href="volunteer_dashboard.php" class="btn btn-secondary">Volunteer Portal</a>
        </div>

        <p style="margin-top: 30px; color: #999; font-size: 14px;">
            Application submitted on <?php echo date('F j, Y \a\t g:i A'); ?>
        </p>
    </div>

    <script>
        // Add some celebratory animation
        document.addEventListener('DOMContentLoaded', function() {
            // Animate the success container
            const container = document.querySelector('.success-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                container.style.transition = 'all 0.6s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);

            // Animate list items
            const listItems = document.querySelectorAll('.steps-list li');
            listItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    item.style.transition = 'all 0.4s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, 800 + (index * 100));
            });
        });
    </script>
</body>
</html>