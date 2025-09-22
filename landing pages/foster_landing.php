<?php
session_start();
include '../databaseConnection.php'; 

// This block handles the "Apply to Foster" click
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_action'])) {
    
    // Check if the user is already logged in
    if (isset($_SESSION['userID'])) {
        // User IS logged in, redirect them straight to the foster application
        header("Location: ../dashboard_user/foster_application.php");
        exit;
    } else {
        // User IS NOT logged in, redirect them to the registration page.
        // The 'foster_redirect=1' tells the registration/login page where to go next.
        header("Location: ../navbar functionalities/userRegisterC.php?foster_redirect=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Foster Hero - SPCA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    
    <style>
        /* --- Global Styles & Variables --- */
        :root {
            --primary-color: #2c5aa0; /* Foster Page Blue */
            --secondary-color: #1a3d73; /* Darker Blue */
            --accent-color: #34495e; /* Professional Slate Grey/Blue */
            --text-dark: #34495e;
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
            min-height: 600px;
            height: 85vh;
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
            transition: opacity 2s cubic-bezier(0.77,0,0.175,1);
            transform: scale(1.08);
        }

        .slide.active {
            opacity: 1;
            z-index: 2;
            animation: kenburns 20s linear infinite;
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
            font-size: 3.5em;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 3px 20px rgba(0,0,0,0.4);
        }

        .hero-overlay-text p {
            font-size: 1.4em;
            font-weight: 400;
            margin-bottom: 30px;
            text-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }

        .hero-buttons {
            position: relative;
            z-index: 3;
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 40px;
        }
        
        /* --- Button Styles --- */
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
            background: var(--white);
            color: var(--accent-color);
            border-color: var(--white);
        }

        .btn-primary, .form-btn:hover {
            background: var(--white);
            color: var(--primary-color);
            border-color: var(--white);
        }

        .btn-primary:hover, .form-btn:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: scale(1.05);
        }

        .btn-secondary {
            background: transparent;
            color: var(--white);
            border-color: var(--white);
        }
        .btn-secondary:hover {
            background: var(--white);
            color: var(--primary-color);
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
            font-size: 1.15em;
        }

        /* --- Foster Types Grid --- */
        .foster-types {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .foster-card {
            background: var(--white);
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .foster-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 35px rgba(44, 62, 80, 0.15);
        }
        
        .card-image {
            height: 220px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .card-duration {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-color);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
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
            content: "\f004"; /* Font Awesome heart icon */
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
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--white);
            text-align: center;
            padding: 80px 20px;
        }

        .cta-section h2 {
            color: var(--white);
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

        /* --- Responsive Design --- */
        @media (max-width: 768px) {
            .hero-overlay-text h1 {
                font-size: 2.5em;
            }
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            .btn, .form-btn {
                min-width: 280px;
            }
        }
    </style>
</head>
<body>
    <?php include '../navbar functionalities/navbar.php'; ?>

    <div class="hero-section">
        <div class="hero-slideshow">
            <div class="slide active" style="background-image: url('background.png');"></div>
            <div class="slide" style="background-image: url('image1.jpg');"></div>
            <div class="slide" style="background-image: url('image3.jpg');"></div>
            <div class="slide" style="background-image: url('mother cat with kittens.jpg');"></div>
            <div class="slide" style="background-image: url('recovery kitten.jpg');"></div>
            <div class="slide" style="background-image: url('socializing shy dog.jpg');"></div>
        </div>
        <div class="hero-overlay-text">
            <h1>Foster a Life, Change Everything</h1>
            <p>Open your home to an animal in need. Provide temporary love that creates permanent change. Every foster story is a rescue story with a happy ending.</p>
        </div>
        <div class="hero-buttons">
            <form method="POST" action="foster_landing.php" style="display: inline;">
                <button type="submit" name="apply_action" class="btn btn-primary form-btn">Start Foster Application</button>
            </form>
            <a href='../navbar functionalities/userLoginD.php?foster_redirect=1' class="btn btn-secondary">Foster Login</a>
        </div>
    </div>
    
    <div class="content-section" style="background-color: var(--bg-light);">
        <h2 data-aos="fade-up">Foster Opportunities</h2>
        <p class="section-intro" data-aos="fade-up" data-aos-delay="100">
            Every animal has different needs, and every foster family has different strengths. Find the perfect match for your lifestyle and make a life-saving difference.
        </p>
        
        <div class="foster-types">
            <div class="foster-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-image" style="background-image: url('mother cat with kittens.jpg');">
                    <div class="card-duration">2-8 weeks</div>
                </div>
                <div class="card-content">
                    <h3>Maternity & Newborn Care</h3>
                    <p>Provide a safe, quiet space for pregnant mothers or care for orphaned babies who need bottle feeding and round-the-clock attention.</p>
                    <ul>
                        <li>Separate, quiet room required</li>
                        <li>Most hands-on and rewarding experience</li>
                        <li>Watch babies grow from helpless to adoptable</li>
                        <li>Full support and supplies provided</li>
                    </ul>
                </div>
            </div>
            
            <div class="foster-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-image" style="background-image: url('recovery kitten.jpg');">
                    <div class="card-duration">1-6 weeks</div>
                </div>
                <div class="card-content">
                    <h3>Medical Recovery Foster</h3>
                    <p>Help animals heal after surgery, illness, or injury. Your calm home environment speeds recovery and reduces shelter stress.</p>
                     <ul>
                        <li>Simple medication administration</li>
                        <li>Quiet environment for healing</li>
                        <li>Regular vet check-ups included</li>
                        <li>Perfect for patient, nurturing families</li>
                    </ul>
                </div>
            </div>
            
            <div class="foster-card" data-aos="fade-up" data-aos-delay="400">
                <div class="card-image" style="background-image: url('socializing shy dog.jpg');">
                    <div class="card-duration">4-12 weeks</div>
                </div>
                <div class="card-content">
                    <h3>Behavioral & Socialization</h3>
                    <p>Transform shy, fearful, or under-socialized animals into confident, loving companions ready for their forever homes.</p>
                    <ul>
                        <li>Build trust and confidence</li>
                        <li>Basic training and socialization</li>
                        <li>Professional behavioral support</li>
                        <li>See incredible transformations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="content-section">
        <h2 data-aos="fade-up">Your Foster Journey</h2>
        <p class="section-intro" data-aos="fade-up" data-aos-delay="100">We make fostering simple and supported. From application to placement, we're with you every step of the way.</p>
        <div class="process-timeline">
            <div class="timeline-step" data-aos="fade-right">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>Complete Foster Application</h4>
                    <p>Tell us about your home, lifestyle, and fostering preferences. The process is thorough to ensure the best matches.</p>
                </div>
            </div>
            <div class="timeline-step" data-aos="fade-right" data-aos-delay="100">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>Home Visit & Approval</h4>
                    <p>A friendly volunteer visits your home to ensure it's safe and suitable for fostering. This also answers any questions you might have.</p>
                </div>
            </div>
            <div class="timeline-step" data-aos="fade-right" data-aos-delay="200">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>Foster Training & Orientation</h4>
                    <p>Attend a comprehensive training session covering animal care, emergency procedures, and what to expect as a foster family.</p>
                </div>
            </div>
            <div class="timeline-step" data-aos="fade-right" data-aos-delay="300">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h4>Meet Your Foster Match</h4>
                    <p>We'll match you with an animal that fits your preferences and lifestyle. When the right one needs you, we'll call!</p>
                </div>
            </div>
        </div>
    </div>

    <div class="content-section" style="background-color: var(--bg-light);">
        <h2 data-aos="fade-up">Foster Family FAQs</h2>
        <div class="faq-section" data-aos="fade-up" data-aos-delay="100">
            <div class="faq-item">
                <button class="faq-question">What does SPCA provide to foster families?</button>
                <div class="faq-answer">
                    <p>We provide everything: food, supplies, toys, bedding, carrier, and all medical care. Foster families provide love, care, and a temporary home.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">How long do animals stay in foster care?</button>
                <div class="faq-answer">
                    <p>It varies by need: newborns may stay 6-8 weeks, medical recovery 1-4 weeks, and socialization cases 4-12 weeks. We work with your schedule.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">Can I foster if I have pets?</button>
                <div class="faq-answer">
                    <p>Yes! Your pets must be up-to-date on vaccinations and spayed/neutered. We'll help determine the best foster matches for multi-pet households.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">What if I get too attached to my foster animal?</button>
                <div class="faq-answer">
                    <p>Foster failures are actually foster successes! Many foster families adopt their animals. However, remember that each animal you foster opens space for another life to be saved.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">Is there support if problems arise?</button>
                <div class="faq-answer">
                    <p>Absolutely. Our foster coordinator and veterinary team are available 24/7 for emergencies and guidance. You're never alone in your foster journey.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="cta-section">
        <h2 data-aos="fade-up">Ready to Save a Life?</h2>
        <p class="section-intro" style="color: rgba(255,255,255,0.9);" data-aos="fade-up" data-aos-delay="100">
            Fostering is the most direct way to save an animal's life. Your temporary love gives them the chance to find their forever home. Start your foster journey today.
        </p>
        <div data-aos="fade-up" data-aos-delay="200">
            <form method="POST" action="foster_landing.php" style="display: inline;">
                <button type="submit" name="apply_action" class="btn btn-primary form-btn" style="margin-right: 15px;">Begin Foster Application</button>
            </form>
            <a href="../navbar functionalities/userLoginC.php?foster_redirect=1" class="btn btn-secondary">Existing Foster Login</a>
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
                
                // Close all other questions
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

        // Hero Slideshow Logic
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
            slideTimeout = setInterval(nextSlide, 4000); // Change slide every 4 seconds
        }

        showSlide(currentSlide);
        startSlideshow();

        // Pause slideshow when user hovers over hero section
        const heroSection = document.querySelector('.hero-section');
        heroSection.addEventListener('mouseenter', () => {
            clearInterval(slideTimeout);
        });
        heroSection.addEventListener('mouseleave', () => {
            startSlideshow();
        });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>