<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/*
  Get enrolled courses with progress
*/
$sql = "
SELECT 
    c.id AS course_id,
    c.title AS course_title,
    COUNT(m.id) AS total_modules,
    SUM(CASE WHEN um.completed = 1 THEN 1 ELSE 0 END) AS completed_modules
FROM enrollments e
JOIN course c ON c.id = e.course_id
LEFT JOIN section m ON m.course_id = c.id
LEFT JOIN user_modules um 
    ON um.module_id = m.id AND um.user_id = ?
WHERE e.user_id = ?
GROUP BY c.id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$active_courses = [];
$completed_courses = [];

while ($row = $result->fetch_assoc()) {
    $progress = ($row['total_modules'] > 0)
        ? round(($row['completed_modules'] / $row['total_modules']) * 100)
        : 0;

    $row['progress'] = $progress;

    if ($progress >= 100) {
        $completed_courses[] = $row;
    } else {
        $active_courses[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TrainUp | Enrolled</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/dash.css">

  <style>
    .enrolled-container h2 { font-size: 26px; margin-bottom: 20px; color: #00ffff; }
    .course-tabs { display: flex; gap: 15px; margin-bottom: 25px; }
    .course-tabs .tab { background: transparent; color: #ccc; border: none; font-size: 16px; cursor: pointer; padding: 8px 15px; transition: 0.3s; }
    .course-tabs .tab.active { color: #00ffff; border-bottom: 2px solid #00ffff; }
    .courses-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 60px; }
    .course-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(0, 255, 255, 0.3); border-radius: 10px; padding: 20px; position: relative; transition: 0.3s; box-shadow: 0 0 15px rgba(0, 255, 255, 0.1); width: 300px; height: auto; }
    .course-card:hover { box-shadow: 0 0 25px rgba(0, 255, 255, 0.3); transform: translateY(-5px); }
    .course-card h3 { font-size: 18px; margin-bottom: 15px; color: #fff; text-transform: capitalize; }
    .progress-bar { background: rgba(255, 255, 255, 0.1); border-radius: 10px; height: 10px; overflow: hidden; margin-bottom: 10px; }
    .progress { height: 100%; background: linear-gradient(90deg, #00ffff, #0066ff); border-radius: 10px; transition: width 0.4s ease; }
    .percentage { font-size: 14px; color: #00ffff; display: block; text-align: right; margin-bottom: 8px; }
    .course-card.completed { opacity: 0.8; border-color: rgba(255, 255, 255, 0.2); }
    .course-card.completed h3 { color: #00bcd4; }
  </style>
</head>

<body>
<header>
  <nav>
    <ul>
      <li><img src="img/logo.png" alt=""></li>
      <li><a href="dashboard.php">Home</a></li>
      <li><a href="courses.php">Courses</a></li>
      <li><a href="events.php">Events</a></li>
      <li><a href="my_courses.php">My Courses</a></li>
      <li><a href="#"><img src="img/Search.png" class="bar"></a></li>
      <li>|</li>
      <li><a href="panier.php"><img src="img/Group 68.png" class="bar"></a></li>
      <li><a href="#"><img src="img/Doorbell.png" class="bar"></a></li>
      <li><a href="profile.php"><img src="img/User.png" class="bar"></a></li>
    </ul>
  </nav>
</header>

<main class="dashboard-container">
  <div class="sidebar">
    <a href="courses.php" class="sidebar-link">Courses</a>
    <a href="dashboard.php" class="sidebar-link">Dashboard</a>
    <a href="enrolled-courses.php" class="sidebar-link active">Enrolled Courses</a>
    <a href="events.php" class="sidebar-link">Events</a>
    <a href="my_courses.php" class="sidebar-link">My Courses</a>
    <a href="settings.php" class="sidebar-link">Settings</a>
  </div>

  <section class="dashboard-content">
    <div class="enrolled-container">
      <h2>Enrolled Courses</h2>

      <div class="course-tabs">
        <button class="tab active" data-type="active">Active course</button>
        <button class="tab" data-type="completed">Completed course</button>
      </div>

      <div class="courses-grid">
        <!-- Active courses -->
        <?php foreach ($active_courses as $course): ?>
          <div class="course-card" data-status="active">
            <h3><?= htmlspecialchars($course['course_title']); ?></h3>
            <span class="percentage"><?= $course['progress']; ?>%</span>
            <div class="progress-bar">
              <div class="progress" style="width:<?= $course['progress']; ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>

        <!-- Completed courses -->
        <?php foreach ($completed_courses as $course): ?>
          <div class="course-card completed" data-status="completed">
            <h3><?= htmlspecialchars($course['course_title']); ?></h3>
            <span class="percentage">100%</span>
            <div class="progress-bar">
              <div class="progress" style="width:100%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<script>
const tabs = document.querySelectorAll('.tab');
const cards = document.querySelectorAll('.course-card');

function filterCourses(type) {
  cards.forEach(card => {
    card.style.display = card.dataset.status === type ? 'block' : 'none';
  });
}

// Default: show ACTIVE only
filterCourses('active');

tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    tabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    filterCourses(tab.dataset.type);
  });
});
</script>
</body>
</html>
