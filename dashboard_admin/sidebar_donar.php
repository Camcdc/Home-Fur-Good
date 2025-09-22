<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="sidebar_admin.css">
  <title>Donor Portal</title>
</head>
<body>

<nav class="sidebar">
  <div class="sidebar-header">
    <h2><i class="fa-solid fa-hand-holding-heart"></i> Donor Portal</h2>
  </div>

  <div class="sidebar-section">
    <ul>
      <li>
        <a href="dashboard_donation.php">
          <i class="fa-solid fa-chart-line"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a href="displayAllDonations.php">
          <i class="fa-solid fa-list"></i>
          <span>View Donations</span>
        </a>
      </li>
      <li>
        <a href="Donation Analytics.php">
          <i class="fa-solid fa-chart-column"></i>
          <span>Donation Analytics</span>
        </a>
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