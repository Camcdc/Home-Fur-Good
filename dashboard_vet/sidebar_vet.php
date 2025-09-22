<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <title>Document</title>
</head>
<body>

</body>

<nav class="sidebar">
  <div class="sidebar-header">
    <h2><i class="fa-solid fa-paw"></i> Vet Portal</h2>
  </div>
  <div class="sidebar-section">
    <ul>
      <li><a href="dashboard_vet.php"><i class="fa fa-align-justify"></i> Dashboard Home</a></li>
      <li><a href="manageMedicalReport.php"><i class="fa-solid fa-list"></i> Medical Records</a></li>

      <li><a href="allThreeVisuals.php"><i class="fa-solid fa-chart-pie"></i> View Analytics</a></li>
      <li><a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
  </div>
</nav>






<script>
  // Dropdown toggle script
  document.querySelectorAll(".dropdown-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const dropdown = button.nextElementSibling;

      document.querySelectorAll(".dropdown-container").forEach((container) => {
        if (container !== dropdown) container.classList.remove("show");
      });

      document.querySelectorAll(".dropdown-btn").forEach((btn) => {
        if (btn !== button) btn.classList.remove("active");
      });

      if (dropdown) {
        dropdown.classList.toggle("show");
        button.classList.toggle("active");
      }
    });
  });

  src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"
</script>
