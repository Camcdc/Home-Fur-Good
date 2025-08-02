 
        <link rel="stylesheet" href = "navbar.css">
        <link rel="stylesheet" href = "login.css">

    <div class= "topnav">
        <div class ="topnav-left">
            <div class="brand">
                <img src="logo/Log.jpg" alt="Logo" class=logo>
                <h3>Home Fur Good</h3>
            </div>

            <a href="Home">Home</a>

            <div class="dropdown">
                <a href="navbar.php">Adopt<i class="fa fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="Browse Animals">Browse Animals</a>
                    <a href="Adoption Process">Adoption Process</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="crueltyReport">Report Cruelty<i class="fa fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href='CrueltyReport.html'>Create Report</a>
                    <a href="View existing reports">View existing reports</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="navbar.php">How to help<i class="fa fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="Browse Animals">Volunteer</a>
                    <a href="Adoption Process">Foster an animal</a>
                </div>
            </div>

            <div>
                <a href='about.php'>About Us</a>
            </div>
            <div>
                <a href='contact.php'>Contact Us</a>
            </div>
        </div>

        <div class ="topnav-center">
            <a id = 'Donate' href="donate" class="split">Donate</a>
        </div>

        <div class = "topnav-right">
            <a href="#" onclick="openModal()" class="split">Login</a>
            <a href="register" class="split">Register</a>
        </div>
    </div>


    <div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Login</h2>
        <form onsubmit="handleLogin(event)">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
        </form>
    </div>
    </div>

<!--JAVASCRIPT-->
<script>
        function openModal() {
        document.getElementById('loginModal').style.display = 'block';
        }

        function closeModal() {
            console.log("closing modal");
        document.getElementById('loginModal').style.display = 'none';
        }

        function handleLogin(event) {
        event.preventDefault(); 
        const username = event.target.username.value;
        const password = event.target.password.value;

        
        console.log("Login submitted:", username, password);

        closeModal();
        }

        window.onclick = function(event) {
        const modal = document.getElementById('loginModal');
        if (event.target === modal) {
            modal.style.display = "none";
        }
        }
</script>



