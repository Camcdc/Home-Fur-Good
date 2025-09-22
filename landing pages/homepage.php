<?php
require '../databaseconnection.php';

//progress bar for donations
$sql="SELECT sum(amount) FROM donations";
$result=$conn->query($sql);
$totalDonation = 0;
if ($result && $row = $result->fetch_assoc()) {
    $totalDonation = $row['sum(amount)'];
}

$goalAmount = 100000;
while ($totalDonation > $goalAmount) {
    $goalAmount += 50000;

}
$sql1="SELECT * FROM animal WHERE status='Available' AND isDeleted=0 ";
$result1=$conn->query($sql1);
$featuredAnimals = [];
if ($result1 && $result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        $featuredAnimals[] = $row;
    
    }
    
}



$maxLength = 100; // Maximum number of characters to show for the description

$sql1 = "SELECT * FROM animal WHERE status='Available' AND isDeleted=0 LIMIT 6";
$result1 = $conn->query($sql1);
$featuredAnimals = [];

if ($result1 && $result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        // Truncate description for each animal
        $description = htmlspecialchars($row['description']);
        $shortDescription = (strlen($description) > $maxLength) ? substr($description, 0, $maxLength) . '...' : $description;
        
        // Add the animal data with short description
        $row['shortDescription'] = $shortDescription;
        $featuredAnimals[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makhanda SPCA | Every Life Matters</title>
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <link rel="stylesheet" href="../landing pages/footer.css">
    <link rel="stylesheet" type="text/css" href="homepage.css">
    <link rel="stylesheet" href="../navbar functionalities/login-register.css">
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>
    <?php
    include '../navbar functionalities/navbar.php';
    ?>
<script src="homepage.js"></script>
    <!-- Hero Section -->
    <header>
        <div class="hero-image">
            <div class="hero-text">
                <h1>Every Life Matters</h1>
                <h2>Animals deserve a second chance too. Find your loyal companion today.</h2>
                <nav>
                    <a href="browseAnimals.php"><button>Adopt Now</button></a>
                    <button type="button" onclick="window.location.href='../navbar functionalities/userRegisterC.php'">Donate Today</button>
                    <button type="button" onclick="window.location.href='../dashboard_user/CrueltyReportInfor.php'">Report Abuse</button>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <!-- Welcome Section -->
        <section class="column full-width">
            <article class="column full-width">
                <h3>Welcome to Makhanda SPCA</h3>
                <p>
                    Since our founding, Makhanda SPCA has worked tirelessly to rescue, protect, and rehome animals in need. 
                    We believe that every animal deserves love, dignity, and the chance to live a happy life.  
                </p>
                <p>
                    Whether you are here to adopt, volunteer, donate, or simply learn more about us, 
                    thank you for being part of our mission to make a difference in the lives of animals.
                </p>
                <a href="../landing%20pages/about.php"><button>Learn More About Us</button></a>
            </article>
        </section>

        <!-- Key Actions -->
        <section class="row full-width">
            <article class="column">
                <h3>Browse Animals</h3>
                <p>Discover our wonderful animals waiting for loving homes.</p>
                <img src="images/Rescue.jpg" alt="Rescued animals" class="card-img">
                
            </article>

            <article class="column">
                <h3>Report Animal Cruelty</h3>
                <p>Be their voice — help us stop neglect and cruelty.</p>
                <img src="images/report.jpg" alt="Report Animal Cruelty" class="card-img">
               
            </article>

            <article class="column">
                <h3>Get Involved</h3>
                <p>Volunteer or donate — every effort makes a difference.</p>
                <img src="images/volunteer.jpg" alt="Volunteer with us" class="card-img">
                
            </article>

            <article class="column">
                <h3>Donate</h3>
                <p>Your contributions provide food, shelter, and medical care for our animals.</p>
                <img src="images/donate.jpg" alt="Donate to SPCA" class="card-img">
                
            </article>
</section>

<!-- Staff & Volunteers -->
<section class="row full-width">
<article class="column" style="flex-basis: 100%; max-width: 100%;">
<h3>Meet Our Heroes</h3>
<p>Our dedicated staff and volunteers make everything possible.</p>
<div class="team">
<div class="team-member">
<img src="images/ayanda.jpg" alt="Volunteer">
<p><strong>Ayanda</strong> – fostered over 30 animals and continues to save lives.</p>
</div>
<div class="team-member">
<img src="images/team.jpg" alt="Volunteer">
<p><strong>Dumi and Zintle </strong> – passionate about community outreach and education.</p>
</div>
</div>
</article>
</section>

<!-- Donation Transparency Put file in PHP then calculate all amount to get progress value-->
<section class="row full-width">
<article class="column full-width">
<h3>Where Your Donations Go</h3>
<ul class="donation-list">          
<li>50% → Medical Care</li>
<li>30% → Food & Shelter</li>
<li>15% → Rescue Operations</li>
<li>5% → Administration</li>
</ul>
<progress value="<?php echo ($totalDonation / $goalAmount) * 100; ?>" max="100"></progress>
<p>Current Goal: <?php echo " R$goalAmount raised towards veterinary equipment."; ?></p>
</article>
</section>



<!-- Partnerships -->
<section class="row full-width">
<article class="column" style="flex-basis: 100%; max-width: 100%;">
<h3>Our Partners</h3>
<p>We are proud to work with local businesses and organizations.</p>
<div class="partners">
<img src="images/vet_clinic.jpg" alt="Vet Clinic">
<img src="images/university.png" alt="University Partner">
</div>
</article>
</section>
<!--Animals Available-->
<h3>Animals Available for Adoption</h3>
<section class="animal-grid">
  <?php foreach ($featuredAnimals as $animal): ?>
    <article class="animal-card">
      <div class="card-content">
        <!-- Animal Image -->
        <img src="../pictures/animals/<?php echo htmlspecialchars($animal['picture']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>" class="card-img">

        <!-- Animal Name and Age -->
        <p><strong><?php echo htmlspecialchars($animal['name']); ?></strong> </p>

        <!-- Animal Description -->
        <div class="animal-description">
          <span class="limited-text">
            <?php echo $shortDescription; ?>
          </span>
        </div>

        <!-- Adopt Button -->
        <a href="animal1.php?id=<?php echo urlencode($animal['animalID']); ?>">
          <button>Adopt <?php echo htmlspecialchars($animal['name']); ?></button>
        </a>
      </div>
    </article>
  <?php endforeach; ?>
</section>

        

        <!-- Impact Numbers -->
        <section class="row">
            <article class="column">
                <h3>💖 1,200+</h3>
                <p>Animals Rescued</p>
            </article>
            <article class="column">
                <h3>🏡 900+</h3>
                <p>Forever Homes Found</p>
            </article>
            <article class="column">
                <h3>🤝 150+</h3>
                <p>Active Volunteers</p>
            </article>
            <article class="column">
                <h3>📅 50+</h3>
                <p>Events Hosted</p>
            </article>
        </section>
        <!--Success Stories-->
        <section class="row full-width">
            <article class="column full-width">
                <h3>Success Stories</h3>
                <div class="success-stories">
                    <div class="story-card">
                        <img src="images/success1.jpg" alt="Success Story 1" class="card-img">
                        <h4>From Stray to Star: Bella's Journey</h4>
                        <p>Bella was found abandoned and scared. After months of care, she blossomed into a loving companion now thriving in her new home with the Nkosi family in Johannesburg.</p>
                        <p>Builds Eco</p>
                    </div>
            </article>
            <article class="column">
                    <div class="story-card">
                        <img src="images/success2.jpg" alt="Success Story 2" class="card-img">
                        <h4>Rex's Rescue: A Second Chance</h4>
                        <p>Rex was rescued from a neglectful situation. With medical treatment and lots of love from our volunteers, he found his forever home with the Mthembu family in Durban.</p>
                        <p>Builds Eco</p>
                        </div>
            </article>
        </section>
<!-- Map -->
<section class="row full-width">
  <article class="column full-width">
    <h3>Find Us</h3>
    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d13335.821027744942!2d26.498198700544297!3d-33.319985187171106!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e645ddb58a81999%3A0x626cd8eebd707ce8!2sS.P.C.A%20Grahamstown!5e0!3m2!1sen!2sza!4v1757328468736!5m2!1sen!2sza" > 
      </iframe>
    </div>
  </article>
</section>


        <!-- Testimonials -->
        <section class="row full-width">
            <article class="column full-width">
                <h3>What Our Adopters Say</h3>
                <blockquote>
                    "Adopting from Makhanda SPCA was the best decision for our family. Our new friend brings us joy every day!"
                    <cite>- Sarah & Family</cite>
                </blockquote>
                <blockquote>
                    "Volunteering here has been so rewarding. The staff truly care about every animal."
                    <cite>- Thabo, Volunteer</cite>
                </blockquote>
            </article>
        </section>

        <!-- Events -->
        <section class="row full-width">
            <article class="column full-width">
                <h3>Upcoming Events</h3>
                <ul>
                    <li><strong>Adoption Day:</strong> September 15, 2025</li>
                    <li><strong>Fundraiser Gala:</strong> October 10, 2025</li>
                    <li><strong>Volunteer Orientation:</strong> Every Saturday at 10am</li>
                    <li><strong>Pet Reunion: </strong> November 20, 2025</li>
                </ul>
            </article>
        </section>

        <!-- FAQ Section -->
        <section class="row full-width">
            <article class="column full-width">
                <h3>Frequently Asked Questions</h3>
                <details>
                    <summary>What is the adoption fee?</summary>
                    <p>Adoption fees vary depending on the animal, but they cover vaccinations, microchipping, and sterilization.</p>
                </details>
                <details>
                    <summary>Can I volunteer without experience?</summary>
                    <p>Yes! We provide training and guidance. All you need is compassion and commitment.</p>
                </details>
                <details>
                    <summary>How do I report cruelty?</summary>
                    <p>You can report online using our cruelty report form, or call our hotline available 24/7.</p>
                </details>
            </article>
        </section>

      
    </main>

<?php include 'footer.php'; ?>
<button onclick="topFunction()" id="backToTopBtn" title="Go to top">⬆️Back to top</button>    
</body>
</html>
