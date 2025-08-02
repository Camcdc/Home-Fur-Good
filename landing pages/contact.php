<?php include '../navbar functionalities/navbar.php'; ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
            <title>Contact | Home Fur Good</title>
            <link rel="stylesheet" href="../navbar functionalities/login-register.css">
            <link rel="stylesheet" href="../navbar functionalities/navbar.css">
            <link rel="stylesheet" href="contact.css">
</head>
<body>

<div class="contact-container">
    <h1>Contact Us</h1>
    <p>Questions about adoption? Want to volunteer? Need to report animal cruelty? We're here to help.</p>

    <div class="contact-content">
        <form class="contact-form">
            <h2>Send Us a Message</h2>
            <label>Full Name:</label>
            <input type="text" placeholder="Your full name" required>
            <label>Email:</label>
            <input type="email" placeholder="Your email address" required>
            <label>phone number</label>
            <input type="tel" placeholder="Optional" optional>

            <label> Subject:</label>
            <select>
                <option>What can we help you with?</option>
                <option>Adoption inquiry</option>
                <option>Volunteer opportunity</option>
                <option>Report animal cruelty</option>
                <option>Other</option>
            </select>

            <label>Message:</label>
            <textarea placeholder="Your message" rows="5" required></textarea>
            <button type="submit">Send Message</button>
</form>
        
<div class="contact-info">
            <h3>Email Us</h3>
            Info@homefurgood.org<br>
            Adoption@homefur.org<br>
            Emergency@homefurgood.org<p>

            <h3>Call Us</h3>
            <p><strong>Main:</strong> (555) ANIMALS<br>
           <strong>Adoptions:</strong> (555) 234-5678<br>
           <em>Mon–Sat 9am–6pm, Sun 12pm–5pm</em></p>
</div>
</div>
</div>
</body> 
</html>


