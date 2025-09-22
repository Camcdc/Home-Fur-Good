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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    
    <style>
        /* --- Global Styles & Variables --- */
        :root {
            --primary-color: #AE9787; /* Warm Beige from original */
            --secondary-color: #977f6f; /* Darker Beige from original */
            --accent-color: #2c3e50; /* Professional Slate Blue */
            --text-dark: #34495e; /* Darker text for readability */
            --text-light: #7f8c8d;
            --bg-light: #f8f9fa;
            --white: #ffffff;
            --border-radius-md: 12px;
            --shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: var(--white);
            color: var(--text-dark);
            line-height: 1.7;
            padding-top: 70px; /* Account for fixed navbar */
        }

        /* --- Hero Section with Slideshow --- */
        .hero-section {
            color: white;
            text-align: center;
            padding: 0;
            position: relative;
            min-height: 600px; /* Increased height for better image view */
            height: 80vh; /* Responsive height */
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            overflow: hidden;
        }

        .hero-slideshow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 2s cubic-bezier(0.77,0,0.175,1), transform 20s linear;
            transform: scale(1.08);
        }

        .slide.active {
            opacity: 1;
            z-index: 2;
            animation: kenburns 20s linear;
        }
        
        @keyframes kenburns {
            0% { transform: scale(1.08) translate(0, 0); }
            50% { transform: scale(1.15) translate(2%, -2%); }
            100% { transform: scale(1.08) translate(0, 0); }
        }

        .hero-section::before {
             content: '';
             position: absolute;
             top: 0;
             left: 0;
             right: 0;
             bottom: 0;
             background: linear-gradient(to top, rgba(0,0,0,0.6), transparent 70%);
             z-index: 2;
        }

        .hero-overlay-text {
            position: absolute;
            z-index: 3;
            top: 20%;
            left: 0;
            width: 100%;
            text-align: center;
            color: #fff;
        }

        .hero-overlay-text h1 {
            font-size: 3em;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 16px rgba(44,62,80,0.3);
        }

        .hero-overlay-text p {
            font-size: 1.4em;
            font-weight: 400;
            margin-bottom: 30px;
            text-shadow: 0 2px 12px rgba(44,62,80,0.2);
        }

        .hero-buttons {
            position: relative;
            z-index: 3;
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 40px;
        }
        .btn, .form-btn {
            display: inline-block;
            min-width: 220px;
            padding: 16px 36px;
            font-size: 1.2em;
            font-weight: 700;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.77,0,0.175,1);
            cursor: pointer;
            border: 2px solid transparent;
            box-shadow: 0 4px 16px rgba(44,62,80,0.08);
            text-align: center;
        }
        .form-btn {
            min-width: 220px;
            padding: 16px 36px;
            font-size: 1.2em;
            font-weight: 700;
            border-radius: 50px;
            border: 2px solid transparent;
            background: var(--white);
            color: var(--accent-color);
            box-shadow: 0 4px 16px rgba(44,62,80,0.08);
            text-align: center;
        }
        .form-btn.btn-primary {
            background: var(--white);
            color: var(--accent-color);
            border-color: var(--white);
        }
        .form-btn.btn-primary:hover {
            background: var(--accent-color);
            color: var(--white);
            border-color: var(--accent-color);
            transform: scale(1.05);
        }
        .btn-primary, .form-btn.btn-primary {
            background: var(--white);
            color: var(--accent-color);
            border-color: var(--white);
        }
        .btn-primary:hover, .form-btn.btn-primary:hover {
            background: var(--accent-color);
            color: var(--white);
            border-color: var(--accent-color);
            transform: scale(1.05);
        }
        .btn-secondary {
            background: transparent;
            color: var(--white);
            border-color: var(--white);
        }
        .btn-secondary:hover {
            background: var(--white);
            color: var(--accent-color);
            border-color: var(--white);
            transform: scale(1.05);
        }

        /* --- Content Sections --- */
        .content-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 80px 20px;
        }

        h2 {
            font-size: 2.8em;
            margin-bottom: 20px;
            color: var(--accent-color);
            text-align: center;
            font-weight: 700;
        }
        
        .section-intro {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 50px auto;
            color: var(--text-light);
            font-size: 1.1em;
        }

        /* --- Opportunities Grid --- */
        .volunteer-opportunities {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .opportunity-card {
            background: var(--white);
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .opportunity-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 35px rgba(44, 62, 80, 0.15);
        }
        
        .card-image {
            height: 220px;
            background-size: cover;
            background-position: center;
        }
        
        .card-content {
            padding: 25px;
        }
        
        .card-content h3 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.6em;
        }

        .card-content p {
            margin-bottom: 15px;
            color: var(--text-light);
        }

        .card-content ul {
            list-style: none;
            padding-left: 0;
            color: var(--text-light);
        }

        .card-content li {
            padding-left: 25px;
            position: relative;
            margin-bottom: 8px;
        }

        .card-content li::before {
            content: "\f00c"; /* Font Awesome check icon */
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--primary-color);
            position: absolute;
            left: 0;
        }

        /* --- Process Timeline --- */
        .process-timeline {
            max-width: 800px;
            margin: 50px auto 0;
            position: relative;
        }

        .timeline-step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 40px;
        }
        
        .step-number {
            background: var(--primary-color);
            color: var(--white);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.5em;
            flex-shrink: 0;
            margin-right: 30px;
            border: 3px solid var(--white);
            box-shadow: 0 0 0 4px var(--primary-color);
        }
        
        .step-content h4 {
            margin: 0 0 10px 0;
            color: var(--accent-color);
            font-size: 1.4em;
        }
        
        .step-content p {
            margin: 0;
            color: var(--text-light);
        }

        /* --- FAQ Section --- */
        .faq-section {
            max-width: 800px;
            margin: 50px auto 0;
        }
        .faq-item {
            background: var(--white);
            margin-bottom: 15px;
            border-radius: var(--border-radius-md);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary-color);
        }
        .faq-question {
            width: 100%;
            background: transparent;
            border: none;
            text-align: left;
            padding: 20px;
            font-size: 1.1em;
            font-weight: 600;
            color: var(--text-dark);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-question::after {
            content: '\f078'; /* chevron-down */
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            transition: transform 0.3s ease;
            color: var(--primary-color);
        }
        .faq-question.active::after {
            transform: rotate(180deg);
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            padding: 0 20px;
        }
        .faq-answer p {
            padding-bottom: 20px;
            margin: 0;
            color: var(--text-light);
        }

        /* --- CTA Section --- */
        .cta-section {
            background: linear-gradient(135deg, var(--accent-color) 0%, #34495e 100%);
            color: var(--white);
            text-align: center;
            padding: 80px 20px;
        }

        .cta-section h2 {
            color: var(--white);
        }

        .cta-section p {
             font-size: 1.2em;
        }

        /* --- Back to Top Button --- */
        #backToTopBtn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.2em;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #backToTopBtn.show {
            opacity: 1;
            visibility: visible;
        }

        #backToTopBtn:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
   <?php include '../navbar functionalities/navbar.php'; ?>
    
    <div class="hero-section">
        <div class="hero-slideshow">
            <div class="slide active" style="background-image: url('Volunteer-1.png');"></div>
            <div class="slide" style="background-image: url('slideshow1.jpg');"></div>
            <div class="slide" style="background-image: url('slideshow2.jpg');"></div>
            <div class="slide" style="background-image: url('slideshow 3.jpg');"></div>
            <div class="slide" style="background-image: url('slideshow4.jpg');"></div>
            <div class="slide" style="background-image: url('slideshow5.jpg');"></div>
        </div>
        <div class="hero-overlay-text" style="position: absolute; z-index: 3; top: 20%; left: 0; width: 100%; text-align: center; color: #fff;">
            <h1 style="font-size: 3em; font-weight: 700; margin-bottom: 10px; text-shadow: 0 2px 16px rgba(44,62,80,0.3);">Volunteer at SPCA</h1>
            <p style="font-size: 1.4em; font-weight: 400; margin-bottom: 30px; text-shadow: 0 2px 12px rgba(44,62,80,0.2);">Make a difference in the lives of animals and your community. Join our team of passionate volunteers and help us create happy endings every day.</p>
        </div>
        <div class="hero-buttons">
            <form action="volunteer_landing.php" method="POST" style="display: inline;">
                <input type="hidden" name="apply_action" value="1">
                <button type="submit" class="btn btn-primary form-btn" style="margin-right: 0;">Start Application</button>
            </form>
            <a href='../navbar functionalities/userLoginC.php?volunteer_redirect=1' class="btn btn-secondary">Volunteer Login</a>
        </div>
    </div>
    
    <div class="content-section" style="background-color: var(--bg-light);">
        <h2 data-aos="fade-up">Volunteer Opportunities</h2>
        <p class="section-intro" data-aos="fade-up" data-aos-delay="100">
            Whether you have a few hours a week or a month, there's a perfect role waiting for you. Our diverse volunteer programs ensure every skill set can make a meaningful impact.
        </p>
        
        <div class="volunteer-opportunities">
            <div class="opportunity-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-image" style="background-image: url('PDT-Course.jpg');"></div>
                <div class="card-content">
                    <h3>Animal Care & Training</h3>
                    <p>Provide exercise, socialization, and companionship to our dogs and cats. Help them stay happy and healthy while they await adoption.</p>
                    <ul>
                        <li>Daily walks and exercise</li>
                        <li>Basic training reinforcement</li>
                        <li>Enrichment and playtime</li>
                        <li>Grooming assistance</li>
                    </ul>
                </div>
            </div>
            
            <div class="opportunity-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-image" style="background-image: url('Socialization & Fostering.jpg');"></div>
                <div class="card-content">
                    <h3>Socialization & Fostering</h3>
                    <p>Help our animals become more comfortable with human interaction, especially the shy ones. Fostering opportunities also available.</p>
                     <ul>
                        <li>Gentle handling and cuddling</li>
                        <li>Interactive play sessions</li>
                        <li>Kitten and puppy fostering</li>
                        <li>Assisting with special needs animals</li>
                    </ul>
                </div>
            </div>
            
            <div class="opportunity-card" data-aos="fade-up" data-aos-delay="400">
                <div class="card-image" style="background-image: url('party-cat-celebrates-stockcake.jpg');"></div>
                <div class="card-content">
                    <h3>Events & Admin Support</h3>
                    <p>Be the backbone of our operations. Help with fundraising events, community outreach, and essential administrative tasks.</p>
                    <ul>
                        <li>Adoption event coordination</li>
                        <li>Fundraising campaign support</li>
                        <li>Community outreach booths</li>
                        <li>Data entry and office tasks</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="content-section">
        <h2 data-aos="fade-up">How to Get Started</h2>
        <p class="section-intro" data-aos="fade-up" data-aos-delay="100">Our simple four-step process is designed to get you trained and ready to make a difference as quickly as possible.</p>
        <div class="process-timeline">
            <div class="timeline-step" data-aos="fade-right">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>Submit Your Application</h4>
                    <p>Complete our simple online form to tell us about yourself, your interests, and your availability.</p>
                </div>
            </div>
            <div class="timeline-step" data-aos="fade-right" data-aos-delay="100">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>Attend Orientation</h4>
                    <p>Join an orientation session to learn about our mission, policies, and safety procedures.</p>
                </div>
            </div>
            <div class="timeline-step" data-aos="fade-right" data-aos-delay="200">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>Complete Training</h4>
                    <p>Participate in role-specific training to ensure you're comfortable and confident in your chosen area.</p>
                </div>
            </div>
            <div class="timeline-step" data-aos="fade-right" data-aos-delay="300">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h4>Start Making a Difference</h4>
                    <p>Begin your volunteer journey with the full support of our staff and experienced volunteers!</p>
                </div>
            </div>
        </div>
    </div>

    <div class="content-section" style="background-color: var(--bg-light);">
        <h2 data-aos="fade-up">Frequently Asked Questions</h2>
        <div class="faq-section" data-aos="fade-up" data-aos-delay="100">
            <div class="faq-item">
                <button class="faq-question">What is the minimum age to volunteer?</button>
                <div class="faq-answer">
                    <p>You must be at least 16 years old to volunteer. Volunteers under 18 require a signed consent form from a parent or guardian.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">What is the time commitment?</button>
                <div class="faq-answer">
                    <p>We ask for a minimum commitment of 6 months. Most roles require one recurring shift per week, typically lasting 2-3 hours.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">Do I need prior experience with animals?</button>
                <div class="faq-answer">
                    <p>No prior experience is necessary for most roles! A passion for animals and a willingness to learn are the most important qualifications.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="cta-section">
        <h2 data-aos="fade-up">Ready to Make a Difference?</h2>
        <p class="section-intro" style="color: rgba(255,255,255,0.8);" data-aos="fade-up" data-aos-delay="100">
            Your volunteer work directly contributes to saving lives and creating happy endings. Join us today and become part of something truly meaningful.
        </p>
        <div data-aos="fade-up" data-aos-delay="200">
            <form action="volunteer_landing.php" method="POST" style="display: inline;">
                <input type="hidden" name="apply_action" value="1">
                <button type="submit" class="btn btn-primary form-btn" style="margin-right: 15px;">Start Your Application</button>
            </form>
            <a href="../navbar functionalities/userLoginC.php?volunteer_redirect=1" class="btn btn-secondary">Returning Volunteer</a>
        </div>
    </div>
    
    <button id="backToTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
        });

        // Back to top button functionality
        const backToTopBtn = document.getElementById('backToTopBtn');
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        backToTopBtn.addEventListener('click', () => {
             window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // FAQ Accordion Logic
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const isActive = question.classList.contains('active');
                
                // Optional: Deactivate all other questions first
                faqQuestions.forEach(q => {
                    q.classList.remove('active');
                    q.nextElementSibling.style.maxHeight = null;
                });
                
                if (!isActive) {
                    question.classList.add('active');
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }
            });
        });

        // --- NEW: Hero Slideshow Logic ---
        const slides = document.querySelectorAll('.hero-slideshow .slide');
        let currentSlide = 0;
        let slideTimeout;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                slide.style.zIndex = i === index ? 2 : 1;
            });
            slides[index].classList.add('active');
        }

        function nextSlide() {
            showSlide((currentSlide + 1) % slides.length);
            currentSlide = (currentSlide + 1) % slides.length;
        }

        function startSlideshow() {
            slideTimeout = setInterval(nextSlide, 3600);
        }

        showSlide(currentSlide);
        startSlideshow();
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>