<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="sidebar_admin.css">
  <title>Staff Portal</title>
</head>
<body>

<nav class="sidebar">
  <div class="sidebar-header">
    <h2><i class="fa-solid fa-paw"></i> Staff Portal</h2>
  </div>

  <div class="sidebar-section">
    <ul>
      <li>
        <a href="dashboard_admin.php">
          <i class="fa-solid fa-chart-line"></i>
          <span>Dashboard</span>
        </a>
      </li>
      
      <li>
        <button class="dropdown-btn">
          <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-paw"></i>
            <span>Animal Management</span>
          </div>
          <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
          <a href="animalRegistrationForm.php">
            <i class="fa-solid fa-plus"></i>
            <span>Register Animal</span>
          </a>
          <a href="viewAllAnimals.php">
            <i class="fa-solid fa-eye"></i>
            <span>View Animals</span>
          </a>
          <a href="kennelManagement.php">
            <i class="fa-solid fa-house"></i>
            <span>Kennel Management</span>
          </a>
        </div>

      
      <li>
        <a href="displayAllrecords.php">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span>Cruelty Reports</span>
        </a>
      </li>
      
      <li>
        <button class="dropdown-btn">
          <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-heart"></i>
            <span>Adoptions</span>
          </div>
          <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
          <a href="manageAdoptions.php">
            <i class="fa-solid fa-bars"></i>
            <span>Adoptions Management</span>
          </a>
          <a href="../dashboard_admin/postFollowUp.php">
            <i class="fa-solid fa-file-signature"></i>
            <span>Post-Follow-Up Adoption</span>
          </a>
        </div>
      </li>
      <li>
         <button class="dropdown-btn">
          <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-hand-holding-heart"></i>
            <span>Volunteer Management</span>
          </div>
          <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
          <a href="volunteer_admin.php">
            <i class="fa-solid fa-bars"></i>
            <span>Volunteer Dashboard</span>
          </a>
          <a href="#">
            <i class="fa-solid fa-eye"></i>
            <span>View Volunteers</span>
          </a>
        </div>
      </li>
      <li>
        <button class="dropdown-btn">
          <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-house-chimney-crack"></i>
            <span>Foster Management</span>
          </div>
          <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
          <a href="foster_admin.php">
            <i class="fa-solid fa-chart-line"></i>
            <span>Foster Dashboard</span>
          </a>
          <a href="manageFosters.php">
            <i class="fa-solid fa-user-check"></i>
            <span>Manage Applications</span>
          </a>
        </div>
        
      </li>

      <li>
        <button class="dropdown-btn">
          <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-sack-dollar"></i>
            <span>Donations</span>
          </div>
          <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
          <a href="dashboard_donation.php">
            <i class="fa-solid fa-chart-line"></i>
            <span>Donation Dashboard</span>
          </a>
     
          <a href="displayAllDonations.php">
            <i class="fa-solid fa-list"></i>
            <span>View Donations</span>
          </a>
        </div>
      </li>
      
      
      <li>
        <button class="dropdown-btn">
          <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-chart-bar"></i>
            <span>Analytics</span>
          </div>
          <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
          <a href="animalAnalytics.php">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Adoption Analytics</span>
          </a>
          <a href="crueltyReportAnalytics.php">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Cruelty Report Analytics</span>
          </a>
          <a href="donationAnalytics.php">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Donation Analytics</span>
          </a>
           <a href="volunteer analytics.php">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Volunteer Analytics</span>
          </a>
            <a href="foster analytics.php">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Foster Analytics</span>
          </a>
        </div>
      </li>
      
      <li>
        <a href="../logout.php">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </div>
</nav>
</body>
</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
  // Dropdown toggle functionality
document.querySelectorAll('.dropdown-btn').forEach((button) => {
  button.addEventListener('click', (e) => {
    e.preventDefault();
    const dropdown = button.nextElementSibling;

    // Close other dropdowns
    document.querySelectorAll('.dropdown-container').forEach((container) => {
      if (container !== dropdown) {
        container.classList.remove('show');
      }
    });

    // Toggle current dropdown
    if (dropdown && dropdown.classList.contains('dropdown-container')) {
      dropdown.classList.toggle('show');
    }

    // Toggle active state on button
    button.classList.toggle('active');
  });
});

// Sidebar toggle for mobile view
document.querySelector('.sidebar-toggle').addEventListener('click', function() {
  document.querySelector('.sidebar').classList.toggle('open');
});

  // Highlight current page
  document.addEventListener("DOMContentLoaded", () => {
    const currentPath = window.location.pathname;
    const currentPage = currentPath.substring(currentPath.lastIndexOf('/') + 1);
    
    document.querySelectorAll(".sidebar-section a").forEach((link) => {
      const href = link.getAttribute("href");
      if (href === currentPage) {
        link.classList.add("active");
      }
    });
  });
</script>
