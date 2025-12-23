<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Get enrolled courses + progress
$sql = "
SELECT 
    c.id AS course_id,
    COUNT(m.id) AS total_modules,
    SUM(CASE WHEN um.completed = 1 THEN 1 ELSE 0 END) AS completed_modules
FROM enrollments e
JOIN course c ON c.id = e.course_id
LEFT JOIN section m ON m.course_id = c.id
LEFT JOIN user_modules um ON um.module_id = m.id AND um.user_id = ?
WHERE e.user_id = ?
GROUP BY c.id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$active_count = 0;
$completed_count = 0;

while ($row = $result->fetch_assoc()) {
    $progress = ($row['total_modules'] > 0) 
        ? round(($row['completed_modules'] / $row['total_modules']) * 100)
        : 0;

    if ($progress >= 100) {
        $completed_count++;
    } else {
        $active_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TrainUp | Dashboard</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/dash.css">
  <style>
    .me {
      color: #00ffff;
    }
  </style>
</head>
<body>
  <header>
    <nav>
      <ul>
        <li><img src="img/logo.png" alt=""></li>
        <li><a class="me" href="dashboard.php" >Home</a></li>
        <li><a href="courses.php">Courses</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a href="my_courses.php">My Courses</a></li>
        <li><a href="#"><img src="img/Search.png" class="bar" alt=""></a></li>
        <li>|</li>
        <li><a href="panier.php"><img src="img/Group 68.png" class="bar" alt=""></a></li>
        <li><a href="#"><img src="img/Doorbell.png" class="bar" alt=""></a></li>
        <li><a href="profile.php "><img src="img/User.png" class="bar" alt=""></a></li>
      </ul>
    </nav>
  </header>

  <main class="dashboard-container">
    <div class="sidebar">
      <a href="courses.php" class="sidebar-link">Courses</a>
      <a href="dashboard.php" class="sidebar-link active">Dashboard</a>
      <a href="enrolled-courses.php" class="sidebar-link">Enrolled Courses</a>
      <a href="events.php" class="sidebar-link">Events</a>
      <a href="my_courses.php" class="sidebar-link">My Courses</a>
      <a href="settings.php" class="sidebar-link">Settings</a>
    </div>

    <section class="dashboard-content">
      <h2>Dashboard</h2>
<div class="stats">
  <div class="stat-box">
    <div class="icon-box"><img src="img/Group 76.png" alt=""></div>
    <h3><?= $active_count ?></h3>
    <p>Active Courses</p>
  </div>
  <div class="stat-box">
    <div class="icon-box"><img src="img/Trophy.png" alt=""></div>
    <h3><?= $completed_count ?></h3>
    <p>Completed Courses</p>
  </div>
</div>

    </section>
  </main>
</body>
</html>
