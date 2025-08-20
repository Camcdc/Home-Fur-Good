<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us | Home Fur Good</title>
  <link rel="stylesheet" href="../navbar functionalities/login-register.css">
  <link rel="stylesheet" href="../navbar functionalities/navbar.css">
  <link rel="stylesheet" href="about.css"> 
</head>
<body>
  <?php include '../navbar functionalities/navbar.php'; ?>

  <div class="about-container">
    <section class="intro">
      <h1>About Us </h1>
      <p>We're passionate about creating innovative solutions that transform the way animals are treated.</p>
    </section>

    <section class="mission-values">
      <div class="mission">
        <h2>Our Mission</h2>
        <p>The Makhanda SPCA is dedicated to protecting animals from cruelty, promoting responsible pet ownership, and providing shelter and care for animals in need. Through compassionate service and community involvement, the SPCA aims to rescue, rehabilitate, and rehome animals while educating the public on animal welfare.</p>
      
      </div>

      <div class="values">
        <h2>Our Values</h2>
        <ul>
          <li>• Animal Welfare First
            <br>
            The well-being and dignity of every animal is our top priority.</li>
          <li>• Compassion and Respect
            <br> 
            We treat all animals and people with kindness, understanding, and respect.</li>
          <li>• Integrity and Transparency
            <br>
            We handle every case, adoption, and report with honesty and openness.</li>
          <li>• Community Engagement
            <br>
            We actively involve the public through education, volunteering, and adoption initiatives.</li>
        </ul>
      </div>
    </section>

    <section class="team">
      <h2>Meet Our Team</h2>
      <p>The passionate professionals behind our success</p>
      <div class="team-members">
        <div class="member">
          <div class="circle">SJ</div>
          <h3>Soms</h3>
          <p class="role">CEO</p>
          <p>Has alot of experince</p>
        </div>
        <div class="member">
          <div class="circle">MB</div>
          <h3>Michael Bradshaw</h3>
          <p class="role">Project Manager</p>
          <p>Technical expert passionate about building scalable solutions.</p>
        </div>
        <div class="member">
          <div class="circle">ER</div>
          <h3>Cameron Campbell</h3>
          <p class="role">Head of Design</p>
          <p>Creative professional focused on exceptional user experiences.</p>
        </div>
      </div>
    </section>

    <section class="cta">
      <h2>Ready to Work Together?</h2>
      <p>Contact us to start making a difference together.</p>
      <a href="contact.php" class="btn-contact">Contact Us</a>
    </section>
  </div>


    <!--success message for registration (INCLUDE IN ALL LANDING PAGES)-->  
      <?php if (isset($_GET['register_success'])): ?>
    <script>
      const loginModal = document.getElementById('loginModal');
      if (loginModal) loginModal.style.display = 'block';

      const successMsg = document.getElementById('registerSuccessMessage');
      if (successMsg) successMsg.style.display = 'block';
    </script>
    <?php endif; ?>


</body>
</html>

<script>

  //checks if register_success is in the URL (INCLUDE IN ALL LANDING PAGES)
  window.onload = function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('register_success')) {
        closeRegisterModal();
        openLoginModal();
    }
}

</script>
