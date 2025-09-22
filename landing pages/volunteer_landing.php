<?php
session_start();
include '../databaseConnection.php'; 

// This block is for handling the "Start Application" click from the form/button
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_action'])) {
    
    // Check if the user is already logged in
    if (isset($_SESSION['userID'])) {
        // User IS logged in, redirect them straight to the volunteer application
        header("Location: ../dashboard_user/volunteer_application.php");
        exit;
    } else {
        // User IS NOT logged in, redirect them to the registration page.
        // The 'volunteer_redirect=1' tells the registration/login page where to go next.
        header("Location: ../navbar functionalities/userRegisterC.php?volunteer_redirect=1");
        exit;
    }
}

// The rest of the page loads normally if the form wasn't submitted
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPCA - Volunteer With Us</title>
    <link rel="stylesheet" href="../navbar functionalities/login-register.css">
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    
    <style>
        /* Import Google Fonts to match navbar */
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
            background-color: #f8f9fa;
            padding-top: 70px; /* Account for fixed navbar */
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #AE9787 0%, #977f6f 100%);
            color: white;
            text-align: center;
            padding: 100px 20px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>') no-repeat center bottom;
            background-size: cover;
        }

        .hero-section h1 {
            font-size: 4.5em;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }

        .hero-subtitle {
            font-size: 1.3em;
            margin-bottom: 40px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            z-index: 1;
            opacity: 0.95;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            font-size: 1.1em;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
            position: relative;
            z-index: 1;
        }
        
        /* Form-based button styling */
        .form-btn {
             background: none;
             border: none;
             padding: 0;
             margin: 0;
             font-family: inherit;
             font-size: inherit;
             cursor: pointer;
        }

        .btn-primary {
            background: white;
            color: #AE9787;
            border-color: white;
        }

        .btn-primary:hover {
            background: transparent;
            color: white;
            border-color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border-color: white;
        }

        .btn-secondary:hover {
            background: white;
            color: #AE9787;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Content Sections */
        .content-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .content-section h2 {
            font-size: 2.5em;
            margin-bottom: 30px;
            color: #AE9787;
            text-align: center;
        }

        .content-section p {
            font-size: 1.1em;
            margin-bottom: 20px;
            text-align: center;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Section Divider */
        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #AE9787, transparent);
            margin: 50px 0;
        }

        /* Stats Grid */
        .volunteer-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .stat-card {
            text-align: center;
            background: white;
            padding: 40px 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-top: 4px solid #AE9787;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 3em;
            font-weight: bold;
            color: #AE9787;
            display: block;
            margin-bottom: 10px;
        }

        /* Opportunities Grid */
        .volunteer-opportunities {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .opportunity-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .opportunity-card:hover {
            transform: translateY(-8px);
        }

        .card-image {
            height: 200px;
            background: linear-gradient(45deg, #AE9787, #977f6f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3em;
            position: relative;
        }

        .card-content {
            padding: 30px;
        }

        .card-content h3 {
            color: #AE9787;
            margin-bottom: 15px;
            font-size: 1.5em;
        }

        .card-content ul {
            list-style: none;
            padding-left: 0;
        }

        .card-content li {
            padding: 5px 0;
            position: relative;
            padding-left: 20px;
        }

        .card-content li:before {
            content: '✓';
            color: #AE9787;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        /* Testimonials */
        .testimonial-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 60px 20px;
            margin: 50px 0;
        }

        .testimonials {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .testimonial {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            position: relative;
        }

        .testimonial::before {
            content: '"';
            font-size: 4em;
            color: #AE9787;
            position: absolute;
            top: -10px;
            left: 20px;
            line-height: 1;
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            padding-top: 20px;
        }

        .testimonial-author {
            font-weight: bold;
            color: #AE9787;
        }

        /* Timeline */
        .process-timeline {
            max-width: 800px;
            margin: 40px auto;
        }

        .timeline-step {
            display: flex;
            align-items: center;
            margin: 30px 0;
            position: relative;
        }

        .step-number {
            background: #AE9787;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2em;
            flex-shrink: 0;
            margin-right: 30px;
        }

        .step-content h4 {
            margin: 0 0 10px 0;
            color: #AE9787;
        }

        .step-content p {
            margin: 0;
            color: #666;
            text-align: left;
        }

        /* Features Grid */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .feature-card {
            text-align: center;
            padding: 30px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3em;
            margin-bottom: 20px;
            display: block;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #AE9787 0%, #977f6f 100%);
            color: white;
            text-align: center;
            padding: 80px 20px;
            margin: 50px 0 0 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,100 0,100"/></svg>') no-repeat center top;
            background-size: cover;
        }

        .cta-section h2,
        .cta-section p,
        .cta-section .btn {
            position: relative;
            z-index: 1;
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: #AE9787;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.5em;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: #977f6f;
            transform: translateY(-3px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5em;
            }

            .hero-subtitle {
                font-size: 1.1em;
            }

            .content-section {
                padding: 40px 15px;
            }

            .content-section h2 {
                font-size: 2em;
            }

            .btn {
                display: block;
                margin: 10px auto;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>
   <?php include '../navbar functionalities/navbar.php'; ?>
    
    <div class="hero-section">
        <h1>Become a Volunteer</h1>
        <p class="hero-subtitle">Your time, compassion, and effort can change the world for an animal in need. Join our family of over 500 dedicated volunteers making a difference every day.</p>
        <div style="display: inline-block;">
            <form action="volunteer_landing.php" method="POST" style="display: inline;">
                <input type="hidden" name="apply_action" value="1">
                <button type="submit" class="btn btn-primary form-btn" style="margin-right: 15px;">Start Application</button>
            </form>
            <!-- FIXED: Updated link to point to correct login file with volunteer redirect -->
            <a href='../navbar functionalities/userLoginD.php?volunteer_redirect=1' class="btn btn-secondary">Volunteer Login</a>
        </div>
    </div>
    
    <!-- Rest of your HTML content remains the same -->
    <div class="content-section">
        <h2>Our Volunteer Impact</h2>
        <div class="volunteer-stats">
            <div class="stat-card">
                <span class="stat-number">2,400+</span>
                <p><strong>Animals Helped</strong><br>Last year alone</p>
            </div>
            <div class="stat-card">
                <span class="stat-number">15,000</span>
                <p><strong>Volunteer Hours</strong><br>Contributed monthly</p>
            </div>
            <div class="stat-card">
                <span class="stat-number">500+</span>
                <p><strong>Active Volunteers</strong><br>In our community</p>
            </div>
            <div class="stat-card">
                <span class="stat-number">95%</span>
                <p><strong>Adoption Rate</strong><br>With volunteer support</p>
            </div>
        </div>
    </div>
    
    <!-- Continue with the rest of your existing content... -->
    
    <div class="cta-section">
        <h2 style="font-size: 2.5em; margin-bottom: 20px;">Ready to Make a Difference?</h2>
        <p style="font-size: 1.2em; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
            Every animal deserves love, care, and a chance at happiness. Your volunteer work directly contributes to saving lives and creating happy endings. Join us today and become part of something truly meaningful.
        </p>
        <div>
            <form action="volunteer_landing.php" method="POST" style="display: inline;">
                <input type="hidden" name="apply_action" value="1">
                <button type="submit" class="btn btn-primary form-btn" style="background: white; color: #AE9787; margin-right: 15px; font-size: 1.1em; padding: 15px 30px;">Start Your Application</button>
            </form>
            <!-- FIXED: Updated link with volunteer redirect parameter -->
            <a href="../navbar functionalities/userLoginC.php?volunteer_redirect=1" class="btn btn-secondary" style="border: 2px solid white; color: white; font-size: 1.1em; padding: 15px 30px;">Returning Volunteer</a>
        </div>
    </div>
    
    <button class="back-to-top" onclick="scrollToTop()">↑</button>

    <script>
        // Back to top functionality
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add fade-in animation for cards on scroll
        function animateOnScroll() {
            const cards = document.querySelectorAll('.stat-card, .opportunity-card, .feature-card, .testimonial');
            const windowHeight = window.innerHeight;
            
            cards.forEach(card => {
                const cardTop = card.getBoundingClientRect().top;
                if (cardTop < windowHeight - 100) {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }
            });
        }

        // Initialize cards with fade-in effect
        window.addEventListener('load', function() {
            const cards = document.querySelectorAll('.stat-card, .opportunity-card, .feature-card, .testimonial');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            });
            
            animateOnScroll();
        });

        window.addEventListener('scroll', animateOnScroll);
    </script>
</body>
</html>