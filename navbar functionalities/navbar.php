<head>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href = "login-register.css">
</head>        
<div class="body-content">
    <div class= "topnav">
        <div class ="topnav-left">
            <div class="brand">
                <img src="../pictures/logo/Log.jpg" alt="Logo" class=logo>
                <h3>Home Fur Good</h3>
            </div>

            <a href="Home">Home</a>

            <div class="dropdown">
                <a href="">Adopt<i class="fa fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="Browse Animals">Browse Animals</a>
                    <a href="Adoption Process">Adoption Process</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="crueltyReport">Report Cruelty<i class="fa fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href='CrueltyReportUser.php'>Create Report</a>
                    <a href="View existing reports">View existing reports</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="../landing pages/how to help.php">How to help<i class="fa fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="Browse Animals">Volunteer</a>
                    <a href="Adoption Process">Foster an animal</a>
                </div>
            </div>

            <div>
                <a href='../landing pages/about.php'>About Us</a>
            </div>
            <div>
                <a href='../landing pages/contact.php'>Contact Us</a>
            </div>
        </div>

        <div class ="topnav-center">
            <a id = 'Donate' href="donate" class="split">Donate</a>
        </div>

        <div class = "topnav-right">
            <a href="#" onclick='openLoginModal()' class="split">Login</a>
            <a href="#" onclick='openRegisterModal()' class="split">Register</a>
        </div>
    </div>
</div>

    <div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <h2>Login</h2>
        <form action="../navbar functionalities/login.php" method="post">
        <label for="email">Email</label>
        <input type="text" id="loginEmail" name="loginEmail" required>

        <label for="password">Password</label>
        <input type="password" id="loginPassword" name="loginPassword" required>

        <button type="submit">Login</button>
        </form>
    </div>
    </div>



<!--Register Modal-->

    <div id="registerModal" class="modal">
        <div class="modal-content">
        <span class="close" onclick="closeRegisterModal()">&times;</span>
        <h3>Register</h3>
        <form onsubmit="handleRegister(event)">

            <label for="firstname">First Name</label>
            <input type="text" id="fname" name="firstname" placeholder="First Name" required>

            <label for="surname">Surname</label>
            <input type="text" id="sname" name="surname" placeholder="Surname" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <label for="dateofbirth">Date of Birth</label>
            <input type="date" id="dateofbirth" name="dateofbirth" placeholder="Date of Birth" required>

            <label for="cellnumber">Cell Number</label>
            <input type="text" id="cellnumber" name="cellnumber" placeholder="Cell Number" required>

            <label for="address">Adress</label>
            <input type="text" id="address" name="address" placeholder="address" required>

            <button type="submit">Register</button>
        </form>
    </div>
</div>



    


<!--JAVASCRIPT-->
<script>

    //Login
        function openLoginModal() {
        document.getElementById('loginModal').style.display = 'block';
        }

        function closeLoginModal() {
        document.getElementById('loginModal').style.display = 'none';
        }
        


//Register
       
       function openRegisterModal() {
        document.getElementById('registerModal').style.display = 'block';
        }

        function closeRegisterModal() {
        document.getElementById('registerModal').style.display = 'none';
        }
        
        function handleRegister(event) {
        event.preventDefault();
        const firstname = event.target.firstname.value;
        const surname = event.target.surname.value;
        const email = event.target.email.value;
        const password = event.target.password.value;
        const dateofbirth = event.target.dateofbirth.value;
        const cellnumber = event.target.cellnumber.value;
        const address = event.target.address.value;

        console.log("Register submitted:", firstname, surname, email, password, dateofbirth, cellnumber, address);

        }


        window.onclick = function(event) {
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        
        
        if (event.target === loginModal) {
            closeLoginModal();

        }if (event.target === registerModal) {
            closeRegisterModal();
        }
        }

</script>



