<?php
require '../databaseconnection.php';
include '../navbar functionalities/navbar.php';
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makhanda SPCA | Every Life Matters</title>
     <link rel="stylesheet" href="../navbar functionalities/login-register.css">
    <link rel="stylesheet" href="../navbar functionalities/navbar.css">
    <link rel="stylesheet" type="text/css" href="homepage.css">
</head>
<body>
<script src="homepage.js"></script>
    <!-- Hero Section -->
    <header>
        <div class="hero-image">
            <div class="hero-text">
                <h1>Every Life Matters</h1>
                <h2>Animals deserve a second chance too. Find your loyal companion today.</h2>
                <nav>
                    <a href="../Final - HomeFurGood/landing%20pages/browseAnimals.php"><button>Adopt Now</button></a>
                    <a href="../Final - HomeFurGood/landing%20pages/register.php"><button>Donate Today</button></a>
                    <a href="../Final - HomeFurGood/landing%20pages/register.php"><button>Report Abuse</button></a>
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
                <a href="../Final - HomeFurGood/landing%20pages/about.php"><button>Learn More About Us</button></a>
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
        <!-- Animals to adopt-->
         <h3>Animals Available for Adoption</h3>
        <section class="row full-width">
            
            <article class="column">
                <div class="animal-cards">
                    <div class="animal-card">
                        <img src=".../pictures/cat (5).jpg" alt="Dog 1" class="card-img">
                        <h4>Max</h4>
                        <p>7-year-old Labrador mix, friendly and energetic.</p>
                        <a href="browseAnimals.php"><button>Adopt Max</button></a>
                    </div>
                </article>
                <article class="column">
                    <div class="animal-card">
                        <img src="images/cat1.jpg" alt="Cat 1" class="card-img">
                        <h4>Luna</h4>
                        <p>3-year-old domestic shorthair, affectionate and playful.</p>
                        <a href="browseAnimals.php"><button>Adopt Luna</button></a>
                    </div>
                </article>
            <article class="column">
                    <div class="animal-card">
                        <img src="images/rabbit1.jpg" alt="Rabbit 1" class="card-img">
                        <h4>Bun Bun</h4>
                        <p>1-year-old rabbit, gentle and loves to cuddle.</p>
                        <a href="browseAnimals.php"><button>Adopt Bun Bun</button></a>
                    </div>
            </article>
            <article class="column">
                    <div class="animal-card">
                        <img src="images/dog2.jpg" alt="Dog 2" class="card-img">
                        <h4>Charlie</h4>
                        <p>4-year-old Beagle, curious and loves to explore.</p>
                        <a href="browseAnimals.php"><button>Adopt Charlie</button></a>
                    </div>
                </div>
            </article>
              
        </section>
<a href="browseAnimals.php"><button>See All Animals</button></a>
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

<?php
include '/landing pages/footer.php'
?>
<button onclick="topFunction()" id="backToTopBtn" title="Go to top">⬆️Back to top</button>    
</body>
</html>
