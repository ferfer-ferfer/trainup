<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? 0;
if(!$user_id) {
    header("Location: login.php");
    exit;
}

// Fetch all enrolled courses
$coursesQuery = $conn->query("
    SELECT c.*
    FROM course c
    INNER JOIN enrollments e ON e.course_id = c.id
    WHERE e.user_id = $user_id
");
$courses = $coursesQuery->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TrainUp | My Courses</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/dash.css">
<style>
/* Make the section flex container */
.dashboard-content {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

/* Ensure h2 takes full width */
.dashboard-content h2 {
    width: 100%;
    margin-bottom: 20px;
    color: #00ffff;
}

/* Course cards: max 2 per row */
.course-card {
    background: rgba(0, 255, 255, 0.05);
    border: 1px solid rgba(0, 255, 255, 0.3);
    border-radius: 12px;
    padding: 20px;
    width: calc(50% - 10px); /* 2 per row with 20px gap */
    height: auto;
    box-sizing: border-box;
    transition: transform 0.2s, box-shadow 0.2s;
}
.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 255, 255, 0.1);
}

/* Module list */
.module-list {
    list-style: none;
    padding-left: 0;
    margin: 0;
}
.module-list li {
    padding: 8px 10px;
    border-bottom: 1px solid rgba(0, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.module-list li:last-child {
    border-bottom: none;
}

/* Completed badge */
.completed {
    color: #0f0;
    font-weight: bold;
    font-size: 0.9rem;
}

/* Buttons */
button {
    padding: 6px 12px;
    border: none;
    background: #00ffff;
    color: #000;
    cursor: pointer;
    border-radius: 6px;
    font-weight: 500;
    transition: background 0.2s, transform 0.1s;
}
button:hover {
    background: #00e5e5;
    transform: translateY(-1px);
}

/* Responsive: single column on small screens */
@media (max-width: 700px) {
    .course-card {
        width: 100%;
    }
}
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
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="courses.php">Courses</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a class="me" href="my_courses.php">My Courses</a></li>
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
      <a href="dashboard.php" class="sidebar-link ">Dashboard</a>
      <a href="enrolled-courses.php" class="sidebar-link">Enrolled Courses</a>
      <a href="events.php" class="sidebar-link">Events</a>
      <a href="my_courses.php" class="sidebar-link active">My Courses</a>
      <a href="settings.php" class="sidebar-link">Settings</a>
    </div>
 <section class="dashboard-content">
<h2>My Courses</h2>

<?php foreach($courses as $course): ?>
<div class="course-card">
    <h3><?= htmlspecialchars($course['title']); ?></h3>

    <?php
    $modulesQuery = $conn->query("SELECT * FROM section WHERE course_id=".$course['id']." ORDER BY position ASC");
    $modules = $modulesQuery->fetch_all(MYSQLI_ASSOC);
    ?>
    
    <ul class="module-list">
    <?php foreach($modules as $module):
        $completed = $conn->query("SELECT completed FROM user_modules WHERE user_id=$user_id AND module_id=".$module['id'])->fetch_assoc()['completed'] ?? 0;
    ?>
        <li>
            <?= htmlspecialchars($module['title']); ?>
            <?php if($completed): ?>
                <span class="completed">Completed</span>
            <?php else: ?>
                <form method="POST" action="mark_complete.php">
    <input type="hidden" name="module_id" value="<?= $module['id']; ?>">
    <input type="hidden" name="course_id" value="<?= $course_id; ?>">
    <button type="submit" class="complete-btn">
        Mark as completed
    </button>
</form>

            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
<?php endforeach; ?>
</section>
</main>
</body>
</html>

