<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
            <title>Contact Us | Home Fur Good</title>
            <link rel="stylesheet" href="../navbar functionalities/login-register.css">
            <link rel="stylesheet" href="../navbar functionalities/navbar.css">
            <link rel="stylesheet" href="contact.css">
            <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
</head>
<body>

<?php include '../navbar functionalities/navbar.php'; ?>
<?php
require '../databaseConnection.php';
// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $error = '';
    // Restrictions
    if (strlen($full_name) > 100) {
        $error = "Full name must be 100 characters or less.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $error = "Please enter a valid email address (max 100 characters).";
    } elseif ($phone && (!preg_match('/^\d{10}$/', $phone))) {
        $error = "Phone number must be exactly 10 digits.";
    } elseif (strlen($subject) > 50) {
        $error = "Subject must be 50 characters or less.";
    } elseif (strlen($message) > 1000) {
        $error = "Message must be 1000 characters or less.";
    } elseif (!$full_name || !$email || !$subject || !$message) {
        $error = "Please fill in all required fields.";
    }
    if (!$error) {
        $sql = "INSERT INTO contact_messages (full_name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $full_name, $email, $phone, $subject, $message);
        $stmt->execute();
        $stmt->close();
        $success = true;
    }
}
?>

<div class="contact-container">
    <h1>Contact Us</h1>
    <p>Questions about adoption? Want to volunteer? Need to report animal cruelty? We're here to help.</p>

    <div class="contact-content">
        <div style="flex:1.7; min-width:400px; text-align:left; margin-right:20px; display:flex; flex-direction:column;">
            <form class="contact-form" method="POST" action="">
                <h2>Send Us a Message</h2>
                <label>Full Name:</label>
                <input type="text" name="full_name" placeholder="Your full name" required maxlength="100" pattern="[A-Za-z\s\-\.]+">
                <label>Email:</label>
                <input type="email" name="email" placeholder="Your email address" required maxlength="100">
                <label>Phone Number:</label>
                <input type="tel" name="phone" placeholder="Optional" maxlength="10" pattern="\d{10}" title="Enter a 10-digit phone number">
                <label>Subject:</label>
                <select name="subject" required>
                    <option value="">What can we help you with?</option>
                    <option value="Adoption inquiry">Adoption inquiry</option>
                    <option value="Volunteer opportunity">Volunteer opportunity</option>
                    <option value="Report animal cruelty">Report animal cruelty</option>
                    <option value="Other">Other</option>
                </select>
                <label>Message:</label>
                <textarea name="message" placeholder="Your message" rows="5" required maxlength="1000"></textarea>
                <button type="submit" name="send_message">Send Message</button>
                <?php if (isset($success) && $success): ?>
                    <div style="color:green;margin-top:1rem;">Thank you! Your message has been sent.</div>
                <?php elseif (isset($error) && $error): ?>
                    <div style="color:red;margin-top:1rem;"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
            </form>
        </div>
        <div class="contact-info">
            <h3>Email Us</h3>
            Info@homefurgood.org<br>
            Adoption@homefur.org<br>
            Emergency@homefurgood.org<p>

            <h3>Call Us</h3>
            <p><strong>Main:</strong> (555) ANIMALS<br>
           <strong>Adoptions:</strong> (555) 234-5678<br>
           <em>Mon–Sat 9am–6pm, Sun 12pm–5pm</em></p>
           <div class="contact-map">
              <h3>Find Us</h3>
              <div class="map-container">
                <iframe 
                  src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d13335.821027744942!2d26.498198700544297!3d-33.319985187171106!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e645ddb58a81999%3A0x626cd8eebd707ce8!2sS.P.C.A%20Grahamstown!5e0!3m2!1sen!2sza!4v1757328468736!5m2!1sen!2sza" 
                  width="250" height="180" style="border:0; border-radius:10px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
              </div>
            </div>
        </div>
    </div>
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

<?php include 'footer.php'; ?>
</body> 
</html>

<script>

      //checks if register_success is in the URL (INCLUDE ALL LANDING PAGES)
  window.onload = function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('register_success')) {
        closeRegisterModal();
        openLoginModal();
    }
}

</script>


