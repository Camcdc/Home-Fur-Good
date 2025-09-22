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
    <title>Application Submitted - SPCA Foster Program</title>
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
            background: linear-gradient(90deg, #2c5aa0, #1a3d73);
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #2c5aa0, #1a3d73);
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
            color: #2c5aa0;
            margin-bottom: 20px;
            font-size: 2.5em;
            font-weight: 700;
        }

        .success-container h2 {
            color: #2c5aa0;
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
            color: #2c5aa0;
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
            color: #2c5aa0;
            font-weight: bold;
            font-size: 1.2em;
        }

        .contact-info {
            background: #2c5aa0;
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
            background: #2c5aa0;
            color: white;
        }

        .btn-primary:hover {
            background: #1a3d73;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background: transparent;
            color: #2c5aa0;
            border: 2px solid #2c5aa0;
        }

        .btn-secondary:hover {
            background: #2c5aa0;
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: #6c757d;
            border: 2px solid #6c757d;
        }

        .btn-outline:hover {
            background: #6c757d;
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
            border-left: 4px solid #2c5aa0;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }

        .highlight-box {
            background: linear-gradient(135deg, #2c5aa0, #1a3d73);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin: 30px 0;
            text-align: center;
        }

        .highlight-box h3 {
            margin-bottom: 15px;
            font-size: 1.4em;
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
                width: 100%;
                text-align: center;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include '../navbar functionalities/navbar.php'; ?>
    
    <div class="success-container">
        <div class="success-icon">🏠</div>
        
        <h1>Thank You!</h1>
        <h2>Foster Application Submitted Successfully</h2>
        
        <p class="success-message">
            Your foster application has been received and is now being reviewed by our foster coordinators. 
            We're thrilled about your interest in providing a temporary, loving home for animals in need!
        </p>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="application-id">
                <strong>Status:</strong> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <div class="highlight-box">
            <h3>What would you like to do next?</h3>
            <p>You can view your foster dashboard to track your application status and manage your foster profile, or return to our main page.</p>
        </div>

        <div class="next-steps">
            <h3>What Happens Next?</h3>
            <ul class="steps-list">
                <li><strong>Application Review (5-7 days):</strong> Our foster coordinators will carefully review your application and home requirements</li>
                <li><strong>Phone Interview:</strong> We'll contact you to discuss your foster preferences and answer any questions</li>
                <li><strong>Home Check:</strong> A friendly home visit (virtual or in-person) to ensure a safe environment</li>
                <li><strong>Foster Orientation:</strong> Learn about our foster protocols, policies, and meet other foster families</li>
                <li><strong>Your First Foster:</strong> We'll match you with the perfect animal who needs your care!</li>
            </ul>
        </div>

        <div class="contact-info">
            <h4>Questions or Need to Update Your Application?</h4>
            <p>Contact our Foster Coordinator:<br>
            <strong>Email:</strong> foster@spca.org<br>
            <strong>Phone:</strong> (555) 123-4567<br>
            <strong>Hours:</strong> Monday-Friday, 9 AM - 5 PM</p>
        </div>

        <div class="button-group">
            <?php if (isset($_SESSION['userID'])): ?>
                <!-- User is logged in - show dashboard option -->
                <a href="../dashboard_user/foster_dashboard.php" class="btn btn-primary">View My Dashboard</a>
                <a href="../landing pages/foster_landing.php" class="btn btn-secondary">Back to Foster Page</a>
                <a href="../index.php" class="btn btn-outline">Home Page</a>
            <?php else: ?>
                <!-- User is not logged in - show login option -->
                <a href="../navbar functionalities/userLoginC.php?foster_redirect=1" class="btn btn-primary">Login to Dashboard</a>
                <a href="../landing pages/foster_landing.php" class="btn btn-secondary">Back to Foster Page</a>
                <a href="../index.php" class="btn btn-outline">Home Page</a>
            <?php endif; ?>
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

            // Animate buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach((button, index) => {
                button.style.opacity = '0';
                button.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    button.style.transition = 'all 0.4s ease';
                    button.style.opacity = '1';
                    button.style.transform = 'translateY(0)';
                }, 1200 + (index * 100));
            });
        });
    </script>
</body>
</html>