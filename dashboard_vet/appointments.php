<?php
include '../databaseConnection.php';

// Create appointments table if it doesn't exist
$tableCreationQuery = "CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id)
);";
mysqli_query($conn, $tableCreationQuery);

// Fetch upcoming appointments
$query = "SELECT a.id, a.date, a.time, a.reason, an.name AS animal_name, an.species, o.owner_name
          FROM appointments a
          JOIN animals an ON a.animal_id = an.id
          JOIN owners o ON an.owner_id = o.id
          WHERE a.date >= CURDATE()
          ORDER BY a.date, a.time";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../pictures/logo/Log.jpg" type="image/jpeg">
    <meta charset="UTF-8">
    <title>Appointments</title>
    <link rel="stylesheet" href="dashboard_vet.css">
    <link rel="stylesheet" href="sidebar_vet.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">    
</head>
<body>
    <?php include 'sidebar_vet.php'; ?>
    <div class="container">
        <h1>Upcoming Appointments</h1>
        <div class="section">
            <div class="section-list">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="list-item">
                            <div class="item-title">
                                <?php echo htmlspecialchars($row['animal_name']); ?> (<?php echo htmlspecialchars($row['species']); ?>)
                            </div>
                            <div class="item-subtitle">
                                Owner: <?php echo htmlspecialchars($row['owner_name']); ?>
                            </div>
                            <div class="item-date">
                                <?php echo htmlspecialchars($row['date']); ?> at <?php echo htmlspecialchars($row['time']); ?>
                            </div>
                            <div class="item-subtitle">
                                Reason: <?php echo htmlspecialchars($row['reason']); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No upcoming appointments found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
