<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Help - SPCA Grahamstown</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="login-register.css">
    <link rel="stylesheet" href="helpPage.css">
</head>
<body>
    <?php
    include 'navbar.php';
    ?>
    
    <div class="container">
        <!-- Header -->
        <header>
            <h1>How to Help</h1>
            <p class="intro-text">At the SPCA, every act of support helps us continue our vital work in caring for animals in need. We offer several ways for you to get involved and make a real difference in the lives of our animals and the community we serve.</p>
            
            <div class="image-placeholder">
                📸 PLACE HERO IMAGE HERE: Large banner image showing happy rescued animals, volunteers caring for pets, or SPCA facility exterior (Recommended size: 1000x400px)
            </div>
        </header>

        <!-- Donate Section -->
        <section id="donate">
            <h2><i class="fas fa-heart topic-icon"></i>Donate</h2>
            
            <div class="image-placeholder">
                📸 PLACE IMAGE HERE: Animals receiving medical care, feeding, or rehabilitation (800x300px)
            </div>
            
            <p>Your financial support is the cornerstone of our ability to provide comprehensive care for animals in need. Every donation, regardless of size, directly impacts the lives of vulnerable animals in our community. Your generosity enables us to provide essential medical treatment, nutritious food, safe shelter, and behavioral rehabilitation.</p>
            
            <p>When you donate to the SPCA Grahamstown, you're not just giving money – you're giving hope. You're funding life-saving surgeries, providing warm bedding for a frightened puppy, ensuring that no animal goes hungry, and supporting our dedicated staff who work tirelessly to give every animal the care they deserve.</p>

            <h3>Sponsor an Expense</h3>
            <p>You can make a targeted impact by sponsoring specific expenses that directly benefit our animals:</p>
            
            <div class="sponsor-options">
                <div class="sponsor-item">
                    <strong>Pledge Support</strong>
                    <div class="sponsor-price">R100.00</div>
                </div>
                <div class="sponsor-item">
                    <strong>Puppy Pack</strong>
                    <div class="sponsor-price">R950.00</div>
                </div>
                <div class="sponsor-item">
                    <strong>R10 Donation</strong>
                    <div class="sponsor-price">R10.00</div>
                </div>
                <div class="sponsor-item">
                    <strong>Tick & Flea Treatment</strong>
                    <div class="sponsor-price">R100.00</div>
                </div>
            </div>

            <h4>Banking Details:</h4>
            <ul>
                <li><strong>Account Holder:</strong> SPCA Grahamstown</li>
                <li><strong>Bank:</strong> [Bank Name]</li>
                <li><strong>Account No:</strong> [Account Number]</li>
                <li><strong>Reference:</strong> [Donation Reference]</li>
            </ul>

            <a href="donate.php" class="btn">Make a Donation</a>
        </section>

        <!-- Volunteer Section -->
        <section id="volunteer">
            <h2><i class="fas fa-hands-helping topic-icon"></i>Volunteer</h2>
            
            <div class="image-placeholder">
                📸 PLACE IMAGE HERE: Volunteers working with animals, cleaning kennels, or at charity shop (800x300px)
            </div>
            
            <p>Make a tangible difference by volunteering your time and skills at the SPCA Grahamstown. Whether you can spare a few hours a week or dedicate regular time to our cause, your contribution is invaluable in helping us care for animals and support adoptions.</p>
            
            <p>Our volunteers are the heart of our organization. They provide hands-on care for animals, help with daily operations, and play a crucial role in connecting animals with their forever homes. No matter your background or experience level, there's a way for you to contribute meaningfully to our mission.</p>

            <h3>Volunteer Opportunities Include:</h3>
            <ul>
                <li>Animal care and enrichment activities</li>
                <li>Dog walking and exercise programs</li>
                <li>Assisting with feeding and basic medical care</li>
                <li>Administrative support and data entry</li>
                <li>Photography for adoption profiles</li>
                <li>Transportation for animals to vet appointments</li>
                <li>Community outreach and education programs</li>
                <li>Maintenance and facility improvement projects</li>
            </ul>

            <a href="volunteer.php" class="btn">Apply to Volunteer</a>
        </section>

        <!-- Adopt/Foster Section -->
        <section id="adopt-foster">
            <h2><i class="fas fa-home topic-icon"></i>Adopt/Foster</h2>
            
            <div class="image-placeholder">
                📸 PLACE IMAGE HERE: Happy families with adopted pets, cute adoptable animals waiting for homes (800x300px)
            </div>
            
            <p>Give a rescued animal a loving forever home through adoption, or provide temporary care through our foster program. Every adoption saves two lives – the one you adopt and the one that takes their place in our shelter. Our animals come from various backgrounds, but they all share one thing in common: they're ready to love and be loved.</p>
            
            <p>Our adoption process is designed to ensure the best match between you and your new companion. We provide comprehensive information about each animal's personality, medical history, and specific needs to help you make an informed decision.</p>

            <h3>What We Provide:</h3>
            <ul>
                <li>Comprehensive health checks and vaccinations</li>
                <li>Behavioral assessments and training recommendations</li>
                <li>Spaying/neutering services</li>
                <li>Microchipping for identification</li>
                <li>Post-adoption support and guidance</li>
                <li>Meet-and-greet sessions with potential pets</li>
            </ul>

            <h3>Foster Program:</h3>
            <p>If you're not ready for permanent adoption but want to help, consider fostering. Foster families provide temporary homes for animals who need extra care, are too young for adoption, or are recovering from medical treatment. We provide all necessary supplies, medical care, and support – you provide the love and temporary home.</p>

            <a href="adopt.php" class="btn">View Available Pets</a>
        </section>

        <!-- Footer -->
        <footer>
            <p>Copyright © 2025 SPCA Grahamstown. All Rights Reserved.</p>
            <p><strong>Supported By:</strong> Community donations and local business partnerships</p>
        </footer>
    </div>
</body>
</html>
