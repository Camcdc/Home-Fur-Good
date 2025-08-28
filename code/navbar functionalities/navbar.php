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
                <a href="../landing pages/newHelp.php">How to help<i class="fa fa-caret-down"></i></a>
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
        <div id="registerSuccessMessage">Account successfully created! Please log in.</div>
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
        <form action="../navbar functionalities/userRegister.php" method="POST">


            <!--Sends URL to the userRegister.php-->        
            <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

            <label for="Fname">First Name</label>
            <input type="text" id="Fname" name="Fname" placeholder="First Name" required>

            <label for="Sname">Surname</label>
            <input type="text" id="Sname" name="Sname" placeholder="Surname" required>

            <label for="Email">Email</label>
            <input type="email" id="Email" name="Email" placeholder="Email" required>

            <label for="Password">Password</label>
            <input type="password" id="Password" name="Password" placeholder="Password" required>

            <label for="DateOfBirth">Date of Birth</label>
            <input type="date" id="DateOfBirth" name="DateOfBirth" placeholder="Date of Birth" required>

            <label for="CellNumber">Cell Number</label>
            <input type="text" id="CellNumber" name="CellNumber" placeholder="Cell Number" required>

            <label for="Address">Address</label>
            <input type="text" id="Address" name="Address" placeholder="Address" required>

            <label for="Role">Role</label>
            <select id="Role" name="Role" required>
              <option value="" disabled selected>Select your role</option>
              <option value="User">User</option>
              <option value="Veterinarian">Veterinarian</option>
              <option value="Administrator">Administrator</option>
              <option value="Staff">Staff</option>
              <option value="Inspector">Inspector</option>  
            </select>

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

         window.onload = function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('register_success')) {
            closeRegisterModal();
            openLoginModal();
        }
    }
        
        function handleRegister(event) {
        event.preventDefault();
        const firstname = event.target.Fname.value;
        const surname = event.target.Sname.value;
        const email = event.target.Email.value;
        const password = event.target.Password.value;
        const dateofbirth = event.target.DateOfBirth.value;
        const cellnumber = event.target.CellNumber.value;
        const address = event.target.Address.value;

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



