


<div class="sidebar">
  <h2>Admin Panel</h2>

  <button class="dropdown-btn">Dashboard</button>

  <button class="dropdown-btn">
      <span>Animals</span>
      <i class="fa-solid fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <a href="viewAllAnimals.php">View all Animals</a>
    <a href="animalRegistrationForm.php">Register New Animal</a>
  </div>

  <button class="dropdown-btn">Applications <i class="fa-solid fa-caret-down"></i></button>
  <div class="dropdown-container">
    <a href="#">Adoption Applications</a>
    <a href="#">Volunteer Application</a>
    <a href="#">Foster Application</a>
  </div>

  <button class="dropdown-btn">Reports <i class="fa-solid fa-caret-down"></i></button>
  <div class="dropdown-container">
    <a href="displayAllrecords.php">Cruelty Reports</a>
  </div>

  <button class="dropdown-btn">Analytics <i class="fa-solid fa-caret-down"></i></button>
  <div class="dropdown-container">
    <a href="#">Placeholders</a>
  </div>

  <button class="dropdown-btn">Logout</button>
</div>

<script src="https://kit.fontawesome.com/0dfbacd3e2.js" crossorigin="anonymous"></script>

<script>

  document.querySelectorAll('.dropdown-btn').forEach(button => {
    button.addEventListener('click', () => {
      const dropdown = button.nextElementSibling;

      document.querySelectorAll('.dropdown-container').forEach(container => {
        if (container !== dropdown) container.classList.remove('show');
      });

      document.querySelectorAll('.dropdown-btn').forEach(btn => {
        if (btn !== button) btn.classList.remove('active');
      });

      if (dropdown) {
        dropdown.classList.toggle('show');
        button.classList.toggle('active');
      }
    });
  });
</script>
